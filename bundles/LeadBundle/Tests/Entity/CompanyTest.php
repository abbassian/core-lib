<?php

namespace Autoborna\LeadBundle\Tests\Entity;

use Autoborna\LeadBundle\Entity\Company;

class CompanyTest extends \PHPUnit\Framework\TestCase
{
    public function testChangingPropertiesHydratesFieldChanges()
    {
        $email    = 'foo@bar.com';
        $company  = new Company();
        $company->addUpdatedField('email', $email);
        $changes = $company->getChanges();

        $this->assertFalse(empty($changes['fields']['email']));

        $this->assertEquals($email, $changes['fields']['email'][1]);
    }
}
