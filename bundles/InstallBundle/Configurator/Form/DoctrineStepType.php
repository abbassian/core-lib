<?php

namespace Autoborna\InstallBundle\Configurator\Form;

use Autoborna\CoreBundle\Form\Type\FormButtonsType;
use Autoborna\CoreBundle\Form\Type\YesNoButtonGroupType;
use Autoborna\InstallBundle\Configurator\Step\DoctrineStep;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;

/**
 * Doctrine Form Type.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @note   This class is based on Sensio\Bundle\DistributionBundle\Configurator\Form\DoctrineStepType
 */
class DoctrineStepType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'driver',
            ChoiceType::class,
            [
                'choices'           => array_flip(DoctrineStep::getDrivers()),
                'expanded'          => false,
                'multiple'          => false,
                'label'             => 'autoborna.install.form.database.driver',
                'label_attr'        => ['class' => 'control-label'],
                'placeholder'       => false,
                'required'          => true,
                'attr'              => [
                    'class' => 'form-control',
                ],
                'constraints'       => [
                    new Choice(
                        [
                            'callback' => '\Autoborna\InstallBundle\Configurator\Step\DoctrineStep::getDriverKeys',
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'host',
            TextType::class,
            [
                'label'      => 'autoborna.install.form.database.host',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => true,
            ]
        );

        $builder->add(
            'port',
          TextType::class,
            [
                'label'      => 'autoborna.install.form.database.port',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => false,
            ]
        );

        $builder->add(
            'name',
          TextType::class,
            [
                'label'      => 'autoborna.install.form.database.name',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => true,
            ]
        );

        $builder->add(
            'table_prefix',
          TextType::class,
            [
                'label'      => 'autoborna.install.form.database.table.prefix',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => false,
            ]
        );

        $builder->add(
            'user',
          TextType::class,
            [
                'label'      => 'autoborna.install.form.database.user',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => true,
            ]
        );

        $builder->add(
            'password',
             PasswordType::class,
            [
                'label'      => 'autoborna.install.form.database.password',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                    'preaddon' => 'fa fa-lock',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'backup_tables',
            YesNoButtonGroupType::class,
            [
                'label' => 'autoborna.install.form.existing_tables',
                'attr'  => [
                    'tooltip'  => 'autoborna.install.form.existing_tables_descr',
                    'onchange' => 'AutobornaInstaller.toggleBackupPrefix();',
                ],
            ]
        );

        $builder->add(
            'backup_prefix',
            TextType::class,
            [
                'label'      => 'autoborna.install.form.backup_prefix',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'buttons',
            FormButtonsType::class,
            [
                'pre_extra_buttons' => [
                    [
                        'name'  => 'next',
                        'label' => 'autoborna.install.next.step',
                        'type'  => 'submit',
                        'attr'  => [
                            'class'   => 'btn btn-success pull-right btn-next',
                            'icon'    => 'fa fa-arrow-circle-right',
                            'onclick' => 'AutobornaInstaller.showWaitMessage(event);',
                        ],
                    ],
                ],
                'apply_text'  => '',
                'save_text'   => '',
                'cancel_text' => '',
            ]
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'install_doctrine_step';
    }
}
