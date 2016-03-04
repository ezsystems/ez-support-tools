<?php

/**
 * File containing the System class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\InfoProvider\Value;

use EzSystems\EzSupportToolsBundle\InfoProvider\Value;

/**
 * Value for information about the system this is running on.
 */
class System extends Value
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

    /**
     * PHP version.
     *
     * @var string
     */
    public $phpVersion;

    /**
     * PHP accelerator.
     *
     * @var mixed
     */
    public $phpAccelerator;

    /**
     * Database.
     *
     * @var mixed
     */
    public $database;
}
