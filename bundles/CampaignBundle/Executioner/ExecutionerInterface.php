<?php

namespace Autoborna\CampaignBundle\Executioner;

use Autoborna\CampaignBundle\Entity\Campaign;
use Autoborna\CampaignBundle\Executioner\ContactFinder\Limiter\ContactLimiter;
use Symfony\Component\Console\Output\OutputInterface;

interface ExecutionerInterface
{
    /**
     * @return mixed
     */
    public function execute(Campaign $campaign, ContactLimiter $limiter, OutputInterface $output = null);
}
