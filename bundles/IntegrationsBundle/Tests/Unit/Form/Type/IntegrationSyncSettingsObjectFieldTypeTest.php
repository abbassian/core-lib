<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Tests\Unit\Form\Type;

use Autoborna\IntegrationsBundle\Exception\InvalidFormOptionException;
use Autoborna\IntegrationsBundle\Form\Type\IntegrationSyncSettingsObjectFieldType;
use Autoborna\IntegrationsBundle\Mapping\MappedFieldInfoInterface;
use Autoborna\IntegrationsBundle\Sync\DAO\Mapping\ObjectMappingDAO;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class IntegrationSyncSettingsObjectFieldTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockObject|FormBuilderInterface
     */
    private $formBuilder;

    /**
     * @var IntegrationSyncSettingsObjectFieldType
     */
    private $form;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formBuilder = $this->createMock(FormBuilderInterface::class);
        $this->form        = new IntegrationSyncSettingsObjectFieldType();
    }

    public function testBuildFormForWrongField(): void
    {
        $options = ['field' => 'unicorn'];
        $this->expectException(InvalidFormOptionException::class);
        $this->form->buildForm($this->formBuilder, $options);
    }

    public function testBuildFormForMappedField(): void
    {
        $field   = $this->createMock(MappedFieldInfoInterface::class);
        $options = [
            'field'        => $field,
            'placeholder'  => 'Placeholder ABC',
            'object'       => 'Object A',
            'integration'  => 'Integration A',
            'autobornaFields' => [
                'autoborna_field_a' => 'Autoborna Field A',
                'autoborna_field_b' => 'Autoborna Field B',
            ],
        ];

        $field->method('showAsRequired')->willReturn(true);
        $field->method('getName')->willReturn('Integration Field A');
        $field->method('isBidirectionalSyncEnabled')->willReturn(false);
        $field->method('isToIntegrationSyncEnabled')->willReturn(true);
        $field->method('isToAutobornaSyncEnabled')->willReturn(true);

        $this->formBuilder->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [
                    'mappedField',
                    ChoiceType::class,
                    [
                        'label'          => false,
                        'choices'        => [
                            'Autoborna Field A' => 'autoborna_field_a',
                            'Autoborna Field B' => 'autoborna_field_b',
                        ],
                        'required'       => true,
                        'placeholder'    => '',
                        'error_bubbling' => false,
                        'attr'           => [
                            'class'            => 'form-control integration-mapped-field',
                            'data-placeholder' => $options['placeholder'],
                            'data-object'      => $options['object'],
                            'data-integration' => $options['integration'],
                            'data-field'       => 'Integration Field A',
                        ],
                    ],
                ],
                [
                    'syncDirection',
                    ChoiceType::class,
                    [
                        'choices' => [
                            'autoborna.integration.sync_direction_integration' => ObjectMappingDAO::SYNC_TO_INTEGRATION,
                            'autoborna.integration.sync_direction_autoborna'      => ObjectMappingDAO::SYNC_TO_MAUTIC,
                        ],
                        'label'      => false,
                        'empty_data' => ObjectMappingDAO::SYNC_TO_INTEGRATION,
                        'attr'       => [
                            'class'            => 'integration-sync-direction',
                            'data-object'      => 'Object A',
                            'data-integration' => 'Integration A',
                            'data-field'       => 'Integration Field A',
                        ],
                    ],
                ]
            );

        $this->form->buildForm($this->formBuilder, $options);
    }
}
