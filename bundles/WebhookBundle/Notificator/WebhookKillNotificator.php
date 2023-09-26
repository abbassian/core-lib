<?php

namespace Autoborna\WebhookBundle\Notificator;

use Doctrine\ORM\EntityManager;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Model\NotificationModel;
use Autoborna\EmailBundle\Helper\MailHelper;
use Autoborna\UserBundle\Entity\User;
use Autoborna\WebhookBundle\Entity\Webhook;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

class WebhookKillNotificator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var NotificationModel
     */
    private $notificationModel;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MailHelper
     */
    private $mailer;

    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    public function __construct(
        TranslatorInterface $translator,
        Router $router,
        NotificationModel $notificationModel,
        EntityManager $entityManager,
        MailHelper $mailer,
        CoreParametersHelper $coreParametersHelper
    ) {
        $this->translator           = $translator;
        $this->router               = $router;
        $this->notificationModel    = $notificationModel;
        $this->entityManager        = $entityManager;
        $this->mailer               = $mailer;
        $this->coreParametersHelper = $coreParametersHelper;
    }

    /**
     * @param string $reason Translatable key
     */
    public function send(Webhook $webhook, $reason)
    {
        $subject = $this->translator->trans('autoborna.webhook.stopped');
        $reason  = $this->translator->trans($reason);
        $htmlUrl = '<a href="'.$this->router->generate(
                'autoborna_webhook_action',
                ['objectAction' => 'view', 'objectId' => $webhook->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ).'" data-toggle="ajax">'.$webhook->getName().'</a>';

        $details = $this->translator->trans(
            'autoborna.webhook.stopped.details',
            [
                '%reason%'  => $reason,
                '%webhook%' => $htmlUrl,
            ]
        );

        /** @var User $owner */
        $owner = $toUser = $this->entityManager->getReference('AutobornaUserBundle:User', $webhook->getCreatedBy());

        $ccToUser = null;

        if (null !== $webhook->getModifiedBy() && $webhook->getCreatedBy() !== $webhook->getModifiedBy()) {
            $modifiedBy = $this->entityManager->getReference('AutobornaUserBundle:User', $webhook->getModifiedBy());

            $toUser   = $modifiedBy; // Send notification to modifier
            $ccToUser = $owner; // And cc e-mail to owner
        }

        // Send notification
        $this->notificationModel->addNotification(
            $details,
            'error',
            false,
            $subject,
            null,
            null,
            $toUser
        );

        // Send e-mail
        $mailer = $this->mailer;

        $sendToAuthor = $this->coreParametersHelper->get('webhook_send_notification_to_author', 1);
        if ($sendToAuthor) {
            $mailer->setTo($toUser->getEmail());
            if ($ccToUser) {
                $mailer->setCc($ccToUser->getEmail());
            }
        } else {
            $emailAddresses = array_map('trim', explode(',', $this->coreParametersHelper->get('webhook_notification_email_addresses')));
            $mailer->setTo($emailAddresses);
        }

        $mailer->setSubject($subject);
        $mailer->setBody($details);
        $mailer->send(true);
    }
}
