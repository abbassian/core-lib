<?php

namespace Autoborna\LeadBundle\Tests\Helper;

use Autoborna\LeadBundle\Entity\CompanyLeadRepository;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Helper\PrimaryCompanyHelper;
use PHPUnit\Framework\Exception;

class PrimaryCompanyHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CompanyLeadRepository|Exception
     */
    private $leadRepository;

    protected function setUp(): void
    {
        $this->leadRepository = $this->createMock(CompanyLeadRepository::class);

        $this->leadRepository->expects($this->once())
            ->method('getCompaniesByLeadId')
            ->willReturn(
                [
                    [
                        'score'           => 0,
                        'date_added'      => '2018-06-02 00:00:00',
                        'date_associated' => '2018-06-02 00:00:00',
                        'is_primary'      => 1,
                        'companywebsite'  => 'https://foo.com',
                    ],
                    [
                        'score'           => 0,
                        'date_added'      => '2018-06-02 00:00:00',
                        'date_associated' => '2018-06-02 00:00:00',
                        'is_primary'      => 0,
                        'companywebsite'  => 'https://bar.com',
                    ],
                ]
            );
    }

    public function testProfileFieldsReturnedWithPrimaryCompany()
    {
        $lead = $this->createMock(Lead::class);
        $lead->expects($this->once())
            ->method('getProfileFields')
            ->willReturn(
                [
                    'email' => 'test@test.com',
                ]
            );

        $profileFields = $this->getPrimaryCompanyHelper()->getProfileFieldsWithPrimaryCompany($lead);

        $this->assertEquals(['email' => 'test@test.com', 'companywebsite' => 'https://foo.com'], $profileFields);
    }

    public function testPrimaryCompanyMergedIntoProfileFields()
    {
        $leadFields = [
            'email' => 'test@test.com',
        ];

        $profileFields = $this->getPrimaryCompanyHelper()->mergePrimaryCompanyWithProfileFields(1, $leadFields);

        $this->assertEquals(['email' => 'test@test.com', 'companywebsite' => 'https://foo.com'], $profileFields);
    }

    /**
     * @return PrimaryCompanyHelper
     */
    private function getPrimaryCompanyHelper()
    {
        return new PrimaryCompanyHelper($this->leadRepository);
    }
}
