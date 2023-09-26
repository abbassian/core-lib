<?php

namespace Autoborna\EmailBundle\Tests\MonitoredEmail\Organizer;

use Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor;
use Autoborna\EmailBundle\MonitoredEmail\Mailbox;
use Autoborna\EmailBundle\MonitoredEmail\Organizer\MailboxContainer;

class MailboxContainerTest extends \PHPUnit\Framework\TestCase
{
    protected $config = [
        'imap_path' => 'path',
        'user'      => 'user',
        'host'      => 'host',
        'folder'    => 'folder',
    ];

    /**
     * @testdox Container's path should be config's path for services that don't have access
     *          to the config but need to set the path
     *
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Accessor\ConfigAccessor::getPath()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Organizer\MailboxContainer::getPath()
     */
    public function testPathMatches()
    {
        $configAccessor   = new ConfigAccessor($this->config);
        $mailboxContainer = new MailboxContainer($configAccessor);

        $this->assertEquals($configAccessor->getPath(), $mailboxContainer->getPath());
    }

    /**
     * @testdox Criteria should be returned correctly
     *
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Organizer\MailboxContainer::addCriteria()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Organizer\MailboxContainer::getCriteria()
     */
    public function testCriteriaIsSetAsExpected()
    {
        $configAccessor   = new ConfigAccessor($this->config);
        $mailboxContainer = new MailboxContainer($configAccessor);

        $criteria = [
            Mailbox::CRITERIA_ALL => [
                'mailbox1',
                'mailbox2',
            ],
            Mailbox::CRITERIA_UNANSWERED => [
                'mailbox2',
            ],
        ];

        $mailboxContainer->addCriteria(Mailbox::CRITERIA_ALL, 'mailbox1');
        $mailboxContainer->addCriteria(Mailbox::CRITERIA_ALL, 'mailbox2');
        $mailboxContainer->addCriteria(Mailbox::CRITERIA_UNANSWERED, 'mailbox2');

        $this->assertEquals($criteria, $mailboxContainer->getCriteria());
    }

    /**
     * @testdox Keep as unseen flag should be correctly returned when set
     *
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Organizer\MailboxContainer::keepAsUnseen()
     * @covers \Autoborna\EmailBundle\MonitoredEmail\Organizer\MailboxContainer::shouldMarkAsSeen()
     */
    public function testUnseenFlagIsReturnedAsExpected()
    {
        $configAccessor   = new ConfigAccessor($this->config);
        $mailboxContainer = new MailboxContainer($configAccessor);

        $mailboxContainer->keepAsUnseen();

        $this->assertFalse($mailboxContainer->shouldMarkAsSeen());
    }
}
