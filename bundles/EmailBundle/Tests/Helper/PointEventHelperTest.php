<?php

namespace Autoborna\EmailBundle\Tests\Helper;

use Autoborna\CoreBundle\Factory\AutobornaFactory;
use Autoborna\EmailBundle\Entity\Email;
use Autoborna\EmailBundle\Helper\PointEventHelper;
use Autoborna\EmailBundle\Model\EmailModel;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Model\LeadModel;

class PointEventHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testSendEmail()
    {
        $helper = new PointEventHelper();
        $lead   = new Lead();
        $lead->setFields([
            'core' => [
                'email' => [
                    'value' => 'test@test.com',
                ],
            ],
        ]);
        $event = [
            'id'         => 1,
            'properties' => [
                'email' => 1,
            ],
        ];

        $result = $helper->sendEmail($event, $lead, $this->getMockAutobornaFactory());
        $this->assertEquals(true, $result);

        $result = $helper->sendEmail($event, $lead, $this->getMockAutobornaFactory(false));
        $this->assertEquals(false, $result);

        $result = $helper->sendEmail($event, $lead, $this->getMockAutobornaFactory(true, false));
        $this->assertEquals(false, $result);

        $result = $helper->sendEmail($event, new Lead(), $this->getMockAutobornaFactory(true, false));
        $this->assertEquals(false, $result);
    }

    /**
     * @param bool $published
     * @param bool $success
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockAutobornaFactory($published = true, $success = true)
    {
        $mock = $this->getMockBuilder(AutobornaFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getModel'])
            ->getMock()
        ;

        $mock->expects($this->any())
            ->method('getModel')
            ->willReturnCallback(function ($model) use ($published, $success) {
                switch ($model) {
                    case 'email':
                        return $this->getMockEmail($published, $success);
                    case 'lead':
                        return $this->getMockLead();
                }
            });

        return $mock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockLead()
    {
        $mock = $this->getMockBuilder(LeadModel::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        return $mock;
    }

    /**
     * @param bool $published
     * @param bool $success
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockEmail($published = true, $success = true)
    {
        $sendEmail = $success ? true : ['error' => 1];

        $mock = $this->getMockBuilder(EmailModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEntity', 'sendEmail'])
            ->getMock()
        ;

        $mock->expects($this->any())
            ->method('getEntity')
            ->willReturnCallback(function ($id) use ($published) {
                $email = new Email();
                $email->setIsPublished($published);

                return $email;
            });

        $mock->expects($this->any())
            ->method('sendEmail')
            ->willReturn($sendEmail);

        return $mock;
    }
}
