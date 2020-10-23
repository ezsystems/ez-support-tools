<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace SystemInfo\Collector;

use EzSystems\EzPlatformCoreBundle\EzPlatformCoreBundle;
use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\EzSystemInfoCollector;
use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\JsonComposerLockSystemInfoCollector;
use PHPUnit\Framework\TestCase;

class EzSystemInfoCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $composerCollector = new JsonComposerLockSystemInfoCollector(
            __DIR__ . '/_fixtures/composer.lock', __DIR__ . '/_fixtures/composer.json'
        );

        $systemInfoCollector = new EzSystemInfoCollector($composerCollector);
        $systemInfo = $systemInfoCollector->collect();
        self::assertSame('Ibexa Platform', $systemInfo->name);
        self::assertSame(EzPlatformCoreBundle::VERSION, $systemInfo->release);
    }
}
