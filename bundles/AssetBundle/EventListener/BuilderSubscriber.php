<?php

namespace Autoborna\AssetBundle\EventListener;

use Autoborna\AssetBundle\Helper\TokenHelper;
use Autoborna\CoreBundle\Event\BuilderEvent;
use Autoborna\CoreBundle\Helper\BuilderTokenHelperFactory;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Autoborna\EmailBundle\EmailEvents;
use Autoborna\EmailBundle\Event\EmailSendEvent;
use Autoborna\LeadBundle\Tracker\ContactTracker;
use Autoborna\PageBundle\Event\PageDisplayEvent;
use Autoborna\PageBundle\PageEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BuilderSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $assetToken = '{assetlink=(.*?)}';

    /**
     * @var CorePermissions
     */
    private $security;

    /**
     * @var TokenHelper
     */
    private $tokenHelper;

    /**
     * @var ContactTracker
     */
    private $contactTracker;

    /**
     * @var BuilderTokenHelperFactory
     */
    private $builderTokenHelperFactory;

    /**
     * BuilderSubscriber constructor.
     */
    public function __construct(
        CorePermissions $security,
        TokenHelper $tokenHelper,
        ContactTracker $contactTracker,
        BuilderTokenHelperFactory $builderTokenHelperFactory
    ) {
        $this->security                  = $security;
        $this->tokenHelper               = $tokenHelper;
        $this->contactTracker            = $contactTracker;
        $this->builderTokenHelperFactory = $builderTokenHelperFactory;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EmailEvents::EMAIL_ON_BUILD   => ['onBuilderBuild', 0],
            EmailEvents::EMAIL_ON_SEND    => ['onEmailGenerate', 0],
            EmailEvents::EMAIL_ON_DISPLAY => ['onEmailGenerate', 0],
            PageEvents::PAGE_ON_BUILD     => ['onBuilderBuild', 0],
            PageEvents::PAGE_ON_DISPLAY   => ['onPageDisplay', 0],
        ];
    }

    public function onBuilderBuild(BuilderEvent $event)
    {
        if ($event->tokensRequested($this->assetToken)) {
            $tokenHelper = $this->builderTokenHelperFactory->getBuilderTokenHelper('asset');
            $event->addTokensFromHelper($tokenHelper, $this->assetToken, 'title', 'id', true);
        }
    }

    public function onEmailGenerate(EmailSendEvent $event)
    {
        $lead   = $event->getLead();
        $leadId = (int) (null !== $lead ? $lead['id'] : null);
        $email  = $event->getEmail();
        $tokens = $this->generateTokensFromContent($event, $leadId, $event->getSource(), null === $email ? null : $email->getId());
        $event->addTokens($tokens);
    }

    public function onPageDisplay(PageDisplayEvent $event)
    {
        $page    = $event->getPage();
        $lead    = $this->security->isAnonymous() ? $this->contactTracker->getContact() : null;
        $leadId  = $lead ? $lead->getId() : null;
        $tokens  = $this->generateTokensFromContent($event, $leadId, ['page', $page->getId()]);
        $content = $event->getContent();

        if (!empty($tokens)) {
            $content = str_ireplace(array_keys($tokens), $tokens, $content);
        }
        $event->setContent($content);
    }

    /**
     * @param PageDisplayEvent|EmailSendEvent $event
     * @param int                             $leadId
     * @param array                           $source
     * @param null                            $emailId
     *
     * @return array
     */
    private function generateTokensFromContent($event, $leadId, $source = [], $emailId = null)
    {
        if ($event instanceof PageDisplayEvent || ($event instanceof EmailSendEvent && $event->shouldAppendClickthrough())) {
            $clickthrough = [
                'source' => $source,
                'lead'   => $leadId ?? false,
                'email'  => $emailId ?? false,
            ];
        }

        return $this->tokenHelper->findAssetTokens($event->getContent(), array_filter($clickthrough ?? []));
    }
}
