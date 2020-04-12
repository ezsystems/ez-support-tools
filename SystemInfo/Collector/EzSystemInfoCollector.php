<?php

/**
 * File containing the EzcHardwareSystemInfoCollector class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Collector;

use EzSystems\EzSupportToolsBundle\SystemInfo\Exception\ComposerLockFileNotFoundException;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\EzSystemInfo;
use DateTime;

/**
 * Collects information about the eZ installation.
 *
 * @internal This class will greatly change in the future and should not be used as a api, planned:
 *           - Get most of this information off updates.ez.no
 *           - Probably run this as a nightly cronjob to gather summary info
 *           - Be able to provide warnings to admins when something (config/system setup) is not optimal
 *           - Be able to give information if important updates are avaiable to install
 *           - Or be able to tell if install is greatly outdated
 *           - Be able to give heads up when install is approaching end of life.
 */
class EzSystemInfoCollector implements SystemInfoCollector
{
    /**
     * Estimated release dates for given releases.
     *
     * Mainly for usage for trail to calculate TTL expiry.
     */
    const RELEASES = [
        '2.0' => '2017-12-20T23:59:59+00:00',
        '2.1' => '2018-03-20T23:59:59+00:00',
        '2.2' => '2018-06-20T23:59:59+00:00',
        '2.3' => '2018-09-20T23:59:59+00:00',
        '2.4' => '2018-12-20T23:59:59+00:00',
        '2.5' => '2019-03-29T16:59:59+00:00',
        '3.0' => '2020-04-02T23:59:59+00:00',
        '3.1' => '2020-07-06T23:59:59+00:00', // Estimate at time of writing
    ];

    /**
     * Dates for when releases are considered end of maintenance.
     *
     * Open source releases are considered end of life when this date ias reached.
     *
     * @Note: Only enterprise/commerce installs recives fixes for security
     *        issues before the issues are disclosed. Also be aware the link
     *        below is covering Enterprise/Commerce releases, lenght of
     *        maintenance for LTS releases may not be as long for open source
     *        releases as it depends on community maintenance efforts.
     *
     * @see: https://support.ez.no/Public/Service-Life
     */
    const EOM = [
        '2.0' => '2018-03-20T23:59:59+00:00',
        '2.1' => '2018-06-20T23:59:59+00:00',
        '2.2' => '2018-09-20T23:59:59+00:00',
        '2.3' => '2018-12-20T23:59:59+00:00',
        '2.4' => '2019-03-20T23:59:59+00:00',
        '2.5' => '2022-03-29T23:59:59+00:00',
        '3.0' => '2020-07-10T23:59:59+00:00',
        '3.1' => '2020-09-30T23:59:59+00:00', // Estimate at time of writing
    ];

    /**
     * Dates for when Enterprise/Commerce installs are considered end of life.
     *
     * Meaning when they stop reciving security fixes and support.
     *
     * @see: https://support.ez.no/Public/Service-Life
     */
    const EOL = [
        '2.0' => '2018-06-20T23:59:59+00:00',
        '2.1' => '2018-09-20T23:59:59+00:00',
        '2.2' => '2019-03-20T23:59:59+00:00', // Extended
        '2.3' => '2019-03-20T23:59:59+00:00',
        '2.4' => '2019-06-20T23:59:59+00:00',
        '2.5' => '2024-03-29T23:59:59+00:00',
        '3.0' => '2020-08-31T23:59:59+00:00',
        '3.1' => '2020-11-30T23:59:59+00:00', // Estimate at time of writing
    ];

    /**
     * Vendors we watch for stability (and potentially more).
     */
    const PACKAGE_WATCH_REGEX = '/^(doctrine|ezsystems|silversolutions|symfony)\//';

    /**
     * Packages that identifies install as Enterpirse install.
     */
    const ENTERPISE_PACKAGES = [
        'ezsystems/ezplatform-page-builder',
        'ezsystems/flex-workflow',
        'ezsystems/landing-page-fieldtype-bundle',
    ];

    /**
     * Packages that identifies install as Commerce install.
     */
    const COMMERCE_PACKAGES = [
        'ezsystems/ezcommerce-shop',
        'silversolutions/silver.e-shop',
    ];

    /**
     * @var \EzSystems\EzSupportToolsBundle\SystemInfo\Value\ComposerSystemInfo|null
     */
    private $composerInfo;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\JsonComposerLockSystemInfoCollector|\EzSystems\EzSupportToolsBundle\SystemInfo\Collector\SystemInfoCollector $composerCollector
     * @param bool $debug
     */
    public function __construct(SystemInfoCollector $composerCollector, $debug = false)
    {
        try {
            $this->composerInfo = $composerCollector->collect();
        } catch (ComposerLockFileNotFoundException $e) {
            // do nothing
        }
        $this->debug = $debug;
    }

    /**
     * Collects information about the eZ distrobution and version.
     *
     * @return \EzSystems\EzSupportToolsBundle\SystemInfo\Value\EzSystemInfo
     */
    public function collect()
    {
        $ez = new EzSystemInfo(['debug' => $this->debug, 'composerInfo' => $this->composerInfo]);
        if ($this->composerInfo === null) {
            return $ez;
        }

        // The most reliable way to get version is from kernel
        // future updates should make sure to detect when kernel version selector is wrong compare to other packages
        if (isset($this->composerInfo->packages['ezsystems/ezpublish-kernel'])) {
            $ez->release = (string)(((float)$this->composerInfo->packages['ezsystems/ezpublish-kernel']->version) - 5);
        }

        // In case someone switches from TTL to BUL, make sure we only identify install as Trial if this is present,
        // as well as TTL packages
        $hasTTLComposerRepo = \in_array('https://updates.ez.no/ttl', $this->composerInfo->repositoryUrls);

        if ($package = $this->getFirstPackage(self::ENTERPISE_PACKAGES)) {
            $ez->isEnterpise = true;
            $ez->isTrial = $hasTTLComposerRepo && $package->license === 'TTL-2.0';
            $ez->name = 'eZ Platform Enterprise';
        }

        if ($package = $this->getFirstPackage(self::COMMERCE_PACKAGES)) {
            $ez->isCommerce = true;
            $ez->isTrial = $ez->isTrial || $hasTTLComposerRepo && $package->license === 'TTL-2.0';
            $ez->name = 'eZ Commerce';
        }

        if ($ez->isTrial && isset(self::RELEASES[$ez->release])) {
            $months = (new DateTime(self::RELEASES[$ez->release]))->diff(new DateTime())->m;
            $ez->isEndOfMaintenance = $months > 3;
            // Temporary increased from 6 to 10.
            // @todo We need to detect this in a better way, this is temporary until some of the work described in class doc is done.
            $ez->isEndOfLife = $months > 12;
        } else {
            if (isset(self::EOM[$ez->release])) {
                $ez->isEndOfMaintenance = strtotime(self::EOM[$ez->release]) < time();
            }

            if (isset(self::EOL[$ez->release])) {
                if (!$ez->isEnterpise) {
                    $ez->isEndOfLife = $ez->isEndOfMaintenance;
                } else {
                    $ez->isEndOfLife = strtotime(self::EOL[$ez->release]) < time();
                }
            }
        }

        $ez->stability = $this->getStability();

        return $ez;
    }

    private function getStability()
    {
        $stabilityFlags = array_flip(JsonComposerLockSystemInfoCollector::STABILITIES);

        // Root package stability
        $stabilityFlag = $this->composerInfo->minimumStability !== null ?
            $stabilityFlags[$this->composerInfo->minimumStability] :
            $stabilityFlags['stable'];

        // Check if any of the watche packages has lower stability then root
        foreach ($this->composerInfo->packages as $name => $package) {
            if (!preg_match(self::PACKAGE_WATCH_REGEX, $name)) {
                continue;
            }

            if ($package->stability === 'stable' || $package->stability === null) {
                continue;
            }

            if ($stabilityFlags[$package->stability] > $stabilityFlag) {
                $stabilityFlag = $stabilityFlags[$package->stability];
            }
        }

        return JsonComposerLockSystemInfoCollector::STABILITIES[$stabilityFlag];
    }

    private function getFirstPackage($packageNames)
    {
        foreach ($packageNames as $packageName) {
            if (isset($this->composerInfo->packages[$packageName])) {
                return $this->composerInfo->packages[$packageName];
            }
        }
    }
}
