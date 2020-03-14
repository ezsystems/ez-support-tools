<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzSupportToolsBundle\SystemInfo\Collector;

use EzSystems\EzSupportToolsBundle\SystemInfo\EzcSystemInfoWrapper;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value;

/**
 * Collects PHP information using zetacomponents/sysinfo.
 */
class EzcPhpSystemInfoCollector implements SystemInfoCollector
{
    /**
     * @var \EzSystems\EzSupportToolsBundle\SystemInfo\EzcSystemInfoWrapper
     */
    private $ezcSystemInfo;

    public function __construct(EzcSystemInfoWrapper $ezcSystemInfo)
    {
        $this->ezcSystemInfo = $ezcSystemInfo;
    }

    /**
     * Collects information about the PHP installation eZ Platform is using.
     *  - php version
     *  - php accelerator info.
     *
     * @return Value\PhpSystemInfo
     */
    public function collect()
    {
        $properties = [
            'version' => PHP_VERSION,
            'acceleratorEnabled' => false,
        ];

        if ($this->ezcSystemInfo->phpAccelerator) {
            $properties = array_merge(
                $properties,
                [
                    'acceleratorEnabled' => $this->ezcSystemInfo->phpAccelerator->isEnabled,
                    'acceleratorName' => $this->ezcSystemInfo->phpAccelerator->name,
                    'acceleratorURL' => $this->ezcSystemInfo->phpAccelerator->url,
                    'acceleratorVersion' => $this->ezcSystemInfo->phpAccelerator->versionString,
                ]
            );
        }

        return new Value\PhpSystemInfo($properties);
    }
}
