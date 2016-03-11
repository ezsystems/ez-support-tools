<?php

/**
 * File containing the EzcPhpSystemInfoCollectorTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Tests\SystemInfo\Collector;

use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\EzcPhpSystemInfoCollector;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\PhpSystemInfo;
use ezcSystemInfo;
use PHPUnit_Framework_TestCase;

class EzcPhpSystemInfoCollectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ezcSystemInfo|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ezcSystemInfoMock;

    /**
     * @var EzcPhpSystemInfoCollector
     */
    private $ezcPhpCollector;

    public function setUp()
    {
        $this->ezcSystemInfoMock = $this->getMockBuilder('ezcSystemInfo')->disableOriginalConstructor()->getMock();
        $this->ezcSystemInfoMock->phpVersion = '5.6.7-1';

        $this->ezcSystemInfoMock->phpAccelerator = $this
            ->getMockBuilder('ezcSystemInfoAccelerator')
            ->setConstructorArgs([
                'Zend OPcache',
                'http://www.php.net/opcache',
                true,
                false,
                '7.0.4-devFE'])
            ->getMock();

        $this->ezcPhpCollector = new EzcPhpSystemInfoCollector($this->ezcSystemInfoMock);
    }

    public function testBuild()
    {
        $value = $this->ezcPhpCollector->build();

        self::assertInstanceOf('EzSystems\EzSupportToolsBundle\SystemInfo\Value\PhpSystemInfo', $value);

        self::assertEquals(
            new PhpSystemInfo([
                'phpVersion' => $this->ezcSystemInfoMock->phpVersion,
                'phpAccelerator' => [
                    'name' => $this->ezcSystemInfoMock->phpAccelerator->name,
                    'url' => $this->ezcSystemInfoMock->phpAccelerator->url,
                    'enabled' => $this->ezcSystemInfoMock->phpAccelerator->isEnabled,
                    'versionString' => $this->ezcSystemInfoMock->phpAccelerator->versionString,
                ],
            ]),
            $value
        );
    }
}
