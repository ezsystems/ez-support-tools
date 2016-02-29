<?php

/**
 * File containing the EzSystemsEzSupportToolsExtension class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportTools\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class EzSystemsEzSupportToolsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $processor = new ConfigurationProcessor( $container, 'ez_systems_ez_support_tools' );
    }

    public function prepend( ContainerBuilder $container )
    {
        // make sure Assetic can handle the assets (mainly the CSS files)
        $container->prependExtensionConfig( 'assetic', array( 'bundles' => array( 'EzSystemsEzSupportTools' ) ) );
        // prepend the yui.yml and the css.yml from EzSystemsEzSupportTools.php
        // of course depending on your needs you can remove the handling of yui.yml or css.yml
        $this->prependYui( $container );
//        $this->prependCss( $container );
    }

    private function prependYui( ContainerBuilder $container )
    {
        $container->setParameter(
            'support_tools.public_dir',
            'bundles/ezsupporttools'
        );
        $yuiConfigFile = __DIR__ . '/../Resources/config/yui.yml';
        $config = Yaml::parse( file_get_contents( $yuiConfigFile ) );
        $container->prependExtensionConfig( 'ez_platformui', $config );
        $container->addResource( new FileResource( $yuiConfigFile ) );
    }

//    private function prependCss( ContainerBuilder $container )
//    {
//        $container->setParameter(
//            'extending_platformui.css_dir',
//            'bundles/ezsystemsextendingplatformuiconference/css'
//        );
//        $cssConfigFile = __DIR__ . '/../Resources/config/css.yml';
//        $config = Yaml::parse( file_get_contents( $cssConfigFile ) );
//        $container->prependExtensionConfig( 'ez_platformui', $config );
//        $container->addResource( new FileResource( $cssConfigFile ) );
//    }
}
