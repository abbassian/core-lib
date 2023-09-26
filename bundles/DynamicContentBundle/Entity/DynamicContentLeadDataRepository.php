<?php

namespace Autoborna\DynamicContentBundle\Entity;

use Autoborna\CoreBundle\Entity\CommonRepository;

/**
 * Class DownloadRepository.
 */
class DynamicContentLeadDataRepository extends CommonRepository
{
    /**
     * @return string
     */
    public function getTableAlias()
    {
        return 'dcld';
    }
}
