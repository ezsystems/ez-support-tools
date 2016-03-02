<?php

/**
 * File containing the SystemInfoCommand class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Command;

use EzSystems\EzSupportToolsBundle\InfoProvider\InfoProviderCollection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use RuntimeException;

class SystemInfoCommand extends ContainerAwareCommand
{
    /**
     * @var \EzSystems\EzSupportToolsBundle\InfoProvider\InfoProviderCollection
     */
    protected $infoProviderCollection;

    public function __construct(InfoProviderCollection $infoProviderCollection)
    {
        $this->infoProviderCollection = $infoProviderCollection;

        parent::__construct();
    }

    /**
     * Define command and input options.
     */
    protected function configure()
    {
        $this
            ->setName('ez-support-tools:system-info')
            ->setDescription('Collects system information and outputs it in your chosen format.')
            ->addOption(
                'output-format',
                null,
                InputOption::VALUE_REQUIRED,
                'Output format, one of: ' . implode(', ', $this->availableOutputFormats()),
                'plain'
            )
            ->addOption(
                'info-providers',
                null,
                InputOption::VALUE_REQUIRED,
                'Use only these info providers, one or more of: ' .
                implode(', ', $this->infoProviderCollection->infoProviderIdentifiers()) . ' (comma separated)'
            )
            ->setHelp(
                <<<EOT
Collects system information and outputs it to standard output in your chosen
format. To dump the information to file, you can pipe standard output to a
file of your choosing.

By default the plain format is used. Other output formats are available,
see --output-format

Info providers are classes that provide information about an aspect of the
system. By default all available providers are used. You can choose to use
only some of them, see --info-providers

EOT
            )
            ;
    }

    /**
     * Available output formats.
     *
     * @TODO make output formats extensible
     *
     * @return array
     */
    protected function availableOutputFormats()
    {
        return ['plain', 'json'];
    }

    /**
     * Get the output format chosen by the user if any, or the default value.
     *
     * @throws RuntimeException If the specified option is invalid.
     *
     * @param $input InputInterface
     *
     * @return string Output format
     */
    protected function getOutputFormatOption(InputInterface $input)
    {
        $outputFormat = $input->getOption('output-format');
        if (!in_array($outputFormat, $this->availableOutputFormats())) {
            throw new RuntimeException(
                "The output format '$outputFormat' is invalid, choose one of: " .
                implode(', ', $this->availableOutputFormats())
            );
        }

        return $outputFormat;
    }

    /**
     * Get the info provider(s) identifiers chosen by the user if any, or the default (all of them).
     *
     * @throws RuntimeException If the specified option is invalid.
     *
     * @param $input InputInterface
     *
     * @return string[] Info provider identifiers
     */
    protected function getInfoProvidersOption(InputInterface $input)
    {
        $infoProviderIdentifierFilterStr = $input->getOption('info-providers');
        if ($infoProviderIdentifierFilterStr) {
            $infoProviderIdentifierFilter = explode(',', $infoProviderIdentifierFilterStr);
            foreach ($infoProviderIdentifierFilter as $infoProviderIdentifier) {
                if (!in_array($infoProviderIdentifier, $this->infoProviderCollection->infoProviderIdentifiers())) {
                    throw new RuntimeException(
                        "The info provider '$infoProviderIdentifier' is invalid, choose one or more of: " .
                        implode(', ', $this->infoProviderCollection->infoProviderIdentifiers()) .
                        "\n(comma separated)"
                    );
                }
            }
        } else {
            $infoProviderIdentifierFilter = $this->infoProviderCollection->infoProviderIdentifiers();
        }

        return $infoProviderIdentifierFilter;
    }

    /**
     * Execute the Command.
     *
     * @throws RuntimeException When an option is invalid.
     *
     * @param $input InputInterface
     * @param $output OutputInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputFormat = $this->getOutputFormatOption($input);
        $infoProviderIdentifierFilter = $this->getInfoProvidersOption($input);

        $outputArray = [];
        foreach ($this->infoProviderCollection->infoProviders() as $infoProvider) {
            if (!in_array($infoProvider->getIdentifier(), $infoProviderIdentifierFilter)) {
                continue;
            }

            $outputArray[$infoProvider->getIdentifier()] = $infoProvider->getInfo();
        }

        // TODO make output formats extensible
        switch ($outputFormat) {
            case 'plain':
                $output->writeln(var_export($outputArray, true));
                break;

            case 'json':
                $output->writeln(json_encode($outputArray));
                break;
        }
    }
}
