<?php

/**
 * File containing the EzSystemsEzSupportToolsExtension class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportToolsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EzSystemsEzSupportToolsExtension extends Extension
{
    public function getAlias()
    {
        return 'ezplatform_support_tools';
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('default_settings.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        if (!isset($config['system_info'])) {
            return;
        }

        $container->setParameter(
            'ezplatform_support_tools.system_info.powered_by_options.enabled',
            $config['system_info']['powered_by']['enabled']
        );
        $container->setParameter(
            'ezplatform_support_tools.system_info.powered_by_options.release',
            $config['system_info']['powered_by']['release']
        );
        $container->setParameter(
            'ezplatform_support_tools.system_info.powered_by_options.custom_name',
            $config['system_info']['powered_by']['custom_name']
        );
    }
}
