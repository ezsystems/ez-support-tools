<?php

/**
 * File containing the DoctrineDatabaseSystemInfoCollectorTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Tests\SystemInfo\Collector;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\DoctrineDatabaseSystemInfoCollector;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\DatabaseSystemInfo;
use PHPUnit_Framework_TestCase;

class DoctrineDatabaseSystemInfoCollectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Doctrine\DBAL\Connection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dbalConnectionMock;

    /**
     * @var \Doctrine\DBAL\Platforms\MySqlPlatform|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dbalPlatformMock;

    /**
     * @var DoctrineDatabaseSystemInfoCollector
     */
    private $databaseCollector;

    public function setUp()
    {
        $this->dbalConnectionMock = $this->getMockBuilder('Doctrine\DBAL\Connection')->disableOriginalConstructor()->getMock();
        $this->dbalPlatformMock = $this->getMockBuilder('Doctrine\DBAL\Platforms\MySqlPlatform')->getMock();

        $this->databaseCollector = new DoctrineDatabaseSystemInfoCollector($this->dbalConnectionMock);
    }

    /**
     * @covers \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\DoctrineDatabaseSystemInfoCollector::collect()
     */
    public function testCollect()
    {
        $expected = new DatabaseSystemInfo([
            'type' => 'mysql',
            'name' => 'ezp_platform',
            'host' => 'localhost',
            'username' => 'ezp_user',
        ]);

        $this->dbalConnectionMock
            ->expects($this->once())
            ->method('getDatabasePlatform')
            ->will($this->returnValue($this->dbalPlatformMock));

        $this->dbalPlatformMock
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($expected->type));

        $this->dbalConnectionMock
            ->expects($this->once())
            ->method('getDatabase')
            ->will($this->returnValue($expected->name));

        $this->dbalConnectionMock
            ->expects($this->once())
            ->method('getHost')
            ->will($this->returnValue($expected->host));

        $this->dbalConnectionMock
            ->expects($this->once())
            ->method('getUsername')
            ->will($this->returnValue($expected->username));

        $value = $this->databaseCollector->collect();

        self::assertInstanceOf('EzSystems\EzSupportToolsBundle\SystemInfo\Value\DatabaseSystemInfo', $value);

        self::assertEquals($expected, $value);
    }
}
