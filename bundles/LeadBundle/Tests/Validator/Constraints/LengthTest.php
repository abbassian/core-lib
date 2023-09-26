<?php

namespace Autoborna\LeadBundle\Tests\Validator\Constraints;

use Autoborna\LeadBundle\Validator\Constraints\Length;
use Autoborna\LeadBundle\Validator\Constraints\LengthValidator;

class LengthTest extends \PHPUnit\Framework\TestCase
{
    public function testValidateBy()
    {
        $constraint = new Length(['min' => 3]);
        $this->assertEquals(LengthValidator::class, $constraint->validatedBy());
    }
}
