<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Tests\SystemInfo\Collector;

use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\EzcPhpSystemInfoCollector;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\PhpSystemInfo;
use PHPUnit\Framework\TestCase;

class EzcPhpSystemInfoCollectorTest extends TestCase
{
    /**
     * @var \EzSystems\EzSupportToolsBundle\SystemInfo\EzcSystemInfoWrapper|\PHPUnit\Framework\MockObject\MockObject
     */
    private $ezcSystemInfoMock;

    /**
     * @var EzcPhpSystemInfoCollector
     */
    private $ezcPhpCollector;

    protected function setUp(): void
    {
        $this->ezcSystemInfoMock = $this
            ->getMockBuilder('EzSystems\EzSupportToolsBundle\SystemInfo\EzcSystemInfoWrapper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->ezcSystemInfoMock->phpVersion = PHP_VERSION;

        $this->ezcSystemInfoMock->phpAccelerator = $this
            ->getMockBuilder('ezcSystemInfoAccelerator')
            ->setConstructorArgs(
                [
                    'Zend OPcache',
                    'http://www.php.net/opcache',
                    true,
                    false,
                    '7.0.4-devFE',
                ]
            )
            ->getMock();

        $this->ezcPhpCollector = new EzcPhpSystemInfoCollector($this->ezcSystemInfoMock);
    }

    /**
     * @covers \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\EzcPhpSystemInfoCollector::collect()
     */
    public function testCollect()
    {
        $value = $this->ezcPhpCollector->collect();

        self::assertInstanceOf('EzSystems\EzSupportToolsBundle\SystemInfo\Value\PhpSystemInfo', $value);

        self::assertEquals(
            new PhpSystemInfo([
                'version' => $this->ezcSystemInfoMock->phpVersion,
                'acceleratorEnabled' => $this->ezcSystemInfoMock->phpAccelerator->isEnabled,
                'acceleratorName' => $this->ezcSystemInfoMock->phpAccelerator->name,
                'acceleratorURL' => $this->ezcSystemInfoMock->phpAccelerator->url,
                'acceleratorVersion' => $this->ezcSystemInfoMock->phpAccelerator->versionString,
            ]),
            $value
        );
    }
}
