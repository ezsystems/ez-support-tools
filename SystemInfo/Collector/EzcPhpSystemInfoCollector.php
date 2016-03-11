<?php

/**
 * File containing the EzcPhpSystemInfoCollector class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Collector;

use ezcSystemInfo;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value;

/**
 * Collects PHP information using zetacomponents/sysinfo.
 */
class EzcPhpSystemInfoCollector implements SystemInfoCollector
{
    /**
     * ezcSystemInfo from eZ Components
     *
     * @var \ezcSystemInfo
     */
    private $ezcSystemInfo;

    public function __construct(ezcSystemInfo $ezcSystemInfo)
    {
        $this->ezcSystemInfo = $ezcSystemInfo;
    }

    /**
     * Builds information about the PHP installation eZ Platform is using.
     *  - php version
     *  - php accelerator info
     *
     * @return Value\PhpSystemInfo
     */
    public function build()
    {
        $accelerator = false;
        if ($this->ezcSystemInfo->phpAccelerator) {
            $accelerator = [
                'name' => $this->ezcSystemInfo->phpAccelerator->name,
                'url' => $this->ezcSystemInfo->phpAccelerator->url,
                'enabled' => $this->ezcSystemInfo->phpAccelerator->isEnabled,
                'versionString' => $this->ezcSystemInfo->phpAccelerator->versionString,
            ];
        }

        return new Value\PhpSystemInfo([
            'phpVersion' => phpversion(),
            'phpAccelerator' => $accelerator,
        ]);
    }
}
