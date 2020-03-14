<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzSupportToolsBundle\SystemInfo\Value;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Value for information about a Composer package.
 */
class ComposerPackage extends ValueObject implements SystemInfo
{
    /**
     * Name.
     *
     * Example: ezsystems/ezpublish-kernel
     *
     * @var string
     */
    public $name;

    /**
     * Tag or Branch.
     *
     * Examples: v2.7.10, dev-master
     *
     * @var string
     */
    public $branch;

    /**
     * Alias.
     *
     * Examples: v2.7.x-dev
     *
     * @var string|null
     */
    public $alias = null;

    /**
     * Normilized version number.
     *
     * Uses root-alias or package-aliases if present to try to provide a version number even on branches.
     *
     * Examples: 2.7.10, 2.8.x
     *
     * @var string
     */
    public $version;

    /**
     * License string.
     *
     * Only contains the first license on the package, if set.
     *
     * Examples: 'TTL-2.0', 'GPL-2.0-only'
     *
     * @var string|null
     */
    public $license = null;

    /**
     * Stability.
     *
     * One of: stable, RC, beta, alpha, dev, or null if not set.
     *
     * @var string|null
     */
    public $stability;

    /**
     * Date and time.
     *
     * @var \DateTime
     */
    public $dateTime;

    /**
     * Homepage URL, or null of not set.
     *
     * Example: https://symfony.com
     *
     * @var string|null
     */
    public $homepage;

    /**
     * Reference.
     *
     * Example: 9a3b6bf6ebee49370aaf15abc1bdeb4b1986a67d
     *
     * @var string
     */
    public $reference;
}
