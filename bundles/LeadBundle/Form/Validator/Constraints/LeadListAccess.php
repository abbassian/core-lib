<?php

namespace Autoborna\LeadBundle\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class LeadListAccess extends Constraint
{
    public $message = 'autoborna.lead.lists.failed';

    public function validatedBy()
    {
        return 'leadlist_access';
    }
}
