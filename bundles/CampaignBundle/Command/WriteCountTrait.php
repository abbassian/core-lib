<?php

namespace Autoborna\CampaignBundle\Command;

use Autoborna\CampaignBundle\Executioner\Result\Counter;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

trait WriteCountTrait
{
    private function writeCounts(OutputInterface $output, TranslatorInterface $translator, Counter $counter): void
    {
        $output->writeln('');
        $output->writeln(
            '<comment>'.$translator->trans(
                'autoborna.campaign.trigger.events_executed',
                ['%count%' => $counter->getTotalExecuted()]
            )
            .'</comment>'
        );
        $output->writeln(
            '<comment>'.$translator->trans(
                'autoborna.campaign.trigger.events_scheduled',
                ['%count%' => $counter->getTotalScheduled()]
            )
            .'</comment>'
        );
        $output->writeln('');
    }
}
