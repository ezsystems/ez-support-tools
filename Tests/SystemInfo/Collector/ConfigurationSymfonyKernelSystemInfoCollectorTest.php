<?php

/**
 * File containing the ConfigurationSymfonyKernelSystemInfoCollectorTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Tests\SystemInfo\Collector;

use EzSystems\EzSupportToolsBundle\SystemInfo\Collector\ConfigurationSymfonyKernelSystemInfoCollector;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\SymfonyKernelSystemInfo;
use PHPUnit\Framework\TestCase as PHPUnit_Framework_TestCase;
use Symfony\Component\HttpKernel\Kernel;

class ConfigurationSymfonyKernelSystemInfoCollectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\ConfigurationSymfonyKernelSystemInfoCollector::collect()
     */
    public function testCollect()
    {
        $expected = new SymfonyKernelSystemInfo([
            'environment' => 'dev',
            'debugMode' => true,
            'version' => Kernel::VERSION,
            'bundles' => [
                'AppBundle' => 'AppBundle\\AppBundle',
                'DoctrineBundle' => 'Doctrine\\Bundle\\DoctrineBundle\\DoctrineBundle',
                'eZPlatformUIBundle' => 'EzSystems\\PlatformUIBundle\\EzSystemsPlatformUIBundle',
                'EzPublishCoreBundle' => 'eZ\\Bundle\\EzPublishCoreBundle\\EzPublishCoreBundle',
                'EzSystemsEzSupportToolsBundle' => 'EzSystems\\EzSupportToolsBundle\\EzSystemsEzSupportToolsBundle',
            ],
            'rootDir' => '/srv/www/ezpublish-platform/app',
            'name' => 'app',
            'cacheDir' => '/srv/www/ezpublish-platform/app/cache/prod',
            'logDir' => '/srv/www/ezpublish-platform/app/logs',
            'charset' => 'UTF-8',
        ]);

        $kernelMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Kernel')
            ->setConstructorArgs([$expected->environment, $expected->debugMode])
            ->getMock();

        $kernelMock
            ->expects($this->once())
            ->method('getEnvironment')
            ->will($this->returnValue($expected->environment));

        $kernelMock
            ->expects($this->once())
            ->method('isDebug')
            ->will($this->returnValue($expected->debugMode));

        $kernelMock
            ->expects($this->once())
            ->method('getRootDir')
            ->will($this->returnValue($expected->rootDir));

        $kernelMock
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($expected->name));

        $kernelMock
            ->expects($this->once())
            ->method('getCacheDir')
            ->will($this->returnValue($expected->cacheDir));

        $kernelMock
            ->expects($this->once())
            ->method('getLogDir')
            ->will($this->returnValue($expected->logDir));

        $kernelMock
            ->expects($this->once())
            ->method('getCharset')
            ->will($this->returnValue($expected->charset));

        $symfonyCollector = new ConfigurationSymfonyKernelSystemInfoCollector(
            $kernelMock,
            $expected->bundles
        );

        $value = $symfonyCollector->collect();

        self::assertInstanceOf('EzSystems\EzSupportToolsBundle\SystemInfo\Value\SymfonyKernelSystemInfo', $value);

        self::assertEquals($expected, $value);
    }
}
