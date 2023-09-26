<?php

namespace Autoborna\EmailBundle\Helper;

use Autoborna\CoreBundle\Factory\AutobornaFactory;
use Autoborna\LeadBundle\Entity\Lead;

/**
 * Class PointEventHelper.
 */
class PointEventHelper
{
    /**
     * @param $eventDetails
     * @param $action
     *
     * @return int
     */
    public static function validateEmail($eventDetails, $action)
    {
        if (null === $eventDetails) {
            return false;
        }

        $emailId = $eventDetails->getId();

        if (isset($action['properties']['emails'])) {
            $limitToEmails = $action['properties']['emails'];
        }

        if (!empty($limitToEmails) && !in_array($emailId, $limitToEmails)) {
            //no points change
            return false;
        }

        return true;
    }

    /**
     * @param $event
     *
     * @return bool
     */
    public static function sendEmail($event, Lead $lead, AutobornaFactory $factory)
    {
        $properties = $event['properties'];
        $emailId    = (int) $properties['email'];

        /** @var \Autoborna\EmailBundle\Model\EmailModel $model */
        $model = $factory->getModel('email');
        $email = $model->getEntity($emailId);

        //make sure the email still exists and is published
        if (null != $email && $email->isPublished()) {
            $leadFields = $lead->getFields();
            if (isset($leadFields['core']['email']['value']) && $leadFields['core']['email']['value']) {
                /** @var \Autoborna\LeadBundle\Model\LeadModel $leadModel */
                $leadCredentials       = $lead->getProfileFields();
                $leadCredentials['id'] = $lead->getId();

                $options   = ['source' => ['trigger', $event['id']]];
                $emailSent = $model->sendEmail($email, $leadCredentials, $options);

                return is_array($emailSent) ? false : true;
            }
        }

        return false;
    }
}
