<?php

namespace Autoborna\CoreBundle\Model;

use Debril\RssAtomBundle\Protocol\Parser\Item;
use Autoborna\CoreBundle\Entity\Notification;
use Autoborna\CoreBundle\Entity\NotificationRepository;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Helper\EmojiHelper;
use Autoborna\CoreBundle\Helper\InputHelper;
use Autoborna\CoreBundle\Helper\PathsHelper;
use Autoborna\CoreBundle\Helper\UpdateHelper;
use Autoborna\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;

class NotificationModel extends FormModel
{
    /**
     * @var bool
     */
    protected $disableUpdates;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PathsHelper
     */
    protected $pathsHelper;

    /**
     * @var UpdateHelper
     */
    protected $updateHelper;

    /**
     * @var CoreParametersHelper
     */
    protected $coreParametersHelper;

    public function __construct(
        PathsHelper $pathsHelper,
        UpdateHelper $updateHelper,
        CoreParametersHelper $coreParametersHelper
    ) {
        $this->pathsHelper          = $pathsHelper;
        $this->updateHelper         = $updateHelper;
        $this->coreParametersHelper = $coreParametersHelper;
    }

    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param bool $disableUpdates
     */
    public function setDisableUpdates($disableUpdates)
    {
        $this->disableUpdates = $disableUpdates;
    }

    /**
     * @return NotificationRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository(Notification::class);
    }

    /**
     * Write a notification.
     *
     * @param string    $message   Message of the notification
     * @param string    $type      Optional $type to ID the source of the notification
     * @param bool|true $isRead    Add unread indicator
     * @param string    $header    Header for message
     * @param string    $iconClass Font Awesome CSS class for the icon (e.g. fa-eye)
     * @param \DateTime $datetime  Date the item was created
     * @param User|null $user      User object; defaults to current user
     */
    public function addNotification(
        $message,
        $type = null,
        $isRead = false,
        $header = null,
        $iconClass = null,
        \DateTime $datetime = null,
        User $user = null
    ) {
        if (null === $user) {
            $user = $this->userHelper->getUser();
        }

        if (null === $user || !$user->getId()) {
            //ensure notifications aren't written for non users
            return;
        }

        $notification = new Notification();
        $notification->setType($type);
        $notification->setIsRead($isRead);
        $notification->setHeader(EmojiHelper::toHtml(InputHelper::strict_html($header)));
        $notification->setMessage(EmojiHelper::toHtml(InputHelper::strict_html($message)));
        $notification->setIconClass($iconClass);
        $notification->setUser($user);
        if (null == $datetime) {
            $datetime = new \DateTime();
        }
        $notification->setDateAdded($datetime);
        $this->saveAndDetachEntity($notification);
    }

    /**
     * Mark notifications read for a user.
     */
    public function markAllRead()
    {
        $this->getRepository()->markAllReadForUser($this->userHelper->getUser()->getId());
    }

    /**
     * Clears a notification for a user.
     *
     * @param $id       Notification to clear; will clear all if empty
     * @param $limit    Maximum number of notifications to clear if $id is empty
     */
    public function clearNotification($id, $limit = null)
    {
        $this->getRepository()->clearNotificationsForUser($this->userHelper->getUser()->getId(), $id, $limit);
    }

    /**
     * Get content for notifications.
     *
     * @param null $afterId
     * @param bool $includeRead
     * @param int  $limit
     *
     * @return array
     */
    public function getNotificationContent($afterId = null, $includeRead = false, $limit = null)
    {
        if ($this->userHelper->getUser()->isGuest()) {
            return [[], false, ''];
        }

        $showNewIndicator = false;
        $userId           = ($this->userHelper->getUser()) ? $this->userHelper->getUser()->getId() : 0;

        $notifications = $this->getRepository()->getNotifications($userId, $afterId, $includeRead, null, $limit);

        //determine if the new message indicator should be shown
        foreach ($notifications as $n) {
            if (!$n['isRead']) {
                $showNewIndicator = true;
                break;
            }
        }

        // Check for updates
        $updateMessage = '';
        $newUpdate     = false;

        if (!$this->disableUpdates && $this->userHelper->getUser()->isAdmin()) {
            $updateData = [];
            $cacheFile  = $this->pathsHelper->getSystemPath('cache').'/lastUpdateCheck.txt';

            //check to see when we last checked for an update
            $lastChecked = $this->session->get('autoborna.update.checked', 0);

            if (time() - $lastChecked > 3600) {
                $this->session->set('autoborna.update.checked', time());

                $updateData = $this->updateHelper->fetchData();
            } elseif (file_exists($cacheFile)) {
                $updateData = json_decode(file_get_contents($cacheFile), true);
            }

            // If the version key is set, we have an update
            if (isset($updateData['version'])) {
                $announcement = $this->translator->trans(
                    'autoborna.core.updater.update.announcement_link',
                    ['%announcement%' => $updateData['announcement']]
                );

                $updateMessage = $this->translator->trans(
                    $updateData['message'],
                    ['%version%' => $updateData['version'], '%announcement%' => $announcement]
                );

                $alreadyNotified = $this->session->get('autoborna.update.notified');

                if (empty($alreadyNotified) || $alreadyNotified != $updateData['version']) {
                    $newUpdate = true;
                    $this->session->set('autoborna.update.notified', $updateData['version']);
                }
            }
        }

        return [$notifications, $showNewIndicator, ['isNew' => $newUpdate, 'message' => $updateMessage]];
    }
}
