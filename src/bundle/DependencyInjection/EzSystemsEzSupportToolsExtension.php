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

    private function getPoweredByName(ContainerBuilder $container, ?string $release): string
    {
        $vendor = $container->getParameter('kernel.project_dir') . '/vendor/';

        // Autodetect product name
        $name = self::getNameBySubscriptionInfo($vendor . 'ibexa/subscription.json');
        if (empty($name)) {
            // Fallback to detect name by packages
            $name = self::getNameByPackages($vendor);
        }

        if ($release === 'major') {
            $name .= ' v' . (int)EzPlatformCoreBundle::VERSION;
        } elseif ($release === 'minor') {
            $version = explode('.', EzPlatformCoreBundle::VERSION);
            $name .= ' v' . $version[0] . '.' . $version[1];
        }

        return $name;
    }

    private static function getNameBySubscriptionInfo(string $file): ?string
    {
        if (!file_exists($file)) {
            return null;
        }

        $subscriptionData = json_decode(file_get_contents($file), true);
        $name = IbexaSystemInfo::PRODUCT_NAME_VARIANTS['content'];
        foreach ($subscriptionData['product_additions'] as $product) {
            // Map older subscription names to new where needed.
            $identifier = in_array($product['name'], ['enterprise', 'platform']) ? 'experience' : $product['name'];
            if ($identifier !== 'content') {
                $name = IbexaSystemInfo::PRODUCT_NAME_VARIANTS[$identifier];
            }

            // Break out if highest package (commerce) was detected
            if ($identifier === 'commerce') {
                break;
            }
        }

        return $name;
    }

    private static function getNameByPackages(string $vendor): string
    {
        if (is_dir($vendor . IbexaSystemInfoCollector::COMMERCE_PACKAGES[0])) {
            $name = IbexaSystemInfo::PRODUCT_NAME_VARIANTS['commerce'];
        } elseif (is_dir($vendor . IbexaSystemInfoCollector::ENTERPRISE_PACKAGES[0])) {
            $name = IbexaSystemInfo::PRODUCT_NAME_VARIANTS['experience'];
        } elseif (is_dir($vendor . IbexaSystemInfoCollector::CONTENT_PACKAGES[0])) {
            $name = IbexaSystemInfo::PRODUCT_NAME_VARIANTS['content'];
        } else {
            $name = IbexaSystemInfo::PRODUCT_NAME_OSS;
        }

        return $name;
    }
}
