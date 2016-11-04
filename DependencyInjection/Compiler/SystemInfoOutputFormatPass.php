<?php

/**
 * File containing the SystemInfoCollectorPass class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SystemInfoOutputFormatPass implements CompilerPassInterface
{
    /**
     * Registers the SystemInfoCollector into the system info collector registry.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('support_tools.system_info.output_registry')) {
            return;
        }

        $infoCollectorsTagged = $container->findTaggedServiceIds('support_tools.system_info.output_format');

        $infoCollectors = [];
        foreach ($infoCollectorsTagged as $id => $tags) {
            foreach ($tags as $attributes) {
                $infoCollectors[$attributes['identifier']] = new Reference($id);
            }
        }

        $infoCollectorRegistryDef = $container->findDefinition('support_tools.system_info.output_registry');
        $infoCollectorRegistryDef->setArguments([$infoCollectors]);
    }
}
