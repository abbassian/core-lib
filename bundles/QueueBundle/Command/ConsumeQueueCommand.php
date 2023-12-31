<?php

namespace Autoborna\QueueBundle\Command;

use Autoborna\QueueBundle\Queue\QueueService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI Command to process orders that have been queued.
 * Class ProcessQueuesCommand.
 */
class ConsumeQueueCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('autoborna:queue:process')
            ->setDescription('Process queues')
            ->addOption(
                '--queue-name',
                '-i',
                InputOption::VALUE_REQUIRED,
                'Process queues orders for a specific queue.',
                null
            )
            ->addOption(
                '--messages',
                '-m',
                InputOption::VALUE_OPTIONAL,
                'Number of messages from the queue to process. Default is infinite',
                null
            )
            ->addOption(
                '--timeout',
                '-t',
                InputOption::VALUE_REQUIRED,
                'Set a graceful execution time at this many seconds in the future.',
                null
            );

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container    = $this->getContainer();
        /** @var QueueService $queueService */
        $queueService = $container->get('autoborna.queue.service');

        if (!$queueService->isQueueEnabled()) {
            $output->writeLn('You have not configured autoborna to use queue mode, nothing will be processed');

            return 0;
        }

        $queueName = $input->getOption('queue-name');
        if (empty($queueName)) {
            $output->writeLn('You did not provide a valid queue name');

            return 0;
        }

        $messages = $input->getOption('messages');
        if (0 > $messages) {
            $output->writeLn('You did not provide a valid number of messages. It should be null or greater than 0');

            return 0;
        }

        $timeout = $input->getOption('timeout');
        if (0 > $timeout) {
            $output->writeLn('You did not provide a valid number of seconds. It should be null or greater than 0');

            return 0;
        }

        $queueService->consumeFromQueue($queueName, $messages, $timeout);

        return 0;
    }
}
