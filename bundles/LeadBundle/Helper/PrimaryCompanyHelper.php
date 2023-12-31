<?php

namespace Autoborna\LeadBundle\Helper;

use Autoborna\LeadBundle\Entity\CompanyLeadRepository;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Entity\LeadRepository;

class PrimaryCompanyHelper
{
    private $companyLeadRepository;

    /**
     * PrimaryCompanyHelper constructor.
     *
     * @param LeadRepository $companyLeadRepository
     */
    public function __construct(CompanyLeadRepository $companyLeadRepository)
    {
        $this->companyLeadRepository = $companyLeadRepository;
    }

    /**
     * @return array
     */
    public function getProfileFieldsWithPrimaryCompany(Lead $lead)
    {
        return $this->mergeInPrimaryCompany(
            $this->companyLeadRepository->getCompaniesByLeadId($lead->getId()),
            $lead->getProfileFields()
        );
    }

    /**
     * @param $contactId
     *
     * @return array
     */
    public function mergePrimaryCompanyWithProfileFields($contactId, array $profileFields)
    {
        return $this->mergeInPrimaryCompany(
            $this->companyLeadRepository->getCompaniesByLeadId($contactId),
            $profileFields
        );
    }

    /**
     * @return array
     */
    private function mergeInPrimaryCompany(array $companies, array $profileFields)
    {
        foreach ($companies as $company) {
            if (empty($company['is_primary'])) {
                continue;
            }

            unset($company['id'], $company['score'], $company['date_added'], $company['date_associated'], $company['is_primary']);

            return array_merge($profileFields, $company);
        }

        return $profileFields;
    }
}
