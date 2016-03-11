<?php

/**
 * File containing the EzcHardwareTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Tests\SystemInfo\Collector;

use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\EzcHardwareSystemInfoCollector;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\HardwareSystemInfo;
use ezcSystemInfo;
use PHPUnit_Framework_TestCase;

class EzcHardwareTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ezcSystemInfo|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ezcSystemInfoMock;

    /**
     * @var EzcHardwareSystemInfoCollector
     */
    private $ezcHardware;

    public function setUp()
    {
        $this->ezcSystemInfoMock = $this->getMockBuilder('ezcSystemInfo')->disableOriginalConstructor()->getMock();
        $this->ezcSystemInfoMock->cpuType = 'Intel(R) Core(TM) i7-3720QM CPU @ 2.60GHz';
        $this->ezcSystemInfoMock->cpuSpeed = '2591.9000000000001';
        $this->ezcSystemInfoMock->cpuCount = '1';
        $this->ezcSystemInfoMock->memorySize = '1554632704';

        $this->ezcHardware = new EzcHardwareSystemInfoCollector($this->ezcSystemInfoMock);
    }

    public function testBuild()
    {
        $value = $this->ezcHardware->build();

        self::assertInstanceOf('EzSystems\EzSupportToolsBundle\SystemInfo\Value\HardwareSystemInfo', $value);

        self::assertEquals(
            new HardwareSystemInfo([
                'cpuType' => $this->ezcSystemInfoMock->cpuType,
                'cpuSpeed' => $this->ezcSystemInfoMock->cpuSpeed,
                'cpuCount' => $this->ezcSystemInfoMock->cpuCount,
                'memorySize' => $this->ezcSystemInfoMock->memorySize,
            ]),
            $value
        );
    }
}
