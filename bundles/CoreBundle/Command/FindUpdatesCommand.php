<?php

namespace Autoborna\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI Command to fetch application updates.
 */
class FindUpdatesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('autoborna:update:find')
            ->setDescription('Fetches updates for Autoborna')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command checks for updates for the Autoborna application.

<info>php %command.full_name%</info>
EOT
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator */
        $translator = $this->getContainer()->get('translator');
        $translator->setLocale($this->getContainer()->get('autoborna.factory')->getParameter('locale'));

        $updateHelper = $this->getContainer()->get('autoborna.helper.update');
        $updateData   = $updateHelper->fetchData(true);

        if ($updateData['error']) {
            $output->writeln('<error>'.$translator->trans($updateData['message']).'</error>');
        } elseif ('autoborna.core.updater.running.latest.version' == $updateData['message']) {
            $output->writeln('<info>'.$translator->trans($updateData['message']).'</info>');
        } else {
            $output->writeln($translator->trans($updateData['message'], ['%version%' => $updateData['version'], '%announcement%' => $updateData['announcement']]));
            $output->writeln($translator->trans('autoborna.core.updater.cli.update'));
        }

        return 0;
    }
}
