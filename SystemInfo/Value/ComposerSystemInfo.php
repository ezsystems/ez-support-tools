<?php

/**
 * File containing the ComposerSystemInfo class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Value;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Value for information about installed Composer packages.
 */
class ComposerSystemInfo extends ValueObject implements SystemInfo
{
    /**
     * Packages.
     *
     * A hash of composer package names and ComposerPackage values, or null if packages cannot be read.
     *
     * @var ComposerPackage[]|null
     */
    public $packages;

    /**
     * Minimum stability, as read from composer.lock.
     *
     * One of: stable, RC, beta, alpha, dev, or null if not set.
     *
     * @var string|null
     */
    public $minimumStability;

    /**
     * Additional Composer repository urls used.
     *
     * Will contain urls used so would be possible to know if bul or ttl packages are used for instance.
     *
     * @var string[]
     */
    public $repositoryUrls = [];
}
