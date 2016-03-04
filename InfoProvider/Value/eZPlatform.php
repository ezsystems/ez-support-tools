<?php

/**
 * File containing the eZPlatform class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\InfoProvider\Value;

use EzSystems\EzSupportToolsBundle\InfoProvider\Value;

/**
 * Value for information about eZ Platform.
 */
class eZPlatform extends Value
{
    /**
     * eZ Platform version.
     *
     * @var string
     */
    public $eZPlatformVersion;

    /**
     * Symfony version.
     *
     * @var string
     */
    public $symfonyVersion;

    /**
     * Bundles.
     *
     * @var mixed
     */
    public $bundles;
}
