<?php

/**
 * File containing the SystemInfoDumpCommand class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Command;

use EzSystems\EzSupportToolsBundle\SystemInfo\SystemInfoCollectorRegistry;
use EzSystems\EzSupportToolsBundle\SystemInfo\OutputFormatRegistry;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SystemInfoDumpCommand extends ContainerAwareCommand
{
    /**
     * System info collector registry.
     *
     * @var \EzSystems\EzSupportToolsBundle\SystemInfo\SystemInfoCollectorRegistry
     */
    private $systemInfoCollectorRegistry;

    /**
     * Output format registry.
     *
     * @var \EzSystems\EzSupportToolsBundle\SystemInfo\OutputFormatRegistry
     */
    private $outputFormatRegistry;

    public function __construct(SystemInfoCollectorRegistry $systemInfoCollectorRegistry, OutputFormatRegistry $outputFormatRegistry)
    {
        $this->systemInfoCollectorRegistry = $systemInfoCollectorRegistry;
        $this->outputFormatRegistry = $outputFormatRegistry;

        parent::__construct();
    }

    /**
     * Define command and input options.
     */
    protected function configure()
    {
        $this
            ->setName('ez-support-tools:dump-info')
            ->setAliases([
                'ibexa:dump-info',
                'ibexa:info',
            ])
            ->setDescription('Collects system information and dumps it.')
            ->setHelp(<<<'EOD'
By default it dumps information from all available information collectors.
You can specify one or more collectors as arguments, e.g. 'php database hardware'.
To get a list if available collectors, use '--list-info-collectors'
EOD
            )
            ->addOption(
                'list-info-collectors',
                null,
                InputOption::VALUE_NONE,
                'List all available information collectors, and exit.'
            )
            ->addOption(
                'format',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Output format (currently only JSON)',
                'json'
            )
            ->addArgument(
                'info-collectors',
                InputArgument::IS_ARRAY,
                'Which information collector(s) should be used? (separate multiple names with spaces)'
            )
            ;
    }

    /**
     * Execute the Command.
     *
     * @param $input InputInterface
     * @param $output OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('list-info-collectors')) {
            $output->writeln('Available info collectors:', true);
            foreach ($this->systemInfoCollectorRegistry->getIdentifiers() as $identifier) {
                $output->writeln("  $identifier", true);
            }

            return;
        }

        $outputFormatter = $this->outputFormatRegistry->getItem(
            $input->getOption('format')
         );

        if ($input->getArgument('info-collectors')) {
            $identifiers = $input->getArgument('info-collectors');
        } else {
            $identifiers = $this->systemInfoCollectorRegistry->getIdentifiers();
        }

        // Collect info for the given identifiers.
        $collectedInfoArray = [];
        foreach ($identifiers as $identifier) {
            $collectedInfoArray[$identifier] = $this->systemInfoCollectorRegistry->getItem($identifier)->collect();
        }

        $output->writeln(
             $outputFormatter->format($collectedInfoArray)
         );
    }
}
