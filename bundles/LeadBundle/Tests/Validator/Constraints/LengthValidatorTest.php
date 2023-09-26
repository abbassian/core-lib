<?php

namespace Autoborna\LeadBundle\Tests\Validator\Constraints;

use Autoborna\LeadBundle\Validator\Constraints\Length;
use Autoborna\LeadBundle\Validator\Constraints\LengthValidator;

class LengthValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function testValidate()
    {
        $constraint = new Length(['min' => 3]);
        $validator  = new LengthValidator();
        $this->assertNull($validator->validate('valid', $constraint));
        // Not thrownig Symfony\Component\Validator\Exception\UnexpectedTypeException
        $this->assertNull($validator->validate(['0', '1'], $constraint));
    }
}
