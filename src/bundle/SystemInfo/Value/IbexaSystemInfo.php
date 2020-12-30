<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Value;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Value for information about the Ibexa installation.
 *
 * @internal This class will greatly change in the future and should not be used as an api.
 */
class IbexaSystemInfo extends ValueObject implements SystemInfo
{
    public const PRODUCT_NAME_OSS = 'Ibexa Open Source';
    public const PRODUCT_NAME_ENTERPRISE = 'Ibexa DXP';
    public const PRODUCT_NAME_VARIANTS = [
        'oss' => self::PRODUCT_NAME_OSS,
        'content' => 'Ibexa Content',
        'experience' => 'Ibexa Experience',
        'commerce' => self::PRODUCT_NAME_COMMERCE,
    ];

    // @deprecated: use PRODUCT_NAME_VARIANTS
    public const PRODUCT_NAME_COMMERCE = 'Ibexa Commerce';

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
    public $isEnterprise = false;

    /**
     * @var bool
     */
    public $isCommerce = false;

    /**
     * @var bool
     *
     * @uses $endOfMaintenanceDate
     */
    public $isEndOfMaintenance = true;

    /**
     * @var \DateTime EOM for the given release, if you have an Ibexa DXP / Enterpise susbscription.
     *
     * @see https://support.ibexa.co/Public/Service-Life
     */
    public $endOfMaintenanceDate;

    /**
     * @var bool
     *
     * @uses $endOfLifeDate
     */
    public $isEndOfLife = true;

    /**
     * @var \DateTime EOL for the given release, if you have an Ibexa DXP susbscription.
     *
     * @see https://support.ibexa.co/Public/Service-Life
     */
    public $endOfLifeDate;

    /**
     * @var bool
     */
    public $isTrial = false;

    /**
     * Lowest stability found in install (packages / minimumStability).
     *
     * @var string One of {@see \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\JsonComposerLockSystemInfoCollector::STABILITIES}.
     */
    public $lowestStability;

    /**
     * @deprecated Instead use $lowestStability.
     *
     * @var string One of {@see \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\JsonComposerLockSystemInfoCollector::STABILITIES}.
     */
    public $stability;

    /**
     * @deprecated Duplicates collected info on symfony
     *
     * @var bool
     */
    public $debug;

    /**
     * @deprecated This was duplication of collected info from Composer, now only contains key 'minimumStability'
     *
     * @var array|null
     */
    public $composerInfo;
}
