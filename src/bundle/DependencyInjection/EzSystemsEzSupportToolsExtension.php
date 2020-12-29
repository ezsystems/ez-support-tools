<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportToolsBundle\DependencyInjection;

use EzSystems\EzPlatformCoreBundle\EzPlatformCoreBundle;
use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\IbexaSystemInfoCollector;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\IbexaSystemInfo;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EzSystemsEzSupportToolsExtension extends Extension implements PrependExtensionInterface
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
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');
        $loader->load('default_settings.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        if (isset($config['system_info']) && $config['system_info']['powered_by']['enabled']) {
            $container->setParameter(
                'ezplatform_support_tools.system_info.powered_by.name',
                $this->getPoweredByName(
                    $container,
                    $config['system_info']['powered_by']['release']
                )
            );
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        $this->prependJMSTranslation($container);
    }

    private function getPoweredByName(ContainerBuilder $container, ?string $release): string
    {
        // Autodetect product name if configured name is null (default)
        $vendor = $container->getParameter('kernel.project_dir') . '/vendor/';
        if (is_dir($vendor . IbexaSystemInfoCollector::COMMERCE_PACKAGES[0])) {
            $name = IbexaSystemInfo::PRODUCT_NAME_VARIANTS['commerce'];
        } elseif (is_dir($vendor . IbexaSystemInfoCollector::ENTERPRISE_PACKAGES[0])) {
            $name = IbexaSystemInfo::PRODUCT_NAME_VARIANTS['experience'];
        } else {
            $name = IbexaSystemInfo::PRODUCT_NAME_OSS;
        }

        if ($release === 'major') {
            $name .= ' v' . (int)EzPlatformCoreBundle::VERSION;
        } elseif ($release === 'minor') {
            $version = explode('.', EzPlatformCoreBundle::VERSION);
            $name .= ' v' . $version[0] . '.' . $version[1];
        }

        return $name;
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_support_tools' => [
                    'dirs' => [
                        __DIR__ . '/../../../src/',
                    ],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                    'excluded_dirs' => ['Behat', 'Tests', 'node_modules'],
                ],
            ],
        ]);
    }
}
