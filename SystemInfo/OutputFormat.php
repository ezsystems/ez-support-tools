<?php

/**
 * File containing the OutputFormat interface.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo;

interface OutputFormat
{
    /**
     * Format an array of collected information data, and return it as string.
     *
     * @param array $collectedInfo
     * @return string
     */
    public function format(array $collectedInfo);
}
