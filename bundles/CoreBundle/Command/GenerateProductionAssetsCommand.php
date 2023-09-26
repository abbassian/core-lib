<?php

namespace Autoborna\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI Command to generate production assets.
 */
class GenerateProductionAssetsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('autoborna:assets:generate')
            ->setDescription('Combines and minifies asset files from each bundle into single production files')
            ->setHelp(
                <<<'EOT'
                The <info>%command.name%</info> command Combines and minifies files from each bundle's Assets/css/* and Assets/js/* folders into single production files stored in root/media/css and root/media/js respectively.

<info>php %command.full_name%</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container   = $this->getContainer();
        $assetHelper = $container->get('autoborna.helper.assetgeneration');

        $pathsHelper = $container->get('autoborna.helper.paths');

        // Combine and minify bundle assets
        $assetHelper->getAssets(true);

        // Minify Autoborna Form SDK
        file_put_contents(
            $pathsHelper->getSystemPath('assets', true).'/js/autoborna-form-tmp.js',
            \Minify::combine([$pathsHelper->getSystemPath('assets', true).'/js/autoborna-form-src.js'])
        );
        // Fix the AutobornaSDK loader
        file_put_contents(
            $pathsHelper->getSystemPath('assets', true).'/js/autoborna-form.js',
            str_replace("'autoborna-form-src.js'", "'autoborna-form.js'",
                file_get_contents($pathsHelper->getSystemPath('assets', true).'/js/autoborna-form-tmp.js'))
        );
        // Remove temp file.
        unlink($pathsHelper->getSystemPath('assets', true).'/js/autoborna-form-tmp.js');

        /** @var \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator */
        $translator = $container->get('translator');
        $translator->setLocale($container->get('autoborna.helper.core_parameters')->get('locale'));

        // Update successful
        $output->writeln('<info>'.$translator->trans('autoborna.core.command.asset_generate_success').'</info>');

        return 0;
    }
}
