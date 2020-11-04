<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Collector;

use EzSystems\EzPlatformCoreBundle\EzPlatformCoreBundle;
use EzSystems\EzSupportToolsBundle\SystemInfo\Exception\ComposerLockFileNotFoundException;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\ComposerPackage;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\IbexaSystemInfo;
use DateTime;

/**
 * Collects information about the Ibexa installation.
 *
 * @internal This class will greatly change in the future and should not be used as an API, planned:
 *           - Get most of this information off updates.ez.no
 *           - Probably run this as a nightly cronjob to gather summary info
 *           - Be able to provide warnings to admins when something (config/system setup) is not optimal
 *           - Be able to give information if important updates are available to the installation
 *           - Or be able to tell if installation is greatly outdated
 *           - Be able to give heads up when installation is approaching its End of Life.
 */
class IbexaSystemInfoCollector implements SystemInfoCollector
{
    /**
     * Estimated release dates for given releases.
     *
     * Mainly for usage for trial to calculate TTL expiry.
     */
    const RELEASES = [
        '2.5' => '2019-03-29T16:59:59+00:00',
        '3.0' => '2020-04-02T23:59:59+00:00',
        '3.1' => '2020-07-15T23:59:59+00:00',
        '3.2' => '2020-10-23T23:59:59+00:00',
        '3.3' => '2020-12-30T23:59:59+00:00', // Estimate at time of writing
    ];

    /**
     * Dates for when releases are considered End of Maintenance.
     *
     * Open source releases are considered End of Life when this date is reached.
     *
     * @Note: Only Enterprise/Commerce installations receive fixes for security
     *        issues before the issues are disclosed. Also, be aware the link
     *        below is covering Enterprise/Commerce releases, length of
     *        maintenance for LTS releases may not be as long for open source
     *        releases as it depends on community maintenance efforts.
     *
     * @see: https://support.ibexa.co/Public/Service-Life
     */
    const EOM = [
        '2.5' => '2022-03-29T23:59:59+00:00',
        '3.0' => '2020-07-10T23:59:59+00:00',
        '3.1' => '2020-11-30T23:59:59+00:00',
        '3.2' => '2021-02-28T23:59:59+00:00',
        '3.3' => '2021-04-30T23:59:59+00:00', // Estimate at time of writing
    ];

    /**
     * Dates for when Enterprise/Commerce installations are considered End of Life.
     *
     * Meaning, when they stop receiving security fixes and support.
     *
     * @see: https://support.ibexa.co/Public/Service-Life
     */
    const EOL = [
        '2.5' => '2024-03-29T23:59:59+00:00',
        '3.0' => '2020-08-31T23:59:59+00:00',
        '3.1' => '2021-01-30T23:59:59+00:00',
        '3.2' => '2021-04-30T23:59:59+00:00',
        '3.3' => '2021-06-30T23:59:59+00:00', // Estimate at time of writing
    ];

    /**
     * Vendors we watch for stability (and potentially more).
     */
    const PACKAGE_WATCH_REGEX = '/^(doctrine|ezsystems|silversolutions|symfony)\//';

    /**
     * Packages that identify installation as "Enterprise".
     */
    const ENTERPRISE_PACKAGES = [
        'ezsystems/ezplatform-page-builder',
        'ezsystems/flex-workflow',
        'ezsystems/landing-page-fieldtype-bundle',
    ];

    /**
     * Packages that identify installation as "Commerce".
     */
    const COMMERCE_PACKAGES = [
        'silversolutions/silver.e-shop',
        'ezsystems/ezcommerce-shop',
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
     * Collects information about the Ibexa distribution and version.
     *
     * @throws \Exception
     *
     * @return \EzSystems\EzSupportToolsBundle\SystemInfo\Value\IbexaSystemInfo
     */
    public function collect(): IbexaSystemInfo
    {
        $ibexa = new IbexaSystemInfo(['debug' => $this->debug, 'composerInfo' => $this->composerInfo]);
        if ($this->composerInfo === null) {
            return $ibexa;
        }

        $ibexa->release = EzPlatformCoreBundle::VERSION;
        // try to extract version number, but prepare for unexpected string
        [$majorVersion, $minorVersion] = array_pad(explode('.', $ibexa->release), 2, '');
        $ibexaRelease = "{$majorVersion}.{$minorVersion}";

        // In case someone switches from TTL to BUL, make sure we only identify installation as Trial if this is present,
        // as well as TTL packages
        $hasTTLComposerRepo = \in_array('https://updates.ez.no/ttl', $this->composerInfo->repositoryUrls);

        if ($package = $this->getFirstPackage(self::ENTERPRISE_PACKAGES)) {
            $ibexa->isEnterprise = true;
            $ibexa->isTrial = $hasTTLComposerRepo && $package->license === 'TTL-2.0';
            $ibexa->name = 'Ibexa Experience';
        }

        if ($package = $this->getFirstPackage(self::COMMERCE_PACKAGES)) {
            $ibexa->isCommerce = true;
            $ibexa->isTrial = $ibexa->isTrial || $hasTTLComposerRepo && $package->license === 'TTL-2.0';
            $ibexa->name = 'Ibexa Commerce';
        }

        if ($ibexa->isTrial && isset(self::RELEASES[$ibexaRelease])) {
            $months = (new DateTime(self::RELEASES[$ibexaRelease]))->diff(new DateTime())->m;
            $ibexa->isEndOfMaintenance = $months > 3;
            // @todo We need to detect this in a better way, this is temporary until some of the work described in class doc is done.
            $ibexa->isEndOfLife = $months > 6;
        } else {
            if (isset(self::EOM[$ibexaRelease])) {
                $ibexa->isEndOfMaintenance = strtotime(self::EOM[$ibexaRelease]) < time();
            }

            if (isset(self::EOL[$ibexaRelease])) {
                if (!$ibexa->isEnterprise) {
                    $ibexa->isEndOfLife = $ibexa->isEndOfMaintenance;
                } else {
                    $ibexa->isEndOfLife = strtotime(self::EOL[$ibexaRelease]) < time();
                }
            }
        }

        $ibexa->endOfMaintenanceDate = $this->getEOMDate($ibexaRelease);
        $ibexa->endOfLifeDate = $this->getEOLDate($ibexaRelease);
        $ibexa->stability = $this->getStability();

        return $ibexa;
    }

    /**
     * @throws \Exception
     */
    private function getEOMDate(string $ibexaRelease): ?\DateTime
    {
        return isset(self::EOM[$ibexaRelease]) ?
            new DateTime(self::EOM[$ibexaRelease]) :
            null;
    }

    /**
     * @throws \Exception
     */
    private function getEOLDate(string $ibexaRelease): ?\DateTime
    {
        return isset(self::EOL[$ibexaRelease]) ?
            new DateTime(self::EOL[$ibexaRelease]) :
            null;
    }

    private function getStability(): string
    {
        $stabilityFlags = array_flip(JsonComposerLockSystemInfoCollector::STABILITIES);

        // Root package stability
        $stabilityFlag = $this->composerInfo->minimumStability !== null ?
            $stabilityFlags[$this->composerInfo->minimumStability] :
            $stabilityFlags['stable'];

        // Check if any of the watched packages has lower stability than root
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

    private function getFirstPackage($packageNames): ?ComposerPackage
    {
        foreach ($packageNames as $packageName) {
            if (isset($this->composerInfo->packages[$packageName])) {
                return $this->composerInfo->packages[$packageName];
            }
        }

        return null;
    }
}
