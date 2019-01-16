<?php

/**
 * File containing the EzSystemsEzSupportToolsExtension class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EzSystemsEzSupportToolsExtension extends Extension
{
    const EZ_ENCORE_CONFIG_NAME = 'ez.config.js';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('default_settings.yml');

        $this->dumpBundlesWebpackEncodeConfigurationPaths(
            dirname($container->getParameter('kernel.root_dir')) . '/var/encore',
            $container->getParameter('kernel.bundles_metadata')
        );
    }

    /**
     * Looks for Resources/encore/ez.config.js file in every registered and enabled bundle and dumps json list of paths to files found.
     *
     * @param string $targetPath Where to put eZ Encore paths configuration file (default: var/encore/ez.config.js)
     * @param array $bundlesMetadata
     */
    public function dumpBundlesWebpackEncodeConfigurationPaths(string $targetPath, array $bundlesMetadata)
    {
        $finder = new Finder();
        $filesystem = new Filesystem();

        $paths = [];
        
        $finder
            ->in(array_column($bundlesMetadata, 'path'))
            ->path('Resources/encore')
            ->name(self::EZ_ENCORE_CONFIG_NAME)
            ->files();

        foreach ($finder as $fileInfo) {
            $paths[] = $fileInfo->getRealPath();
        }

        $filesystem->mkdir($targetPath);
        $filesystem->dumpFile(
            $targetPath . '/' . self::EZ_ENCORE_CONFIG_NAME,
            sprintf('module.exports = %s', json_encode($paths))
        );
    }
}
