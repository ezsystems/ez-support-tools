<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Tests\SystemInfo\Collector;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use EzSystems\EzSupportTools\Storage\MetricsInterface;
use EzSystems\EzSupportTools\Storage\MetricsProviderInterface;
use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\RepositorySystemInfoCollector;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\RepositoryMetrics;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\RepositorySystemInfo;
use PHPUnit\Framework\TestCase;

class RepositorySystemInfoCollectorTest extends TestCase
{
    /**
     * @var \Doctrine\DBAL\Connection|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dbalConnectionMock;

    /**
     * @var \Doctrine\DBAL\Platforms\MySqlPlatform|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dbalPlatformMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $metricsProviderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $metricsMock;

    /**
     * @var RepositorySystemInfoCollector
     */
    private $repositoryCollector;

    protected function setUp(): void
    {
        $this->dbalConnectionMock = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $this->dbalPlatformMock = $this->getMockBuilder(AbstractPlatform::class)->getMock();
        $this->metricsProviderMock = $this->createMock(MetricsProviderInterface::class);
        $this->metricsMock = $this->createMock(MetricsInterface::class);

        $this->repositoryCollector = new RepositorySystemInfoCollector(
            $this->dbalConnectionMock,
            $this->metricsProviderMock
        );
    }

    /**
     * @covers \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\RepositorySystemInfoCollector::collect()
     */
    public function testCollect()
    {
        $expected = new RepositorySystemInfo([
            'type' => 'mysql',
            'name' => 'ezp_platform',
            'host' => 'localhost',
            'username' => 'ezp_user',
            'repositoryMetrics' => new RepositoryMetrics([
                'publishedCount' => 10,
                'usersCount' => 5,
                'draftsCount' => 20,
                'versionsCount' => 32,
                'contentTypesCount' => 8,
            ]),
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

        $this->metricsProviderMock
            ->expects($this->exactly(5))
            ->method('provideMetrics')
            ->withConsecutive(
                ['published'],
                ['users'],
                ['drafts'],
                ['versions'],
                ['content_types']
            )
            ->willReturnOnConsecutiveCalls(
                $this->metricsMock,
                $this->metricsMock,
                $this->metricsMock,
                $this->metricsMock,
                $this->metricsMock,
            );

        $this->metricsMock
            ->expects($this->exactly(5))
            ->method('getValue')
            ->willReturnOnConsecutiveCalls(
                $expected->repositoryMetrics->publishedCount,
                $expected->repositoryMetrics->usersCount,
                $expected->repositoryMetrics->draftsCount,
                $expected->repositoryMetrics->versionsCount,
                $expected->repositoryMetrics->contentTypesCount,
            );

        $value = $this->repositoryCollector->collect();

        self::assertInstanceOf(RepositorySystemInfo::class, $value);
        self::assertEquals($expected, $value);
    }
}
