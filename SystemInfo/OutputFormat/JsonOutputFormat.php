<?php

/**
 * File containing the JsonOutputFormat class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\OutputFormat;

use EzSystems\EzSupportToolsBundle\SystemInfo\OutputFormat as SystemInfoOutputFormat;

/**
 * Collects hardware system information using zetacomponents/sysinfo.
 */
class JsonOutputFormat implements SystemInfoOutputFormat
{
    public function format(array $collectedInfo)
    {
        return json_encode( $collectedInfo, JSON_PRETTY_PRINT);
    }
}
