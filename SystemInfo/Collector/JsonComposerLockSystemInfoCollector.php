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
        $lockData = json_decode(file_get_contents($this->lockFile), true);
        foreach ($lockData['packages'] as $packageData) {
            $packages[$packageData['name']] = new Value\ComposerPackage([
                'name' => $packageData['name'],
                'version' => $packageData['version'],
                'dateTime' => isset($packageData['time']) ? new \DateTime($packageData['time']) : null,
                'homepage' => isset($packageData['homepage']) ? $packageData['homepage'] : '',
                'reference' => isset($packageData['source']) ? $packageData['source']['reference'] : null,
            ]);

            if (isset($lockData['stability-flags'][$packageData['name']])) {
                $stabilityFlag = (int)$lockData['stability-flags'][$packageData['name']];

                if (isset($this->stabilities[$stabilityFlag])) {
                    $packages[$packageData['name']]->stability = $this->stabilities[$stabilityFlag];
                }
            }
        }

        ksort($packages, SORT_FLAG_CASE | SORT_STRING);

        return new Value\ComposerSystemInfo([
            'packages' => $packages,
            'minimumStability' => isset($lockData['minimum-stability']) ? $lockData['minimum-stability'] : null,
        ]);
    }
}
