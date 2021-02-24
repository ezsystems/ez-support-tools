<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Tests\SystemInfo\Collector;

use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\ServicesSystemInfoCollector;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\ServicesSystemInfo;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ServicesSystemInfoCollectorTest extends TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $parameterBagMock;

    /**
     * @var \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\ServicesSystemInfoCollector
     */
    private $servicesCollector;

    protected function setUp(): void
    {
        $this->parameterBagMock = $this->createMock(ParameterBagInterface::class);
        $this->servicesCollector = new ServicesSystemInfoCollector($this->parameterBagMock);
    }

    /**
     * @covers \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\RepositorySystemInfoCollector::collect()
     */
    public function testCollect()
    {
        $expected = new ServicesSystemInfo([
            'searchEngine' => 'solr',
            'httpCacheProxy' => 'varnish',
            'persistenceCacheAdapter' => 'cache.adapter.memcached',
        ]);

        $this->parameterBagMock
            ->expects($this->at(0))
            ->method('get')
            ->willReturn($expected->searchEngine);

        $this->parameterBagMock
            ->expects($this->at(1))
            ->method('get')
            ->willReturn($expected->httpCacheProxy);

        $this->parameterBagMock
            ->expects($this->at(2))
            ->method('get')
            ->willReturn($expected->persistenceCacheAdapter);

        $value = $this->servicesCollector->collect();

        self::assertInstanceOf(ServicesSystemInfo::class, $value);
        self::assertEquals($expected, $value);
    }
}
