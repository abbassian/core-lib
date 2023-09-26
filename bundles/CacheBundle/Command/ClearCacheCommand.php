<?php

declare(strict_types=1);

namespace Autoborna\CacheBundle\Command;

use Autoborna\CacheBundle\Cache\CacheProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI Command to clear the application cache.
 */
class ClearCacheCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this->setName('autoborna:cache:clear')
            ->setDescription('Clears Autoborna\'s cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        /** @var CacheProvider $cacheProvider */
        $cacheProvider = $this->getContainer()->get('autoborna.cache.provider');

        return (int) !$cacheProvider->clear();
    }
}
