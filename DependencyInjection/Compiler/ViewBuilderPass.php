<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ViewBuilderPass implements CompilerPassInterface
{
    /**
     * Registers the SystemInfoViewBuilder into the view builder registry.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('ezpublish.view_builder.registry')) {
            return;
        }

        $viewBuilderRegistry = $container->findDefinition('ezpublish.view_builder.registry');
        $viewBuilders = [
            $container->findDefinition('support_tools.view.system_info_view_builder'),
        ];

        $viewBuilderRegistry->addMethodCall('addToRegistry', [$viewBuilders]);
    }
}
