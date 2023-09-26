<?php

namespace Autoborna\EmailBundle\Entity;

use Autoborna\LeadBundle\Entity\Lead;

/**
 * Interface EmailReplyRepositoryInterface.
 */
interface EmailReplyRepositoryInterface
{
    /**
     * @param int|Lead $leadId
     * @param array    $options
     *
     * @return array
     */
    public function getByLeadIdForTimeline($leadId, $options);
}
