<?php

namespace Autoborna\FormBundle\Validator\Constraint;

use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FileExtensionConstraintValidator extends ConstraintValidator
{
    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    public function __construct(CoreParametersHelper $coreParametersHelper)
    {
        $this->coreParametersHelper = $coreParametersHelper;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!is_array($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ forbidden }}', '')
                ->addViolation();
        }

        $blacklistedExtensions = $this->coreParametersHelper->get('blacklisted_extensions');
        $intersect             = array_intersect($value, $blacklistedExtensions);
        if ($intersect) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ forbidden }}', implode(', ', $intersect))
                ->addViolation();
        }
    }
}
