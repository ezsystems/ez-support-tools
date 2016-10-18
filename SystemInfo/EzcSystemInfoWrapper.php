<?php

/**
 * File containing the EzcSystemInfoWrapper class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo;

use ezcSystemInfo;
use ezcSystemInfoReaderCantScanOSException;

/**
 * This wraps zetacomponents/sysinfo, exposing its magic properties as public properties.
 * Used here to allow lazy loading.
 */
class EzcSystemInfoWrapper
{
    /** @var string */
    public $osType;

    /** @var string */
    public $osName;

    /** @var string */
    public $fileSystemType;

    /** @var integer */
    public $cpuCount;

    /** @var string */
    public $cpuType;

    /** @var float */
    public $cpuSpeed;

    /** @var integer */
    public $memorySize;

    /** @var string */
    public $lineSeparator;

    /** @var string */
    public $backupFileName;

    /** @var array */
    public $phpVersion;

    /** @var \ezcSystemInfoAccelerator */
    public $phpAccelerator;

    /** @var bool */
    public $isShellExecution;

    public function __construct()
    {
        try {
            $ezcSystemInfo = ezcSystemInfo::getInstance();
        } catch(ezcSystemInfoReaderCantScanOSException $e) {
            // Leave properties as null: https://github.com/zetacomponents/SystemInformation/pull/9
            return;
        }

        $this->osType = $ezcSystemInfo->osType;
        $this->osName = $ezcSystemInfo->osName;
        $this->fileSystemType = $ezcSystemInfo->fileSystemType;
        $this->cpuCount = $ezcSystemInfo->cpuCount;
        $this->cpuType = $ezcSystemInfo->cpuType;
        $this->cpuSpeed = $ezcSystemInfo->cpuSpeed;
        $this->memorySize = $ezcSystemInfo->memorySize;
        $this->lineSeparator = $ezcSystemInfo->lineSeparator;
        $this->backupFileName = $ezcSystemInfo->backupFileName;
        $this->phpVersion = $ezcSystemInfo->phpVersion;
        $this->phpAccelerator = $ezcSystemInfo->phpAccelerator;
        $this->isShellExecution = $ezcSystemInfo->isShellExecution;
    }
}
