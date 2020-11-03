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
    /**
     * @var string
     */
    public $name = 'Ibexa Open Source';

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
     */
    public $isEndOfMaintenance = true;

    /**
     * @var \DateTime
     */
    public $endOfMaintenanceDate;

    /**
     * @var bool
     */
    public $isEndOfLife = true;

    /**
     * @var \DateTime
     */
    public $endOfLifeDate;

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
