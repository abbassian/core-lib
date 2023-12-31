<?php

namespace Autoborna\CoreBundle\Command;

use Autoborna\CoreBundle\IpLookup\AbstractLocalDataLookup;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI Command to fetch updated Maxmind database.
 */
class UpdateIpDataStoreCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('autoborna:iplookup:download')
            ->setDescription('Fetch remote datastores for IP lookup services that leverage local lookups')
            ->setHelp(
                <<<'EOT'
                The <info>%command.name%</info> command is used to update local IP lookup data if applicable.

<info>php %command.full_name%</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ipService  = $this->getContainer()->get('autoborna.ip_lookup');
        $factory    = $this->getContainer()->get('autoborna.factory');
        $translator = $factory->getTranslator();

        if ($ipService instanceof AbstractLocalDataLookup) {
            if ($ipService->downloadRemoteDataStore()) {
                $output->writeln('<info>'.$translator->trans('autoborna.core.success').'</info>');
            } else {
                $remoteUrl = $ipService->getRemoteDateStoreDownloadUrl();
                $localPath = $ipService->getLocalDataStoreFilepath();

                if ($remoteUrl && $localPath) {
                    $output->writeln('<error>'.$translator->trans(
                        'autoborna.core.ip_lookup.remote_fetch_error',
                        [
                            '%remoteUrl%' => $remoteUrl,
                            '%localPath%' => $localPath,
                        ]
                    ).'</error>');
                } else {
                    $output->writeln('<error>'.$translator->trans(
                        'autoborna.core.ip_lookup.remote_fetch_error_generic'
                    ).'</error>');
                }
            }
        }

        return 0;
    }
}
