<?php

/**
 * File containing the EzSystemInfo class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Value;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Value for information about the eZ installation.
 *
 * @internal This class will greatly change in the future and should not be used as an api.
 */
class EzSystemInfo extends ValueObject implements SystemInfo
{
    public const PRODUCT_NAME_OSS = 'eZ Platform';
    public const PRODUCT_NAME_ENTERPISE = 'eZ Platform Enterprise';
    // NOTE: These are specific product names, for 3.2+ thay are all collectivly refered to by PRODUCT_NAME_ENTERPISE as well.
    public const PRODUCT_NAME_COMMERCE = 'eZ Commerce';

    /**
     * @var string
     */
    public $name = self::PRODUCT_NAME_OSS;

    /**
     * @var string|null Either string like '2.5' or null if not detected.
     */
    public $release;

    /**
     * @var bool
     */
    public $isEnterpise = false;

    /**
     * @var bool
     */
    public $isCommerce = false;

    /**
     * @var bool
     */
    public $isEndOfMaintenance = true;

    /**
     * @var bool
     */
    public $isEndOfLife = true;

    /**
     * @var bool
     */
    public $isTrial = false;

    /**
     * @var string One of {@see \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\JsonComposerLockSystemInfoCollector::STABILITIES}.
     */
    public $stability;

    /**
     * @var bool
     */
    public $debug;

    /**
     * @var \EzSystems\EzSupportToolsBundle\SystemInfo\Value\ComposerSystemInfo|null
     */
    public $composerInfo;
}
