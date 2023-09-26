<?php

namespace Autoborna\LeadBundle\Tests\Helper;

use Autoborna\LeadBundle\Helper\IdentifyCompanyHelper;
use Autoborna\LeadBundle\Model\CompanyModel;

class IdentifyCompanyHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testDomainExistsRealDomain()
    {
        $helper     = new IdentifyCompanyHelper();
        $reflection = new \ReflectionClass(IdentifyCompanyHelper::class);
        $method     = $reflection->getMethod('domainExists');
        $method->setAccessible(true);
        $result = $method->invokeArgs($helper, ['hello@autoborna.org']);

        $this->assertTrue(is_string($result));
        $this->assertGreaterThan(0, strlen($result));
    }

    public function testDomainExistsWithFakeDomain()
    {
        $helper     = new IdentifyCompanyHelper();
        $reflection = new \ReflectionClass(IdentifyCompanyHelper::class);
        $method     = $reflection->getMethod('domainExists');
        $method->setAccessible(true);
        $result = $method->invokeArgs($helper, ['hello@domain.fake']);

        $this->assertFalse($result);
    }

    public function testFindCompanyByName()
    {
        $company = [
            'company' => 'Autoborna',
        ];

        $expected = [
            'companyname'    => 'Autoborna',
        ];

        $model = $this->getMockBuilder(CompanyModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $model->expects($this->once())
            ->method('checkForDuplicateCompanies')
            ->willReturn([]);

        $model->expects($this->any())
            ->method('fetchCompanyFields')
            ->willReturn([['alias' => 'companyname']]);

        $helper     = new IdentifyCompanyHelper();
        $reflection = new \ReflectionClass(IdentifyCompanyHelper::class);
        $method     = $reflection->getMethod('findCompany');
        $method->setAccessible(true);
        [$resultCompany, $entities] = $method->invokeArgs($helper, [$company, $model]);

        $this->assertEquals($expected, $resultCompany);
    }

    public function testFindCompanyByNameWithValidEmail()
    {
        $company = [
            'company'      => 'Autoborna',
            'companyemail' => 'hello@autoborna.org',
        ];

        $expected = [
            'companyname'    => 'Autoborna',
            'companyemail'   => 'hello@autoborna.org',
        ];

        $model = $this->getMockBuilder(CompanyModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $model->expects($this->once())
            ->method('checkForDuplicateCompanies')
            ->willReturn([]);

        $model->expects($this->any())
            ->method('fetchCompanyFields')
            ->willReturn([['alias' => 'companyname']]);

        $helper     = new IdentifyCompanyHelper();
        $reflection = new \ReflectionClass(IdentifyCompanyHelper::class);
        $method     = $reflection->getMethod('findCompany');
        $method->setAccessible(true);
        list($resultCompany, $entities) = $method->invokeArgs($helper, [$company, $model]);

        $this->assertEquals($expected, $resultCompany);
    }

    public function testFindCompanyByNameWithValidEmailAndCustomWebsite()
    {
        $company = [
            'company'        => 'Autoborna',
            'companyemail'   => 'hello@autoborna.org',
            'companywebsite' => 'https://autoborna.org',
        ];

        $expected = [
            'companyname'    => 'Autoborna',
            'companywebsite' => 'https://autoborna.org',
            'companyemail'   => 'hello@autoborna.org',
        ];

        $model = $this->getMockBuilder(CompanyModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $model->expects($this->once())
            ->method('checkForDuplicateCompanies')
            ->willReturn([]);

        $model->expects($this->any())
            ->method('fetchCompanyFields')
            ->willReturn([['alias' => 'companyname']]);

        $helper     = new IdentifyCompanyHelper();
        $reflection = new \ReflectionClass(IdentifyCompanyHelper::class);
        $method     = $reflection->getMethod('findCompany');
        $method->setAccessible(true);
        list($resultCompany, $entities) = $method->invokeArgs($helper, [$company, $model]);

        $this->assertEquals($expected, $resultCompany);
    }
}
