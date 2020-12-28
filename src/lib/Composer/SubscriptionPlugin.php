<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\Event;
use Composer\Script\ScriptEvents;
use RuntimeException;

/**
 * Plugin for loading subscription info for hints in admin UI, for trial and expiry info when relevant.
 *
 * Will only register itself if it detects that the installation is configured with 'https://updates.ibexa.co' as composer repository.
 *
 * To debug events fired by Composer, use: COMPOSER_DEBUG_EVENTS=1 composer update
 */
class SubscriptionPlugin implements PluginInterface, EventSubscriberInterface
{
    public const DOWNLOAD_SUBSCRIPTION_CMD = 'ibexa:sync-subscription';

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * @var bool
     */
    private $hasUpdatesIbexaCoRepo = null;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        // Nothing to do
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        // Nothing to do
    }

    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::PRE_INSTALL_CMD => 'checkRepositoryConfig',
            ScriptEvents::PRE_UPDATE_CMD => 'checkRepositoryConfig',

            ScriptEvents::POST_INSTALL_CMD => 'downloadSubscriptionInfo',
            ScriptEvents::POST_UPDATE_CMD => 'downloadSubscriptionInfo',

            // Custom command in order to document what people should run if subscriptin info is missing
            self::DOWNLOAD_SUBSCRIPTION_CMD => 'downloadSubscriptionInfo',
        ];
    }

    public function checkRepositoryConfig(): bool
    {
        //  Make sure this is only run once
        if ($this->hasUpdatesIbexaCoRepo !== null) {
            return $this->hasUpdatesIbexaCoRepo;
        }

        $hasUpdatesIbexaCoRepo = false;
        foreach ($this->composer->getRepositoryManager()->getRepositories() as $repo) {
            $url = $repo->getRepoConfig()['url'] ?? null;
            if (strpos($url, 'https://updates.ibexa.co') !== false) {
                $hasUpdatesIbexaCoRepo = true;
            }

            if (strpos($url, 'https://updates.ez.no/') !== false) {
                $this->io->write("<warning>'updates.ez.no' is deprecated, use 'updates.ibexa.co' instead</warning>");
            } elseif (1 === preg_match('@^https://updates.ibexa.co/[^/]+@', $url)) {
                $this->io->write("<warning>Ibexa update repository should be configured as 'https://updates.ibexa.co'</warning>");
            }
        }

        return $this->hasUpdatesIbexaCoRepo = $hasUpdatesIbexaCoRepo;
    }

    public function downloadSubscriptionInfo(Event $event): void
    {
        // Skip if we don't have updates.ibexa.co repo yet, as we then probably also don't have AUTH config for it
        if (!$this->checkRepositoryConfig()) {
            return;
        }

        $this->io->write(
            '<info>Synchronizing subscription info from: https://updates.ibexa.co/subscription</>',
            true,
            // If directly called make sure there is some output to console
            $event->getName() === self::DOWNLOAD_SUBSCRIPTION_CMD ? IOInterface::NORMAL : IOInterface::VERBOSE
        );

        $ibexaVendorDir = 'vendor/ibexa';
        if (!is_dir($ibexaVendorDir) && !mkdir($ibexaVendorDir) && !is_dir($ibexaVendorDir)) {
            throw new RuntimeException(
                sprintf('Directory "%s" was not created', $ibexaVendorDir)
            );
        }

        // 2.0 API first, allows async download
        if (method_exists($this->composer, 'getLoop')) {
            $this->composer->getLoop()->getHttpDownloader()->addCopy(
                'https://updates.ibexa.co/subscription',
                'vendor/ibexa/subscription.json'
            );
        } else {
            $rfs = Factory::createRemoteFilesystem($this->io, $this->composer->getConfig());
            $rfs->copy(
                'updates.ibexa.co',
                'https://updates.ibexa.co/subscription',
                'vendor/ibexa/subscription.json',
                false,
            );
        }
    }
}
