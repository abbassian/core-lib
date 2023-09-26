<?php

namespace Autoborna\LeadBundle\Validator\Constraints;

use Autoborna\LeadBundle\Helper\FormFieldHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\LengthValidator as SymfonyLengthValidator;

class LengthValidator extends SymfonyLengthValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (is_array($value)) {
            $value = FormFieldHelper::formatList(FormFieldHelper::FORMAT_BAR, $value);
        }

        parent::validate($value, $constraint);
    }
}
