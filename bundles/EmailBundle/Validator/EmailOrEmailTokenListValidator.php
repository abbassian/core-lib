<?php

declare(strict_types=1);

namespace Autoborna\EmailBundle\Validator;

use Autoborna\CoreBundle\Exception\InvalidValueException;
use Autoborna\CoreBundle\Exception\RecordException;
use Autoborna\CoreBundle\Form\DataTransformer\ArrayStringTransformer;
use Autoborna\EmailBundle\Exception\InvalidEmailException;
use Autoborna\EmailBundle\Helper\EmailValidator;
use Autoborna\LeadBundle\DataObject\ContactFieldToken;
use Autoborna\LeadBundle\Exception\InvalidContactFieldTokenException;
use Autoborna\LeadBundle\Validator\CustomFieldValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class EmailOrEmailTokenListValidator extends ConstraintValidator
{
    private EmailValidator $emailValidator;

    private CustomFieldValidator $customFieldValidator;

    private ArrayStringTransformer $transformer;

    public function __construct(
        EmailValidator $emailValidator,
        CustomFieldValidator $customFieldValidator
    ) {
        $this->transformer          = new ArrayStringTransformer();
        $this->emailValidator       = $emailValidator;
        $this->customFieldValidator = $customFieldValidator;
    }

    /**
     * @return void
     */
    public function validate($csv, Constraint $constraint)
    {
        if (!$constraint instanceof EmailOrEmailTokenList) {
            throw new UnexpectedTypeException($constraint, EmailOrEmailTokenList::class);
        }

        if (null === $csv || '' === $csv) {
            return;
        }

        if (!is_string($csv)) {
            throw new UnexpectedTypeException($csv, 'string');
        }

        array_map(
            $this->makeEmailOrEmailTokenValidator(),
            $this->transformer->reverseTransform($csv)
        );
    }

    private function makeEmailOrEmailTokenValidator(): callable
    {
        return function (string $emailOrToken) {
            try {
                // Try to validate if the value is an email address.
                $this->emailValidator->validate($emailOrToken);
            } catch (InvalidEmailException $emailException) {
                try {
                    // The token syntax is validated during creation of new ContactFieldToken object.
                    $contactFieldToken = new ContactFieldToken($emailOrToken);

                    // Validate that the token default value is a valid email address if set.
                    if ($contactFieldToken->getDefaultValue()) {
                        $this->emailValidator->validate($contactFieldToken->getDefaultValue());
                    }

                    // Validate that the contact field exists and is type of email.
                    $this->customFieldValidator->validateFieldType($contactFieldToken->getFieldAlias(), 'email');
                } catch (RecordException | InvalidValueException | InvalidContactFieldTokenException $tokenException) {
                    $this->context->addViolation(
                        'autoborna.email.email_or_token.not_valid',
                        ['%value%' => $emailOrToken, '%details%' => $tokenException->getMessage()]
                    );
                }
            }
        };
    }
}
