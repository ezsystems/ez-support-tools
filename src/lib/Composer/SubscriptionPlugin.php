<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PostFileDownloadEvent;
use Composer\Plugin\PreFileDownloadEvent;

/**
 * Class SubscriptionPlugin
 *
 * COMPOSER_DEBUG_EVENTS=1 composer update
 */
class SubscriptionPlugin implements PluginInterface
{
    protected $composer;
    /**
     * @var IOInterface
     */
    protected $io;

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
        return array(
            PluginEvents::PRE_FILE_DOWNLOAD => [
                ['onPreFileDownload', 0],
            ],
            // NOTE: Won't be fired if there are no downloads
            PluginEvents::POST_FILE_DOWNLOAD => [
                ['onPostFileDownload', 0],
            ],
        );
    }

    public function onPreFileDownload(PreFileDownloadEvent $event)
    {
        $url = $event->getProcessedUrl();
        if (false !== preg_match('@^https://updates.ez.no/[^/]+/packages.json$@', $url)) {
            $this->io->warning(
                "updates.ez.no will be sunset by end of 2021, please move to updates.ibexa.co.\n".
                "    See: blog post url"
            );
            return;
        }

        $this->io->warning(
            "Match failed"
        );
    }

    public function onPostFileDownload(PostFileDownloadEvent $event)
    {
        echo $event->getUrl();

        if ($protocol === 's3') {
            // ...
        }
    }
}
