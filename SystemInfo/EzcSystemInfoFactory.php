<?php

/**
 * File containing the EzcSystemInfoFactory class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo;

use ezcSystemInfo;

/**
 * Factory for zetacomponents/sysinfo. Used here to simplify testing.
 */
class EzcSystemInfoFactory
{
    public static function buildEzcSystemInfo()
    {
        return ezcSystemInfo::getInstance();
    }
}
