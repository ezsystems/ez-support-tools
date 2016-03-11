<?php

/**
 * File containing the EzcHardwareSystemInfoCollector class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Collector;

use ezcSystemInfo;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value;

/**
 * Collects hardware system information using zetacomponents/sysinfo.
 */
class EzcHardwareSystemInfoCollector implements SystemInfoCollector
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
     * Builds information about the hardware eZ Platform is installed on.
     *  - cpu information
     *  - memory size
     *
     * @return Value\HardwareSystemInfo
     */
    public function build()
    {
        return new Value\HardwareSystemInfo([
            'cpuType' => $this->ezcSystemInfo->cpuType,
            'cpuSpeed' => $this->ezcSystemInfo->cpuSpeed,
            'cpuCount' => $this->ezcSystemInfo->cpuCount,
            'memorySize' => $this->ezcSystemInfo->memorySize,
        ]);
    }
}
