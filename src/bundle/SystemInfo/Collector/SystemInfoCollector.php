<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Collector;

use EzSystems\EzSupportToolsBundle\SystemInfo\Value;

/**
 * Collects system information.
 */
interface SystemInfoCollector
{
    /**
     * Collects system information.
     *
     * @return Value\SystemInfo
     */
    public function collect();
}
