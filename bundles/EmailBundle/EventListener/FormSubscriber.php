<?php

namespace Autoborna\EmailBundle\EventListener;

use Doctrine\ORM\ORMException;
use Autoborna\EmailBundle\Form\Type\EmailSendType;
use Autoborna\EmailBundle\Form\Type\FormSubmitActionUserEmailType;
use Autoborna\EmailBundle\Model\EmailModel;
use Autoborna\FormBundle\Event\FormBuilderEvent;
use Autoborna\FormBundle\Event\SubmissionEvent;
use Autoborna\FormBundle\FormEvents;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Tracker\ContactTracker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormSubscriber implements EventSubscriberInterface
{
    /**
     * @var EmailModel
     */
    private $emailModel;

    /**
     * @var ContactTracker
     */
    private $contactTracker;

    public function __construct(
        EmailModel $emailModel,
        ContactTracker $contactTracker
    ) {
        $this->emailModel     = $emailModel;
        $this->contactTracker = $contactTracker;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD            => ['onFormBuilder', 0],
            FormEvents::ON_EXECUTE_SUBMIT_ACTION => [
                ['onFormSubmitActionSendEmail', 0],
            ],
        ];
    }

    /**
     * Add a send email actions to available form submit actions.
     */
    public function onFormBuilder(FormBuilderEvent $event)
    {
        $event->addSubmitAction('email.send.user', [
            'group'             => 'autoborna.email.actions',
            'label'             => 'autoborna.email.form.action.sendemail.admin',
            'description'       => 'autoborna.email.form.action.sendemail.admin.descr',
            'formType'          => FormSubmitActionUserEmailType::class,
            'formTheme'         => 'AutobornaEmailBundle:FormTheme\EmailSendList',
            'eventName'         => FormEvents::ON_EXECUTE_SUBMIT_ACTION,
            'allowCampaignForm' => true,
        ]);

        $event->addSubmitAction('email.send.lead', [
            'group'           => 'autoborna.email.actions',
            'label'           => 'autoborna.email.form.action.sendemail.lead',
            'description'     => 'autoborna.email.form.action.sendemail.lead.descr',
            'formType'        => EmailSendType::class,
            'formTypeOptions' => ['update_select' => 'formaction_properties_email'],
            'formTheme'       => 'AutobornaEmailBundle:FormTheme\EmailSendList',
            'eventName'       => FormEvents::ON_EXECUTE_SUBMIT_ACTION,
        ]);
    }

    /**
     * @throws ORMException
     */
    public function onFormSubmitActionSendEmail(SubmissionEvent $event): void
    {
        if (false === $event->checkContext('email.send.user') && false === $event->checkContext('email.send.lead')) {
            return;
        }

        $properties = $event->getAction()->getProperties();
        $emailId    = isset($properties['useremail']) ? (int) $properties['useremail']['email'] : (int) $properties['email'];
        $email      = $this->emailModel->getEntity($emailId);

        if (null === $email || false === $email->isPublished()) {
            return;
        }

        $currentLead = $this->getCurrentLead($event->getActionFeedback());

        if (isset($properties['user_id']) && $properties['user_id']) {
            $this->emailModel->sendEmailToUser($email, $properties['user_id'], $currentLead, $event->getTokens());
        } elseif (isset($currentLead['email'])) {
            $this->emailModel->sendEmail($email, $currentLead, [
                'source'    => ['form', $event->getAction()->getForm()->getId()],
                'tokens'    => $event->getTokens(),
                'ignoreDNC' => true,
            ]);
        }
    }

    private function getCurrentLead(array $feedback): ?array
    {
        // Deal with Lead email
        if (!empty($feedback['lead.create']['lead'])) {
            // the lead was just created via the lead.create action
            $currentLead = $feedback['lead.create']['lead'];
        } else {
            $currentLead = $this->contactTracker->getContact();
        }

        if ($currentLead instanceof Lead) {
            $currentLead = $currentLead->getProfileFields();
        }

        return $currentLead;
    }
}
