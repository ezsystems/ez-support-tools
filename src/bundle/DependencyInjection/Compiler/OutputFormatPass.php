<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OutputFormatPass implements CompilerPassInterface
{
    /**
     * Registers the OutputFormat tagged services into the output format registry.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('support_tools.system_info.output_registry')) {
            return;
        }

        $outputFormattersTagged = $container->findTaggedServiceIds('support_tools.system_info.output_format');

        $outputFormatters = [];
        foreach ($outputFormattersTagged as $id => $tags) {
            foreach ($tags as $attributes) {
                $outputFormatters[$attributes['format']] = new Reference($id);
            }
        }

        $outputFormatRegistryDef = $container->findDefinition('support_tools.system_info.output_registry');
        $outputFormatRegistryDef->setArguments([$outputFormatters]);
    }
}
