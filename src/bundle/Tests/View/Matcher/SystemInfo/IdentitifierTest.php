<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Tests\View\Matcher\SystemInfo;

use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\HardwareSystemInfo;
use EzSystems\EzSupportToolsBundle\View\Matcher\SystemInfo\Identifier;
use EzSystems\EzSupportToolsBundle\View\SystemInfoView;
use PHPUnit\Framework\TestCase;

class IdentitifierTest extends TestCase
{
    public function testMatch()
    {
        $view = new SystemInfoView();
        $view->setInfo(new HardwareSystemInfo());

        $matcher = new Identifier();
        $matcher->setMatchingConfig('hardware');

        self::assertTrue($matcher->match($view));
    }

    public function testNoMatch()
    {
        $view = new SystemInfoView();
        $view->setInfo(new HardwareSystemInfo());

        $matcher = new Identifier();
        $matcher->setMatchingConfig('php');

        self::assertFalse($matcher->match($view));
    }

    public function testMatchOtherView()
    {
        $view = new ContentView();

        $matcher = new Identifier();
        $matcher->setMatchingConfig('test');

        self::assertFalse($matcher->match($view));
    }
}
