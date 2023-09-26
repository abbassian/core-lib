<?php

namespace Autoborna\LeadBundle\Deduplicate;

use Autoborna\LeadBundle\Entity\CompanyRepository;
use Autoborna\LeadBundle\Exception\UniqueFieldNotFoundException;
use Autoborna\LeadBundle\Model\FieldModel;

class CompanyDeduper
{
    use DeduperTrait;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * DedupModel constructor.
     */
    public function __construct(FieldModel $fieldModel, CompanyRepository $companyRepository)
    {
        $this->fieldModel        = $fieldModel;
        $this->companyRepository = $companyRepository;
        $this->object            = 'company';
    }

    /**
     * @return Company[]
     *
     * @throws UniqueFieldNotFoundException
     */
    public function checkForDuplicateCompanies(array $queryFields): array
    {
        $uniqueData = $this->getUniqueData($queryFields);
        if (empty($uniqueData)) {
            throw new UniqueFieldNotFoundException();
        }

        return $this->companyRepository->getCompaniesByUniqueFields($uniqueData);
    }
}
