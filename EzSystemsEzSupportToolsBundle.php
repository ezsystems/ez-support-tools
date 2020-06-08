<?php

/**
 * File containing the EzSystemsEzSupportToolsBundle class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle;

use EzSystems\EzSupportToolsBundle\DependencyInjection\EzSystemsEzSupportToolsExtension;
use EzSystems\EzSupportToolsBundle\DependencyInjection\Compiler\SystemInfoCollectorPass;
use EzSystems\EzSupportToolsBundle\DependencyInjection\Compiler\OutputFormatPass;
use EzSystems\EzSupportToolsBundle\DependencyInjection\Compiler\ViewBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzSystemsEzSupportToolsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new SystemInfoCollectorPass());
        $container->addCompilerPass(new OutputFormatPass());
        $container->addCompilerPass(new ViewBuilderPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ExtensionInterface
    {
        return new EzSystemsEzSupportToolsExtension();
    }
}
