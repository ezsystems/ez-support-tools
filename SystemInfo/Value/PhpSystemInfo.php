<?php

/**
 * File containing the PhpSystemInfo class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Value;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Value for information about the PHP interpreter and accelerator (if any) we are using.
 */
class PhpSystemInfo extends ValueObject implements SystemInfo
{
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
}
