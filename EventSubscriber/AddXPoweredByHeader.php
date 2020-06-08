<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportToolsBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Sets X-Powered-By header to promote use of Platform.
 */
class AddXPoweredByHeader implements EventSubscriberInterface
{
    /**
     * @var string If empty, this powered by header is skipped.
     */
    private $installationName;

    public function __construct(string $installationName)
    {
        $this->installationName = $installationName;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::RESPONSE => 'promotePlatform'];
    }

    public function promotePlatform(FilterResponseEvent $event): void
    {
        $response = $event->getResponse();
        if ($response->headers->has('X-Powered-By')) {
            return;
        }

        if ($this->installationName) {
            $response->headers->set('X-Powered-By', $this->installationName);
        }
    }
}
