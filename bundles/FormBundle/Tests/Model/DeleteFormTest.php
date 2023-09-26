<?php

declare(strict_types=1);

namespace Autoborna\FormBundle\Tests\Model;

use Doctrine\ORM\EntityManager;
use Autoborna\CoreBundle\Doctrine\Helper\ColumnSchemaHelper;
use Autoborna\CoreBundle\Doctrine\Helper\TableSchemaHelper;
use Autoborna\CoreBundle\Helper\TemplatingHelper;
use Autoborna\CoreBundle\Helper\ThemeHelperInterface;
use Autoborna\FormBundle\Entity\Form;
use Autoborna\FormBundle\Entity\FormRepository;
use Autoborna\FormBundle\Helper\FormFieldHelper;
use Autoborna\FormBundle\Helper\FormUploader;
use Autoborna\FormBundle\Model\ActionModel;
use Autoborna\FormBundle\Model\FieldModel;
use Autoborna\FormBundle\Model\FormModel;
use Autoborna\FormBundle\Tests\FormTestAbstract;
use Autoborna\LeadBundle\Model\FieldModel as LeadFieldModel;
use Autoborna\LeadBundle\Tracker\ContactTracker;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;

class DeleteFormTest extends FormTestAbstract
{
    public function testDelete(): void
    {
        $requestStack         = $this->createMock(RequestStack::class);
        $templatingHelperMock = $this->createMock(TemplatingHelper::class);
        $themeHelper          = $this->createMock(ThemeHelperInterface::class);
        $formActionModel      = $this->createMock(ActionModel::class);
        $formFieldModel       = $this->createMock(FieldModel::class);
        $fieldHelper          = $this->createMock(FormFieldHelper::class);
        $leadFieldModel       = $this->createMock(LeadFieldModel::class);
        $formUploaderMock     = $this->createMock(FormUploader::class);
        $contactTracker       = $this->createMock(ContactTracker::class);
        $columnSchemaHelper   = $this->createMock(ColumnSchemaHelper::class);
        $tableSchemaHelper    = $this->createMock(TableSchemaHelper::class);
        $entityManager        = $this->createMock(EntityManager::class);
        $dispatcher           = $this->createMock(EventDispatcher::class);
        $formRepository       = $this->createMock(FormRepository::class);
        $form                 = $this->createMock(Form::class);
        $formModel            = new FormModel(
            $requestStack,
            $templatingHelperMock,
            $themeHelper,
            $formActionModel,
            $formFieldModel,
            $fieldHelper,
            $leadFieldModel,
            $formUploaderMock,
            $contactTracker,
            $columnSchemaHelper,
            $tableSchemaHelper
        );

        $dispatcher->expects($this->exactly(2))
            ->method('hasListeners')
            ->withConsecutive(['autoborna.form_pre_delete'], ['autoborna.form_post_delete'])
            ->willReturn(false);

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($formRepository);

        $formModel->setDispatcher($dispatcher);
        $formModel->setEntityManager($entityManager);

        $form->expects($this->exactly(2))
            ->method('getId')
            ->with()
            ->willReturn(1);

        $formUploaderMock->expects($this->once())
            ->method('deleteFilesOfForm')
            ->with($form);

        $formRepository->expects($this->once())
            ->method('deleteEntity')
            ->with($form);

        $formModel->deleteEntity($form);

        $this->assertSame(1, $form->deletedId);
    }
}
