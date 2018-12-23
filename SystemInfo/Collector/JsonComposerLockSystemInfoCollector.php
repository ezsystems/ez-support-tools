<?php

/**
 * File containing the JsonComposerLockSystemInfoCollector class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Collector;

use EzSystems\EzSupportToolsBundle\SystemInfo\Exception;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value;

/**
 * Collects information about installed Composer packages, by reading json from composer.lock.
 */
class JsonComposerLockSystemInfoCollector implements SystemInfoCollector
{
    /**
     * @var array Hash of stability constant values to human readable stabilities, see Composer\Package\BasePackage.
     *
     * Needed as long as we don't want to depend on Composer.
     */
    private $stabilities = [
        0 => 'stable',
        5 => 'RC',
        10 => 'beta',
        15 => 'alpha',
        20 => 'dev',
    ];

    /**
     * @var string Composer lock file with full path.
     */
    private $lockFile;

    public function __construct($lockFile)
    {
        $this->lockFile = $lockFile;
    }

    /**
     * Collects information about installed composer packages.
     *
     * @throws Exception\ComposerLockFileNotFoundException if the composer.lock file was not found.
     *
     * @return Value\ComposerSystemInfo
     */
    public function collect()
    {
        if (!file_exists($this->lockFile)) {
            throw new Exception\ComposerLockFileNotFoundException($this->lockFile);
        }

        $packages = [];
        $rootAliases = [];
        $lockData = json_decode(file_get_contents($this->lockFile), true);
        foreach ($lockData['aliases'] as $alias) {
            $rootAliases[$alias['package']] = $alias['alias'];
        }

        foreach ($lockData['packages'] as $packageData) {
            $package = new Value\ComposerPackage([
                'name' => $packageData['name'],
                'branch' => $packageData['version'],
                'dateTime' => isset($packageData['time']) ? new \DateTime($packageData['time']) : null,
                'homepage' => isset($packageData['homepage']) ? $packageData['homepage'] : '',
                'reference' => isset($packageData['source']) ? $packageData['source']['reference'] : null,
                'license' => isset($packageData['license'][0]) ? $packageData['license'][0] : null,
            ]);

            if (isset($lockData['stability-flags'][$package->name])) {
                $stabilityFlag = (int)$lockData['stability-flags'][$package->name];

                if (isset($this->stabilities[$stabilityFlag])) {
                    $package->stability = $this->stabilities[$stabilityFlag];
                }
            }

            if (isset($rootAliases[$package->name])) {
                $package->alias = $rootAliases[$package->name];
            } elseif (isset($packageData['extra']['branch-alias'][$package->branch])) {
                $package->alias = $packageData['extra']['branch-alias'][$package->branch];
            }

            self::setNormalizedVersion($package);

            $packages[$packageData['name']] = $package;
        }

        ksort($packages, SORT_FLAG_CASE | SORT_STRING);

        return new Value\ComposerSystemInfo([
            'packages' => $packages,
            'minimumStability' => isset($lockData['minimum-stability']) ? $lockData['minimum-stability'] : null,
        ]);
    }

    private static function setNormalizedVersion(Value\ComposerPackage $package)
    {
        $version = $package->alias ? $package->alias : $package->branch;
        if ($version[0] === 'v') {
            $version = substr($version, 1);
        }

        if (strpos($version, 'x-dev')) {
            $version = str_replace('-dev', '', $version);
            $package->stability = 'dev';
        }

        $package->version = $version;
    }
}
