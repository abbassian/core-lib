<?php

namespace Autoborna\EmailBundle\MonitoredEmail\Organizer;

use Autoborna\EmailBundle\Event\ParseEmailEvent;
use Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor;
use Autoborna\EmailBundle\MonitoredEmail\Mailbox;

class MailboxOrganizer
{
    /**
     * @var ParseEmailEvent
     */
    protected $event;

    /**
     * @var array
     */
    protected $mailboxes;

    /**
     * @var MailboxContainer[]
     */
    protected $containers = [];

    /**
     * MailboxOrganizer constructor.
     */
    public function __construct(ParseEmailEvent $event, array $mailboxes)
    {
        $this->event     = $event;
        $this->mailboxes = $mailboxes;
    }

    /**
     * Organize the mailboxes into containers by IMAP connection and criteria.
     */
    public function organize()
    {
        $criteriaRequested      = $this->event->getCriteriaRequests();
        $markAsSeenInstructions = $this->event->getMarkAsSeenInstructions();

        /**
         * @var string
         * @var ConfigAccessor $config
         */
        foreach ($this->mailboxes as $name => $config) {
            // Switch mailbox to get information
            if (!$config->isConfigured()) {
                // Not configured so continue
                continue;
            }

            $criteria   = isset($criteriaRequested[$name]) ? $criteriaRequested[$name] : Mailbox::CRITERIA_UNSEEN;
            $markAsSeen = isset($markAsSeenInstructions[$name]) ? $markAsSeenInstructions[$name] : true;

            $container = $this->getContainer($config);
            if (!$markAsSeen) {
                // Keep all the messages fetched from this folder as unseen
                $container->keepAsUnseen();
            }

            $container->addCriteria($criteria, $name);
        }
    }

    /**
     * @return MailboxContainer[]
     */
    public function getContainers()
    {
        return $this->containers;
    }

    /**
     * @return MailboxContainer
     */
    protected function getContainer(ConfigAccessor $config)
    {
        $key = $config->getKey();
        if (!isset($this->containers[$key])) {
            $this->containers[$key] = new MailboxContainer($config);
        }

        return $this->containers[$key];
    }
}
