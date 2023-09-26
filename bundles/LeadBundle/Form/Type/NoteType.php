<?php

namespace Autoborna\LeadBundle\Form\Type;

use Autoborna\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Autoborna\CoreBundle\Form\EventListener\FormExitSubscriber;
use Autoborna\CoreBundle\Form\Type\FormButtonsType;
use Autoborna\CoreBundle\Helper\DateTimeHelper;
use Autoborna\LeadBundle\Entity\LeadNote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    /**
     * @var DateTimeHelper
     */
    private $dateHelper;

    public function __construct()
    {
        $this->dateHelper = new DateTimeHelper();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new CleanFormSubscriber(['text' => 'html']));
        $builder->addEventSubscriber(new FormExitSubscriber('lead.note', $options));

        $builder->add(
            'text',
            TextareaType::class,
            [
                'label'      => 'autoborna.lead.note.form.text',
                'label_attr' => ['class' => 'control-label sr-only'],
                'attr'       => ['class' => 'mousetrap form-control editor', 'rows' => 10, 'autofocus' => 'autofocus'],
            ]
        );

        $builder->add(
            'type',
            ChoiceType::class,
            [
                'label'             => 'autoborna.lead.note.form.type',
                'choices'           => [
                    'autoborna.lead.note.type.general' => 'general',
                    'autoborna.lead.note.type.email'   => 'email',
                    'autoborna.lead.note.type.call'    => 'call',
                    'autoborna.lead.note.type.meeting' => 'meeting',
                ],
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
            ]
        );

        $dt   = $options['data']->getDatetime();
        $data = (null == $dt) ? $this->dateHelper->getDateTime() : $dt;

        $builder->add(
            'dateTime',
            DateTimeType::class,
            [
                'label'      => 'autoborna.core.date.added',
                'label_attr' => ['class' => 'control-label'],
                'widget'     => 'single_text',
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'datetime',
                    'preaddon'    => 'fa fa-calendar',
                ],
                'format' => 'yyyy-MM-dd HH:mm',
                'data'   => $data,
            ]
        );

        $builder->add('buttons', FormButtonsType::class, [
            'apply_text' => false,
            'save_text'  => 'autoborna.core.form.save',
        ]);

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LeadNote::class,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'leadnote';
    }
}
