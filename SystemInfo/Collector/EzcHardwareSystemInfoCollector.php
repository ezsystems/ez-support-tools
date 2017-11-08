<?php

/**
 * File containing the EzcHardwareSystemInfoCollector class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Collector;

use EzSystems\EzSupportToolsBundle\SystemInfo\EzcSystemInfoWrapper;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value;

/**
 * Collects hardware system information using zetacomponents/sysinfo.
 */
class EzcHardwareSystemInfoCollector implements SystemInfoCollector
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
     * Collects information about the hardware eZ Platform is installed on.
     *  - cpu information
     *  - memory size.
     *
     * @return Value\HardwareSystemInfo
     */
    public function collect()
    {
        return new Value\HardwareSystemInfo([
            'cpuType' => $this->ezcSystemInfo->cpuType,
            'cpuSpeed' => $this->ezcSystemInfo->cpuSpeed,
            'cpuCount' => $this->ezcSystemInfo->cpuCount,
            'memorySize' => $this->ezcSystemInfo->memorySize,
        ]);
    }
}
