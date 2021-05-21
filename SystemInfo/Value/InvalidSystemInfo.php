<?php

/**
 * File containing the SymfonyKernelSystemInfo class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Value;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Invalid value for system info used in case of any errors occur while collecting data.
 */
class InvalidSystemInfo extends ValueObject implements SystemInfo
{
    /**
     * Error message shown in the System info tab
     *
     * @var string
     */
    public $errorMessage;
}
