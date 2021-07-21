<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\Tests\VersionStability;

use EzSystems\EzSupportTools\Value\Stability;
use EzSystems\EzSupportTools\VersionStability\ComposerVersionStabilityChecker;
use PHPUnit\Framework\TestCase;

final class VersionStabilityCheckerTest extends TestCase
{
    /** @var \EzSystems\EzSupportTools\VersionStability\VersionStabilityChecker */
    private $versionStabilityChecker;

    public function setUp(): void
    {
        $this->versionStabilityChecker = new ComposerVersionStabilityChecker();
    }

    /**
     * @dataProvider provideStableVersions
     */
    public function testIsStableVersion(string $stableVersion): void
    {
        self::assertTrue(
            $this->versionStabilityChecker->isStableVersion($stableVersion)
        );
    }

    public function provideStableVersions(): iterable
    {
        yield ['1.0.0.0'];
        yield ['1.1.0.0'];
        yield ['2.10.5.0'];
        yield ['6.1.10.10'];
        yield ['15.20.100.1500'];
    }

    /**
     * @dataProvider provideNotStableVersions
     */
    public function testIsNotStableVersion(string $notStableVersion): void
    {
        self::assertFalse(
            $this->versionStabilityChecker->isStableVersion($notStableVersion)
        );
    }

    public function provideNotStableVersions(): iterable
    {
        yield ['1.0.20'];
        yield ['1.0.2-beta'];
        yield ['1.0.2-beta1'];
        yield ['1.0.2-rc'];
        yield ['1.0.2-dev'];
        yield ['dev-1.0.2'];
        yield ['dev-master'];
        yield ['dev-main'];
        yield ['1.0.2.1-alpha'];
        yield ['1.0'];
        yield ['v1.0-rc2'];
    }

    /**
     * @dataProvider provideVersions
     */
    public function testGetStability(
        string $version,
        string $expectedStability
    ): void {
        self::assertEquals(
            $expectedStability,
            $this->versionStabilityChecker->getStability($version)
        );
    }

    public function provideVersions(): iterable
    {
        yield ['0.1.10.50', Stability::STABILITIES[0]];
        yield ['10.10.10.50', Stability::STABILITIES[0]];
        yield ['1.0.0.1-RC20', Stability::STABILITIES[5]];
        yield ['1.0.1.5-RC2', Stability::STABILITIES[5]];
        yield ['1.0.1.5-beta1', Stability::STABILITIES[10]];
        yield ['1.0.1.5-beta12', Stability::STABILITIES[10]];
        yield ['1.0.1.5-beta100', Stability::STABILITIES[10]];
        yield ['1.1000.1.5-beta1000', Stability::STABILITIES[10]];
        yield ['1.100.10.0-alpha1', Stability::STABILITIES[15]];
        yield ['1.0.0-alpha5', Stability::STABILITIES[15]];
        yield ['1.0.0-alpha51', Stability::STABILITIES[15]];
        yield ['10.10.10.50-dev', Stability::STABILITIES[20]];
        yield ['dev-master', Stability::STABILITIES[20]];
        yield ['1.0.0.1-custom', Stability::STABILITIES[20]];
        yield ['dev-master', Stability::STABILITIES[20]];
        yield ['dev-test-v1', Stability::STABILITIES[20]];
        yield ['dev-test_custom', Stability::STABILITIES[20]];
    }
}
