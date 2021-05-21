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
    const STABILITIES = [
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

    /**
     * @var string Composer json file with full path.
     */
    private $jsonFile;

    /**
     * @var Value\ComposerSystemInfo The collected value, cached in case info is collected by other collectors.
     */
    private $value;

    public function __construct($lockFile, $jsonFile)
    {
        $this->lockFile = $lockFile;
        $this->jsonFile = $jsonFile;
    }

    /**
     * Collects information about installed composer packages.
     *
     * @throws Exception\ComposerLockFileNotFoundException if the composer.lock file was not found.
     * @throws Exception\ComposerFileValidationException if composer.lock of composer.json are not valid.
     *
     * @return Value\ComposerSystemInfo
     */
    public function collect()
    {
        if ($this->value) {
            return $this->value;
        }

        if (!file_exists($this->lockFile)) {
            throw new Exception\ComposerLockFileNotFoundException($this->lockFile);
        }

        if (!file_exists($this->jsonFile)) {
            throw new Exception\ComposerJsonFileNotFoundException($this->jsonFile);
        }

        $lockData = json_decode(file_get_contents($this->lockFile), true);
        $jsonData = json_decode(file_get_contents($this->jsonFile), true);

        if (!$lockData) {
            throw new Exception\ComposerFileValidationException($this->lockFile);
        }

        if (!$jsonData) {
            throw new Exception\ComposerFileValidationException($this->jsonFile);
        }

        return $this->value = new Value\ComposerSystemInfo([
            'packages' => $this->extractPackages($lockData),
            'repositoryUrls' => $this->extractRepositoryUrls($jsonData),
            'minimumStability' => isset($lockData['minimum-stability']) ? $lockData['minimum-stability'] : null,
        ]);
    }

    /**
     * @param array $lockData
     *
     * @return \EzSystems\EzSupportToolsBundle\SystemInfo\Value\ComposerPackage[]
     */
    private function extractPackages(array $lockData): array
    {
        $packages = [];
        $rootAliases = [];
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

                if (isset(self::STABILITIES[$stabilityFlag])) {
                    $package->stability = self::STABILITIES[$stabilityFlag];
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

        return $packages;
    }

    /**
     * @param array $jsonData
     *
     * @return string[]
     */
    private function extractRepositoryUrls(array $jsonData): array
    {
        $repos = [];
        if (!isset($jsonData['repositories'])) {
            return $repos;
        }

        foreach ($jsonData['repositories'] as $composerRepository) {
            if (empty($composerRepository['type']) || $composerRepository['type'] !== 'composer') {
                continue;
            }

            if (empty($composerRepository['url'])) {
                continue;
            }

            $repos[] = $composerRepository['url'];
        }

        return $repos;
    }

    /**
     * @param Value\ComposerPackage $package
     */
    private static function setNormalizedVersion(Value\ComposerPackage $package): void
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
