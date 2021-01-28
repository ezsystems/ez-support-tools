<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Tests\SystemInfo\Collector;

use EzSystems\EzSupportTools\VersionStability\VersionStabilityChecker;
use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\JsonComposerLockSystemInfoCollector;
use EzSystems\EzSupportToolsBundle\SystemInfo\Exception\ComposerFileValidationException;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\ComposerPackage;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\ComposerSystemInfo;
use PHPUnit\Framework\TestCase;

class JsonComposerLockSystemInfoCollectorTest extends TestCase
{
    /** @var \EzSystems\EzSupportTools\VersionStability\VersionStabilityChecker|\PHPUnit\Framework\MockObject\MockObject */
    private $versionStabilityChecker;

    public function setUp(): void
    {
        $this->versionStabilityChecker = $this->createMock(VersionStabilityChecker::class);
    }

    /**
     * @covers \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\JsonComposerLockSystemInfoCollector::collect()
     */
    public function testCollect(): void
    {
        $expected = new ComposerSystemInfo([
            'packages' => [
                'ezsystems/ezpublish-kernel' => new ComposerPackage([
                    'name' => 'ezsystems/ezpublish-kernel',
                    'branch' => 'dev-master',
                    'alias' => '7.5.x-dev',
                    'version' => '7.5.x',
                    'license' => 'GPL-2.0',
                    'stability' => 'dev',
                    'dateTime' => new \DateTime('2016-02-28 14:30:53'),
                    'homepage' => 'http://share.ez.no',
                    'reference' => 'ec897baa77c63b745749acf201e85b92bd614723',
                ]),
                'doctrine/dbal' => new ComposerPackage([
                    'name' => 'doctrine/dbal',
                    'branch' => 'v2.5.4',
                     'alias' => null,
                    'version' => '2.5.4',
                    'license' => 'MIT',
                    'dateTime' => new \DateTime('2016-01-05 22:11:12'),
                    'homepage' => 'http://www.doctrine-project.org',
                    'reference' => 'abbdfd1cff43a7b99d027af3be709bc8fc7d4769',
                ]),
                'symfony/symfony' => new ComposerPackage([
                    'name' => 'symfony/symfony',
                    'branch' => 'v2.7.10',
                    'alias' => null,
                    'version' => '2.7.10',
                    'license' => 'MIT',
                    'dateTime' => new \DateTime('2016-02-28 20:37:19'),
                    'homepage' => 'https://symfony.com',
                    'reference' => '9a3b6bf6ebee49370aaf15abc1bdeb4b1986a67d',
                ]),
                'zetacomponents/system-information' => new ComposerPackage([
                    'name' => 'zetacomponents/system-information',
                    'branch' => '1.1',
                    'alias' => null,
                    'version' => '1.1',
                    'license' => 'Apache-2.0',
                    'dateTime' => new \DateTime('2014-09-27 19:26:09'),
                    'homepage' => 'https://github.com/zetacomponents',
                    'reference' => 'be0e5b69dde0a51f8d2a036b891964521939769f',
                ]),
            ],
            'minimumStability' => 'stable',
            'repositoryUrls' => ['https://updates.ez.no/bul'],
        ]);

        $composerCollector = new JsonComposerLockSystemInfoCollector(
            $this->versionStabilityChecker,
            __DIR__ . '/_fixtures/composer.lock',
            __DIR__ . '/_fixtures/composer.json'
        );
        $value = $composerCollector->collect();

        self::assertInstanceOf('EzSystems\EzSupportToolsBundle\SystemInfo\Value\ComposerSystemInfo', $value);
        self::assertEquals($expected, $value);
    }

    /**
     * @covers \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\JsonComposerLockSystemInfoCollector::collect()
     */
    public function testCollectLockFileNotFound(): void
    {
        $this->expectException(ComposerLockFileNotFoundException::class);

        $composerCollectorNotFound = new JsonComposerLockSystemInfoCollector(
            $this->versionStabilityChecker,
            __DIR__ . '/_fixtures/snafu.lock',
            __DIR__ . '/_fixtures/composer.json'
        );
        $composerCollectorNotFound->collect();
    }

    /**
     * @covers \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\JsonComposerLockSystemInfoCollector::collect()
     */
    public function testCollectJsonFileNotFound(): void
    {
        $this->expectException(ComposerJsonFileNotFoundException::class);

        $composerCollectorNotFound = new JsonComposerLockSystemInfoCollector(
            $this->versionStabilityChecker,
            __DIR__ . '/_fixtures/composer.lock',
            __DIR__ . '/_fixtures/snafu.json'
        );
        $composerCollectorNotFound->collect();
    }

    /**
     * @covers \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\JsonComposerLockSystemInfoCollector::collect()
     */
    public function testCollectLockFileCorrupted(): void
    {
        $composerCollectorCorrupted = new JsonComposerLockSystemInfoCollector(
            __DIR__ . '/_fixtures/corrupted_composer.lock',
            __DIR__ . '/_fixtures/composer.json'
        );

        $this->expectException(ComposerFileValidationException::class);
        $composerCollectorCorrupted->collect();
    }

    /**
     * @covers \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\JsonComposerLockSystemInfoCollector::collect()
     */
    public function testCollectJsonFileCorrupted(): void
    {
        $composerCollectorCorrupted = new JsonComposerLockSystemInfoCollector(
            __DIR__ . '/_fixtures/composer.lock',
            __DIR__ . '/_fixtures/corrupted_composer.json'
        );

        $this->expectException(ComposerFileValidationException::class);
        $composerCollectorCorrupted->collect();
    }
}
