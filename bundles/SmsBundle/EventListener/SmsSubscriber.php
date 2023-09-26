<?php

namespace Autoborna\SmsBundle\EventListener;

use Autoborna\AssetBundle\Helper\TokenHelper as AssetTokenHelper;
use Autoborna\CoreBundle\Event\TokenReplacementEvent;
use Autoborna\CoreBundle\Model\AuditLogModel;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Helper\TokenHelper;
use Autoborna\PageBundle\Entity\Trackable;
use Autoborna\PageBundle\Helper\TokenHelper as PageTokenHelper;
use Autoborna\PageBundle\Model\TrackableModel;
use Autoborna\SmsBundle\Event\SmsEvent;
use Autoborna\SmsBundle\Helper\SmsHelper;
use Autoborna\SmsBundle\SmsEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SmsSubscriber implements EventSubscriberInterface
{
    /**
     * @var AuditLogModel
     */
    private $auditLogModel;

    /**
     * @var TrackableModel
     */
    private $trackableModel;

    /**
     * @var PageTokenHelper
     */
    private $pageTokenHelper;

    /**
     * @var AssetTokenHelper
     */
    private $assetTokenHelper;

    /**
     * @var SmsHelper
     */
    private $smsHelper;

    public function __construct(
        AuditLogModel $auditLogModel,
        TrackableModel $trackableModel,
        PageTokenHelper $pageTokenHelper,
        AssetTokenHelper $assetTokenHelper,
        SmsHelper $smsHelper
    ) {
        $this->auditLogModel    = $auditLogModel;
        $this->trackableModel   = $trackableModel;
        $this->pageTokenHelper  = $pageTokenHelper;
        $this->assetTokenHelper = $assetTokenHelper;
        $this->smsHelper        = $smsHelper;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SmsEvents::SMS_POST_SAVE     => ['onPostSave', 0],
            SmsEvents::SMS_POST_DELETE   => ['onDelete', 0],
            SmsEvents::TOKEN_REPLACEMENT => ['onTokenReplacement', 0],
        ];
    }

    /**
     * Add an entry to the audit log.
     */
    public function onPostSave(SmsEvent $event)
    {
        $entity = $event->getSms();
        if ($details = $event->getChanges()) {
            $log = [
                'bundle'   => 'sms',
                'object'   => 'sms',
                'objectId' => $entity->getId(),
                'action'   => ($event->isNew()) ? 'create' : 'update',
                'details'  => $details,
            ];
            $this->auditLogModel->writeToLog($log);
        }
    }

    /**
     * Add a delete entry to the audit log.
     */
    public function onDelete(SmsEvent $event)
    {
        $entity = $event->getSms();
        $log    = [
            'bundle'   => 'sms',
            'object'   => 'sms',
            'objectId' => $entity->deletedId,
            'action'   => 'delete',
            'details'  => ['name' => $entity->getName()],
        ];
        $this->auditLogModel->writeToLog($log);
    }

    public function onTokenReplacement(TokenReplacementEvent $event)
    {
        /** @var Lead $lead */
        $lead         = $event->getLead();
        $content      = $event->getContent();
        $clickthrough = $event->getClickthrough();

        if ($content) {
            $tokens = array_merge(
                TokenHelper::findLeadTokens($content, $lead->getProfileFields()),
                $this->pageTokenHelper->findPageTokens($content, $clickthrough),
                $this->assetTokenHelper->findAssetTokens($content, $clickthrough)
            );

            // Disable trackable urls
            if (!$this->smsHelper->getDisableTrackableUrls()) {
                list($content, $trackables) = $this->trackableModel->parseContentForTrackables(
                    $content,
                    $tokens,
                    'sms',
                    $clickthrough['channel'][1]
                );

                /**
                 * @var string
                 * @var Trackable $trackable
                 */
                foreach ($trackables as $token => $trackable) {
                    $tokens[$token] = $this->trackableModel->generateTrackableUrl($trackable, $clickthrough, true);
                }
            }

            $content = str_replace(array_keys($tokens), array_values($tokens), $content);

            $event->setContent($content);
        }
    }
}