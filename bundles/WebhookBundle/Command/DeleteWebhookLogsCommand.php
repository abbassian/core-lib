<?php

declare(strict_types=1);

namespace Autoborna\WebhookBundle\Command;

use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\WebhookBundle\Entity\LogRepository;
use Autoborna\WebhookBundle\Model\WebhookModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Retains a rolling number of log records.
 */
class DeleteWebhookLogsCommand extends Command
{
    const COMMAND_NAME = 'autoborna:webhooks:delete_logs';

    /** @var LogRepository */
    private $logRepository;

    /** @var CoreParametersHelper */
    private $coreParametersHelper;

    public function __construct(WebhookModel $webhookModel, CoreParametersHelper $coreParametersHelper)
    {
        $this->logRepository        = $webhookModel->getLogRepository();
        $this->coreParametersHelper = $coreParametersHelper;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription('Retains a rolling number of log records.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logMaxLimit  = $this->coreParametersHelper->get('webhook_log_max', WebhookModel::WEBHOOK_LOG_MAX);
        $webHookIds   = $this->logRepository->getWebhooksBasedOnLogLimit($logMaxLimit);
        $webhookCount = count($webHookIds);
        $output->writeln("<info>There is {$webhookCount} webhooks with logs more than defined limit.</info>");

        foreach ($webHookIds as $webHookId) {
            $deletedLogCount = $this->logRepository->removeLimitExceedLogs($webHookId, $logMaxLimit);
            $output->writeln(sprintf('<info>%s logs deleted successfully for webhook id - %s</info>', $deletedLogCount, $webHookId));
        }

        return 0;
    }
}
