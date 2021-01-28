<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Collector;

use EzSystems\EzSupportToolsBundle\SystemInfo\Exception\ComposerFileValidationException;
use EzSystems\EzSupportToolsBundle\SystemInfo\Exception\ComposerLockFileNotFoundException;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\ComposerSystemInfo;
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
    public const RELEASES = [
        '2.5' => '2019-03-29T16:59:59+00:00',
        '3.0' => '2020-04-02T23:59:59+00:00',
        '3.1' => '2020-07-06T23:59:59+00:00', // Estimate at time of writing
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
    public const EOM = [
        '2.5' => '2022-03-29T23:59:59+00:00',
        '3.0' => '2020-07-10T23:59:59+00:00',
        '3.1' => '2020-09-30T23:59:59+00:00', // Estimate at time of writing
    ];

    /**
     * Dates for when Enterprise/Commerce installations are considered End of Life.
     *
     * Meaning, when they stop receiving security fixes and support.
     *
     * @see: https://support.ibexa.co/Public/Service-Life
     */
    public const EOL = [
        '2.5' => '2024-03-29T23:59:59+00:00',
        '3.0' => '2020-08-31T23:59:59+00:00',
        '3.1' => '2020-11-30T23:59:59+00:00', // Estimate at time of writing


    ];

    /**
     * Vendors we watch for stability (and potentially more).
     */
    public const PACKAGE_WATCH_REGEX = '/^(doctrine|ezsystems|silversolutions|symfony)\//';

    /**
     * Packages that identify installation as "Content".
     */
    public const CONTENT_PACKAGES = [
        'ezsystems/ezplatform-workflow',
    ];

    public const EXPERIENCE_PACKAGES = [
        'ezsystems/ezplatform-page-builder',
        'ezsystems/landing-page-fieldtype-bundle',
    ];

    /**
     * Packages that identify installation as "Enterprise".
     *
     * @deprecated since Ibexa DXP 3.3. Rely either on <code>IbexaSystemInfoCollector::EXPERIENCE_PACKAGES</code>
     * or <code>IbexaSystemInfoCollector::CONTENT_PACKAGES</code>.
     */
    public const ENTERPRISE_PACKAGES = [
        'ezsystems/ezplatform-page-builder',
        'ezsystems/flex-workflow',
        'ezsystems/landing-page-fieldtype-bundle',
    ];

    /**
     * Packages that identify installation as "Commerce".
     */
    public const COMMERCE_PACKAGES = [
        'ezsystems/ezcommerce-transaction',
    ];

    /**
     * @var \EzSystems\EzSupportToolsBundle\SystemInfo\Value\ComposerSystemInfo|null
     */
    private $composerInfo;

    /**
     * @var bool
     */
    private $debug;

    /** @var string */
    private $kernelProjectDir;

    /**
     * @param \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\JsonComposerLockSystemInfoCollector|\EzSystems\EzSupportToolsBundle\SystemInfo\Collector\SystemInfoCollector $composerCollector
     * @param bool $debug
     */
    public function __construct(
        SystemInfoCollector $composerCollector,
        string $kernelProjectDir,
        bool $debug = false
    ) {
        try {
            $this->composerInfo = $composerCollector->collect();
        } catch (ComposerLockFileNotFoundException | ComposerFileValidationException $e) {
            // do nothing
        }
        $this->debug = $debug;
        $this->kernelProjectDir = $kernelProjectDir;
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
        $vendorDir = sprintf('%s/vendor/', $this->kernelProjectDir);

        $ibexa = new IbexaSystemInfo([
            'debug' => $this->debug,
            'name' => EzSystemsEzSupportToolsExtension::getNameByPackages($vendorDir),
        ]);

        $this->setReleaseInfo($ibexa);
        $this->extractComposerInfo($ibexa);

        return $ibexa;
    }

        $ez->release = EzPlatformCoreBundle::VERSION;

        // In case someone switches from TTL to BUL, make sure we only identify installation as Trial if this is present,
        // as well as TTL packages
        $hasTTLComposerRepo = \in_array('https://updates.ez.no/ttl', $this->composerInfo->repositoryUrls);

        if ($package = $this->getFirstPackage(self::ENTERPISE_PACKAGES)) {
            $ez->isEnterpise = true;
            $ez->isTrial = $hasTTLComposerRepo && $package->license === 'TTL-2.0';
            $ez->name = EzSystemInfo::PRODUCT_NAME_ENTERPISE;
        }

        if ($package = $this->getFirstPackage(self::COMMERCE_PACKAGES)) {
            $ez->isCommerce = true;
            $ez->isTrial = $ez->isTrial || $hasTTLComposerRepo && $package->license === 'TTL-2.0';
            $ez->name = EzSystemInfo::PRODUCT_NAME_COMMERCE;
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

        // BC (deprecated property)
        $ibexa->composerInfo = ['minimumStability' => $this->composerInfo->minimumStability];

        $dxpPackages = array_merge(
            self::CONTENT_PACKAGES,
            self::EXPERIENCE_PACKAGES,
            self::COMMERCE_PACKAGES
        );
        $ibexa->isEnterprise = self::hasAnyPackage($this->composerInfo, $dxpPackages);
        $ibexa->stability = $ibexa->lowestStability = self::getStability($this->composerInfo);
    }

    /**
     * @throws \Exception
     */
    private function getEOMDate(string $ibexaRelease): ?DateTime
    {
        return isset(self::EOM[$ibexaRelease]) ?
            new DateTime(self::EOM[$ibexaRelease]) :
            null;
    }

    /**
     * @throws \Exception
     */
    private function getEOLDate(string $ibexaRelease): ?DateTime
    {
        return isset(self::EOL[$ibexaRelease]) ?
            new DateTime(self::EOL[$ibexaRelease]) :
            null;
    }

    private static function getStability(ComposerSystemInfo $composerInfo): string
    {
        $stabilityFlags = array_flip(Stability::STABILITIES);

        // Root package stability
        $stabilityFlag = $composerInfo->minimumStability !== null ?
            $stabilityFlags[$composerInfo->minimumStability] :
            $stabilityFlags['stable'];

        // Check if any of the watched packages has lower stability than root
        foreach ($composerInfo->packages as $name => $package) {
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

        return Stability::STABILITIES[$stabilityFlag];
    }

    private static function hasAnyPackage(
        ComposerSystemInfo $composerInfo,
        array $packageNames
    ): bool {
        foreach ($packageNames as $packageName) {
            if (isset($composerInfo->packages[$packageName])) {
                return true;
            }
        }

        return false;
    }
}
