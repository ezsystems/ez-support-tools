<?php

/**
 * File containing the SystemInfoDumpCommand class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Command;

use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\SystemInfoCollector;
use EzSystems\EzSupportToolsBundle\SystemInfo\SystemInfoCollectorRegistry;
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
    private $registry;

    public function __construct(SystemInfoCollectorRegistry $registry)
    {
        $this->registry = $registry;

        parent::__construct();
    }

    /**
     * Define command and input options.
     */
    protected function configure()
    {
        $this
            ->setName('ez-support-tools:dump-info')
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
            foreach ($this->registry->getIdentifiers() as $identifier) {
                $output->writeln("  $identifier", true);
            }
        } else if ($input->getArgument('info-collectors')) {
            $identifiers = $input->getArgument('info-collectors');
        } else {
            $identifiers = $this->registry->getIdentifiers();
        }

        $collectedInfo = [];
        foreach ($identifiers as $identifier) {
            $collectedInfo[$identifier] = $this->registry->getItem($identifier)->collect();
        }

        $output->writeln(json_encode($collectedInfo, JSON_PRETTY_PRINT));
    }
}
