<?php

namespace Autoborna\CoreBundle\Tests\Unit\Command\src;

use Autoborna\CoreBundle\Command\ModeratedCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FakeModeratedCommand extends ModeratedCommand
{
    protected function configure()
    {
        $this->setName('autoborna:fake:command');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkRunStatus($input, $output);

        return 0;
    }

    public function forceCompleteRun(): void
    {
        $this->completeRun();
    }
}
