<?php

namespace Autoborna\WebhookBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI Command to process queued webhook payloads.
 */
class ProcessWebhookQueuesCommand extends ContainerAwareCommand
{
    public const COMMAND_NAME = 'autoborna:webhooks:process';

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Process queued webhook payloads')
            ->addOption(
                '--webhook-id',
                '-i',
                InputOption::VALUE_OPTIONAL,
                'Process payload for a specific webhook.  If not specified, all webhooks will be processed.',
                null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Autoborna\WebhookBundle\Model\WebhookModel $model */
        $model  = $this->getContainer()->get('autoborna.webhook.model.webhook');
        $params = $this->getContainer()->get('autoborna.helper.core_parameters');

        // check to make sure we are in queue mode
        if ($params->getParameter('queue_mode') != $model::COMMAND_PROCESS) {
            $output->writeLn('Webhook Bundle is in immediate process mode. To use the command function change to command mode.');

            return 0;
        }

        $id = $input->getOption('webhook-id');

        if ($id) {
            $webhook  = $model->getEntity($id);
            $webhooks = (null !== $webhook && $webhook->isPublished()) ? [$id => $webhook] : [];
        } else {
            // make sure we only get published webhook entities
            $webhooks = $model->getEntities(
                [
                    'filter' => [
                        'force' => [
                            [
                                'column' => 'e.isPublished',
                                'expr'   => 'eq',
                                'value'  => 1,
                            ],
                        ],
                    ],
                ]
            );
        }

        if (!count($webhooks)) {
            $output->writeln('<error>No published webhooks found. Try again later.</error>');

            return 0;
        }

        $output->writeLn('<info>Processing Webhooks</info>');

        try {
            $model->processWebhooks($webhooks);
        } catch (\Exception $e) {
            $output->writeLn('<error>'.$e->getMessage().'</error>');
            $output->writeLn('<error>'.$e->getTraceAsString().'</error>');

            return 1;
        }

        $output->writeLn('<info>Webhook Processing Complete</info>');

        return 0;
    }
}
