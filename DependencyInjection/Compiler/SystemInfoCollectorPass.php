<?php

/**
 * File containing the SystemInfoCollectorPass class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportToolsBundle\DependencyInjection\Compiler;

use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\EzSystemInfoCollector;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SystemInfoCollectorPass implements CompilerPassInterface
{
    /**
     * Registers the SystemInfoCollector into the system info collector registry.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->processRegistery($container);
        $this->processSystemInfo($container);
    }

    private function processRegistery(ContainerBuilder $container): void
    {
        if (!$container->has('support_tools.system_info.collector_registry')) {
            return;
        }

        $infoCollectorsTagged = $container->findTaggedServiceIds('support_tools.system_info.collector');

        $infoCollectors = [];
        foreach ($infoCollectorsTagged as $id => $tags) {
            foreach ($tags as $attributes) {
                $infoCollectors[$attributes['identifier']] = new Reference($id);
            }
        }

        $infoCollectorRegistryDef = $container->findDefinition('support_tools.system_info.collector_registry');
        $infoCollectorRegistryDef->setArguments([$infoCollectors]);
    }

    private function processSystemInfo(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('ezplatform_support_tools.system_info.powered_by_options.enabled') ||
            !$container->getParameter('ezplatform_support_tools.system_info.powered_by_options.enabled')
        ) {
            return;
        }

        // Unless there is a custom name, we autodetect based on installed packages
        $vendor = $container->getParameter('kernel.root_dir') . '/../vendor/';
        $customName = $container->getParameter(
            'ezplatform_support_tools.system_info.powered_by_options.custom_name'
        );
        if ($customName !== null) {
            $name = $customName;
        } else if (is_dir($vendor . EzSystemInfoCollector::COMMERCE_PACKAGES[0])) {
            $name = 'eZ Commerce';
        } elseif (is_dir($vendor . EzSystemInfoCollector::ENTERPISE_PACKAGES[0])) {
            $name = 'eZ Platform Enterprise';
        } else {
            $name = 'eZ Platform';
        }

        // Unlike in 3.x there is no constant for version in 2.5, so while this looks hard coded it reflects composer
        // requirements for this package version
        $releaseInfo = $container->getParameter('ezplatform_support_tools.system_info.powered_by_options.release');
        If ($releaseInfo === 'major') {
            $name .= ' 2';
        } else if ($releaseInfo === 'minor') {
            $name .= ' 2.5';
        }

        $container->setParameter('ezplatform_support_tools.system_info.powered_by.name', trim($name));
    }
}
