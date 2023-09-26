<?php

namespace Autoborna\PluginBundle\EventListener;

use Autoborna\FormBundle\Event\FormBuilderEvent;
use Autoborna\FormBundle\Event\SubmissionEvent;
use Autoborna\FormBundle\FormEvents;
use Autoborna\PluginBundle\Form\Type\IntegrationsListType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormSubscriber implements EventSubscriberInterface
{
    use PushToIntegrationTrait;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD            => ['onFormBuild', 0],
            FormEvents::ON_EXECUTE_SUBMIT_ACTION => ['onFormSubmitActionTriggered', 0],
        ];
    }

    public function onFormBuild(FormBuilderEvent $event)
    {
        $event->addSubmitAction('plugin.leadpush', [
            'group'       => 'autoborna.plugin.actions',
            'description' => 'autoborna.plugin.actions.tooltip',
            'label'       => 'autoborna.plugin.actions.push_lead',
            'formType'    => IntegrationsListType::class,
            'formTheme'   => 'AutobornaPluginBundle:FormTheme\Integration',
            'eventName'   => FormEvents::ON_EXECUTE_SUBMIT_ACTION,
        ]);
    }

    /**
     * @return mixed
     */
    public function onFormSubmitActionTriggered(SubmissionEvent $event): void
    {
        if (false === $event->checkContext('plugin.leadpush')) {
            return;
        }

        $this->pushToIntegration($event->getActionConfig(), $event->getSubmission()->getLead());
    }
}
