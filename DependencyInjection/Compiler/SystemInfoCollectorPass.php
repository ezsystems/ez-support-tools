<?php

/**
 * File containing the SystemInfoCollectorPass class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
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

    private function processRegistery(ContainerBuilder $container)
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

    private function processSystemInfo(ContainerBuilder $container)
    {
        if (!$container->getParameter('support_tools.promote_platform.enabled')) {
            // Skip if disabled
            return;
        } else if ($container->getParameter('support_tools.promote_platform.name')) {
            // Skip if custom name has been configured
            return;
        }

        $vendor = $container->getParameter('kernel.root_dir') . '/../vendor/';
        if (is_dir($vendor . EzSystemInfoCollector::COMMERCE_PACKAGES[0])) {
            $name = 'eZ Commerce';
        } elseif (is_dir($vendor . EzSystemInfoCollector::ENTERPISE_PACKAGES[0])) {
            $name = 'eZ Platform Enterprise';
        } else {
            $name = 'eZ Platform';
        }

        $releaseInfo = $container->getParameter('support_tools.promote_platform.release');
        switch ($releaseInfo) {
            // Unlike on 3.x there is no constant for version, so while this looks hard coded it reflects composer requirements
            case "major":
                $name .= ' 2';
                break;
            case "minor":
                $name .= ' 2.5';
                break;
            case "patch":
                // ignored, we don't really know patch release version here. Even if we did it would only be correct if
                // we verified all packes where in exact same versions as released, if not we should maybe use denotation such as "2.5.6+"
        }

        $container->setParameter('support_tools.promote_platform.name', $name);
    }
}
