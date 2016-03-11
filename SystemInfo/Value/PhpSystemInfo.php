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
     * Example: 5.6.7-1
     *
     * @var string
     */
    public $version;

    /**
     * True if an accelerator is enabled.
     *
     * @var bool
     */
    public $acceleratorEnabled;

    /**
     * PHP accelerator name, or null if no accelerator is enabled.
     *
     * Example: Zend OPcache
     *
     * @var string|null
     */
    public $acceleratorName;

    /**
     * PHP accelerator URL, or null if no accelerator is enabled.
     *
     * Example: http://www.php.net/opcache
     *
     * @var string|null
     */
    public $acceleratorURL;

    /**
     * PHP accelerator version, or null if no accelerator is enabled.
     *
     * Example: 7.0.4-devFE
     *
     * @var string|null
     */
    public $acceleratorVersion;
}
