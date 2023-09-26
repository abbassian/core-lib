<?php

namespace Autoborna\FormBundle\EventListener;

use Autoborna\FormBundle\Event\SubmissionEvent;
use Autoborna\FormBundle\Form\Type\PointActionFormSubmitType;
use Autoborna\FormBundle\FormEvents;
use Autoborna\PointBundle\Event\PointBuilderEvent;
use Autoborna\PointBundle\Model\PointModel;
use Autoborna\PointBundle\PointEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PointSubscriber implements EventSubscriberInterface
{
    /**
     * @var PointModel
     */
    private $pointModel;

    public function __construct(PointModel $pointModel)
    {
        $this->pointModel = $pointModel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PointEvents::POINT_ON_BUILD => ['onPointBuild', 0],
            FormEvents::FORM_ON_SUBMIT  => ['onFormSubmit', 0],
        ];
    }

    public function onPointBuild(PointBuilderEvent $event)
    {
        $action = [
            'group'       => 'autoborna.form.point.action',
            'label'       => 'autoborna.form.point.action.submit',
            'description' => 'autoborna.form.point.action.submit_descr',
            'callback'    => ['\\Autoborna\\FormBundle\\Helper\\PointActionHelper', 'validateFormSubmit'],
            'formType'    => PointActionFormSubmitType::class,
        ];

        $event->addAction('form.submit', $action);
    }

    /**
     * Trigger point actions for form submit.
     */
    public function onFormSubmit(SubmissionEvent $event)
    {
        $this->pointModel->triggerAction('form.submit', $event->getSubmission());
    }
}
