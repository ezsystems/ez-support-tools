<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportToolsBundle\DependencyInjection;

use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\EzSystemInfoCollector;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\EzSystemInfo;
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
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('default_settings.yml');

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
        $vendor = $container->getParameter('kernel.root_dir') . '/../vendor/';
        if (is_dir($vendor . EzSystemInfoCollector::COMMERCE_PACKAGES[0])) {
            $name = EzSystemInfo::PRODUCT_NAME_COMMERCE;
        } elseif (is_dir($vendor . EzSystemInfoCollector::ENTERPISE_PACKAGES[0])) {
            $name = EzSystemInfo::PRODUCT_NAME_ENTERPISE;
        } else {
            $name = EzSystemInfo::PRODUCT_NAME_OSS;
        }

        // Unlike in 3.x there is no constant for version in 2.5, so while this looks hard coded it reflects composer
        // requirements for this package version
        if ($release === 'major') {
            $name .= ' v2';
        } elseif ($release === 'minor') {
            $name .= ' v2.5';
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

    public static function getNameByPackages(string $vendor): string
    {
        if (is_dir($vendor . IbexaSystemInfoCollector::COMMERCE_PACKAGES[0])) {
            $name = IbexaSystemInfo::PRODUCT_NAME_VARIANTS['commerce'];
        } elseif (is_dir($vendor . IbexaSystemInfoCollector::EXPERIENCE_PACKAGES[0])) {
            $name = IbexaSystemInfo::PRODUCT_NAME_VARIANTS['experience'];
        } elseif (is_dir($vendor . IbexaSystemInfoCollector::CONTENT_PACKAGES[0])) {
            $name = IbexaSystemInfo::PRODUCT_NAME_VARIANTS['content'];
        } else {
            $name = IbexaSystemInfo::PRODUCT_NAME_OSS;
        }

        return $name;
    }
}
