<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Collector;

use EzSystems\EzPlatformCoreBundle\EzPlatformCoreBundle;
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
     * Packages that identify installation as "Content".
     */
    const CONTENT_PACKAGES = [
        'ezsystems/flex-workflow',
    ];

    /**
     * Packages that identify installation as "Experience".
     */
    const ENTERPRISE_PACKAGES = [
        'ezsystems/ezplatform-page-builder',
        'ezsystems/landing-page-fieldtype-bundle',
    ];

    /**
     * Packages that identify installation as "Commerce".
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
     * @var string Subscription json file with info on subscription downloaded by SubscriptionPlugin for composer.
     */
    private $subscriptionFile;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\JsonComposerLockSystemInfoCollector|\EzSystems\EzSupportToolsBundle\SystemInfo\Collector\SystemInfoCollector $composerCollector
     * @param bool $debug
     * @param string $subscriptionFile = 'vendor/ibexa/subscription.json'
     */
    public function __construct(SystemInfoCollector $composerCollector, $debug = false, $subscriptionFile = 'vendor/ibexa/subscription.json')
    {
        try {
            $this->composerInfo = $composerCollector->collect();
        } catch (ComposerLockFileNotFoundException $e) {
            // do nothing
        }

        $this->subscriptionFile = $subscriptionFile;
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
        $ibexa = new IbexaSystemInfo([
            'debug' => $this->debug,
        ]);

        $this->setSubscriptionInfo($ibexa);
        $this->setReleaseInfo($ibexa);
        $this->extractComposerInfo($ibexa);

        return $ibexa;
    }

    private function setSubscriptionInfo(IbexaSystemInfo $ibexa): void
    {
        if (!file_exists($this->subscriptionFile)) {
            return;
        }

        $subscriptionData = json_decode(file_get_contents($this->subscriptionFile), true);

        // NOTE: For non trials, the date from support.ibexa.co can be auto renewing
        //       typically updated a few weeks before expiring.
        $ibexa->subscriptionExpiryDate = new DateTime($subscriptionData['expiry']);

        $ibexa->isEnterprise = true;
        $ibexa->name = IbexaSystemInfo::PRODUCT_NAME_VARIANTS['content'];

        foreach ($subscriptionData['product_additions'] as $product) {
            // If some of the products is a trial, then currently mark whole install as trial
            $ibexa->isTrial = $ibexa->isTrial || $product['trial'];

            // Map older subscription names to new where needed.
            $identifier = in_array($product['name'], ['enterprise', 'platform']) ? 'experience' : $product['name'];

            // Detect product name using subscription info product identifier
            if (!$ibexa->isCommerce && $identifier !== 'content') {
                $ibexa->name = IbexaSystemInfo::PRODUCT_NAME_VARIANTS[$identifier];

                $ibexa->isCommerce = $identifier === 'commerce';
            }

            $ibexa->subscriptionProducts[] = $identifier;
        }
    }

    private function setReleaseInfo(IbexaSystemInfo $ibexa): void
    {
        $ibexa->release = EzPlatformCoreBundle::VERSION;
        // try to extract version number, but prepare for unexpected string
        [$majorVersion, $minorVersion] = array_pad(explode('.', $ibexa->release), 2, '');
        $ibexaRelease = "{$majorVersion}.{$minorVersion}";

        if (isset(self::EOM[$ibexaRelease])) {
            $ibexa->isEndOfMaintenance = strtotime(self::EOM[$ibexaRelease]) < time();
        }

        if (isset(self::EOL[$ibexaRelease])) {
            $ibexa->isEndOfLife = strtotime(self::EOL[$ibexaRelease]) < time();
        }

        $ibexa->endOfMaintenanceDate = $this->getEOMDate($ibexaRelease);
        $ibexa->endOfLifeDate = $this->getEOLDate($ibexaRelease);
    }

    private function extractComposerInfo(IbexaSystemInfo $ibexa): void
    {
        if ($this->composerInfo === null) {
            return;
        }

        // BC (deprecated property)
        $ibexa->composerInfo = ['minimumStability' => $this->composerInfo->minimumStability];

        $ibexa->shouldHaveSubscription = self::hasPackage(
            $this->composerInfo,
            array_merge(self::ENTERPRISE_PACKAGES, self::COMMERCE_PACKAGES)
        );
        $ibexa->stability = $ibexa->lowestStability = self::getStability($this->composerInfo);
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

    private static function getStability(ComposerSystemInfo $composerInfo): string
    {
        $stabilityFlags = array_flip(JsonComposerLockSystemInfoCollector::STABILITIES);

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

        return JsonComposerLockSystemInfoCollector::STABILITIES[$stabilityFlag];
    }

    private static function hasPackage(ComposerSystemInfo $composerInfo, array $packageNames): bool
    {
        foreach ($packageNames as $packageName) {
            if (isset($composerInfo->packages[$packageName])) {
                return true;
            }
        }

        return false;
    }
}
