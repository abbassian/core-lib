<?php

namespace Autoborna\PluginBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReloadCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('autoborna:plugins:reload')
            ->setAliases(
                [
                    'autoborna:plugins:install',
                    'autoborna:plugins:update',
                ]
            )
            ->setDescription('Installs, updates, enable and/or disable plugins.');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeLn(
            $this->getContainer()->get('autoborna.plugin.facade.reload')->reloadPlugins()
        );

        return 0;
    }
}
