<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;

/**
 * Plugin for loading subscriction info for hints in admin UI, for trail & expiry info when relevant.
 *
 * Will only register itself if it detects that install is configerd with 'https://updates.ibexa.co' as composer repository.
 *
 * To debug events fired by Composer, use: COMPOSER_DEBUG_EVENTS=1 composer update
 */
class SubscriptionPlugin implements PluginInterface, EventSubscriberInterface
{
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
    private $hasUpdatesIbexaCoRepo = false;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::PRE_INSTALL_CMD => 'checkRepositoryConfig',
            ScriptEvents::PRE_UPDATE_CMD => 'checkRepositoryConfig',

            ScriptEvents::POST_INSTALL_CMD => 'downloadSubscriptionInfo',
            ScriptEvents::POST_UPDATE_CMD => 'downloadSubscriptionInfo',
        ];
    }

    public function checkRepositoryConfig(): void
    {
        foreach ($this->composer->getRepositoryManager()->getRepositories() as $repo) {
            if (strpos($repo->getRepoName(), 'https://updates.ibexa.co') !== false) {
                $this->hasUpdatesIbexaCoRepo = true;
            }

            if (strpos($repo->getRepoName(), 'https://updates.ez.no/') !== false) {
                $this->writeWarning("WARNING: 'updates.ez.no' is deprecated, for how to use 'updates.ibexa.co' see: https://TODO");
            } else if (1 === preg_match('@https://updates.ibexa.co/[^/]+@', $repo->getRepoName())) {
                $this->writeWarning("WARNING: Ibexa update repository should be configured as 'https://updates.ibexa.co'");
            }
        }
    }

    public function downloadSubscriptionInfo(): void
    {
        // Skip if we don't have updates.ibexa.co repo yet, as we then probably also don't have AUTH config for it
        if (!$this->hasUpdatesIbexaCoRepo) {
            return;
        }

        !is_dir('vendor/ibexa') && mkdir('vendor/ibexa');

        $this->composer->getLoop()->getHttpDownloader()->addCopy(
            'https://updates.ibexa.co/subscription',
            'vendor/ibexa/subscription.json'
        );

        $this->io->write(
            "Downloading subscription info from 'https://updates.ibexa.co/subscription'",
            true,
            IOInterface::VERBOSE
        );
    }

    private function writeWarning(string $message)
    {
        if (method_exists($this->io, 'warning')) {
            $this->io->warning($message);
        } else {
            $this->io->write($message);
        }
    }
}
