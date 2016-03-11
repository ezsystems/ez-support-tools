<?php

/**
 * File containing the HardwareSystemInfo class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Value;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Value for information about the hardware we are running on.
 */
class HardwareSystemInfo extends ValueObject implements SystemInfo
{
    /**
     * CPU type.
     *
     * @var string
     */
    public $cpuType;

    /**
     * CPU speed.
     *
     * @var string
     */
    public $cpuSpeed;

    /**
     * CPU count.
     *
     * @var int
     */
    public $cpuCount;

    /**
     * Memory size.
     *
     * @var float
     */
    public $memorySize;
}
