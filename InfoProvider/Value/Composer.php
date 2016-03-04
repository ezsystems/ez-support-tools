<?php

/**
 * File containing the Composer class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\InfoProvider\Value;

use EzSystems\EzSupportToolsBundle\InfoProvider\Value;

/**
 * Value for information about Composer packages.
 */
class Composer extends Value
{
    /**
     * Packages.
     *
     * @var mixed
     */
    public $packages;
}
