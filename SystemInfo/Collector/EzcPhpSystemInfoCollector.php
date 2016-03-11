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
        $properties = [
            'version' => phpversion(),
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
