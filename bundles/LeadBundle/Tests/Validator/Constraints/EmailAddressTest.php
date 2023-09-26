<?php

namespace Autoborna\LeadBundle\Tests\Validator\Constraints;

use Autoborna\LeadBundle\Form\Validator\Constraints\EmailAddress;
use Autoborna\LeadBundle\Form\Validator\Constraints\EmailAddressValidator;

class EmailAddressTest extends \PHPUnit\Framework\TestCase
{
    public function testValidateBy(): void
    {
        $constraint = new EmailAddress();
        $this->assertEquals(EmailAddressValidator::class, $constraint->validatedBy());
    }
}
