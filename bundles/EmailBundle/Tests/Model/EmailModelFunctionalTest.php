<?php

declare(strict_types=1);

namespace Autoborna\EmailBundle\Tests\Model;

use Autoborna\CoreBundle\Test\AutobornaMysqlTestCase;
use Autoborna\EmailBundle\Entity\Email;
use Autoborna\EmailBundle\Model\EmailModel;
use Autoborna\LeadBundle\Entity\LeadList;

class EmailModelFunctionalTest extends AutobornaMysqlTestCase
{
    public function testNotOverwriteChildrenTranslationEmailAfterSaveParent(): void
    {
        $segment        = new LeadList();
        $segmentName    = 'Test_segment';
        $segment->setName($segmentName);
        $segment->setPublicName($segmentName);
        $segment->setAlias($segmentName);
        $this->em->persist($segment);

        $emailName        = 'Test';
        $customHtmlParent = 'test EN';
        $parentEmail      = new Email();
        $parentEmail->setName($emailName);
        $parentEmail->setSubject($emailName);
        $parentEmail->setCustomHTML($customHtmlParent);
        $parentEmail->setEmailType('template');
        $parentEmail->setLanguage('en');
        $this->em->persist($parentEmail);

        $customHtmlChildren = 'test FR';
        $childrenEmail      = clone $parentEmail;
        $childrenEmail->setLanguage('fr');
        $childrenEmail->setCustomHTML($customHtmlChildren);
        $childrenEmail->setTranslationParent($parentEmail);
        $this->em->persist($parentEmail);

        $this->em->clear();

        /** @var EmailModel $emailModel */
        $emailModel = self::$container->get('autoborna.email.model.email');
        $parentEmail->setName('Test change');
        $emailModel->saveEntity($parentEmail);

        self::assertSame($customHtmlParent, $parentEmail->getCustomHtml());
        self::assertSame($customHtmlChildren, $childrenEmail->getCustomHtml());
    }
}
