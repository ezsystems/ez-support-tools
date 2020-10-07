<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\View;

use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use EzSystems\EzSupportToolsBundle\SystemInfo\SystemInfoCollectorRegistry;

class SystemInfoViewBuilder implements ViewBuilder
{
    /**
     * @var \eZ\Publish\Core\MVC\Symfony\View\Configurator
     */
    private $viewConfigurator;

    /**
     * System info collector registry.
     *
     * @var \EzSystems\EzSupportToolsBundle\SystemInfo\SystemInfoCollectorRegistry
     */
    private $registry;

    public function __construct(Configurator $viewConfigurator, SystemInfoCollectorRegistry $registry)
    {
        $this->viewConfigurator = $viewConfigurator;
        $this->registry = $registry;
    }

    public function matches($argument)
    {
        return $argument === 'support_tools.view.controller:viewInfoAction';
    }

    public function buildView(array $parameters)
    {
        $collector = $this->getCollector($parameters['systemInfoIdentifier']);
        $view = new SystemInfoView(null, [], $parameters['viewType']);
        $view->setInfo($collector->collect());

        $this->viewConfigurator->configure($view);

        return $view;
    }

    /**
     * @param string $identifier A SystemInfo collector identifier (php, hardware...)
     *
     * @return \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\SystemInfoCollector
     */
    private function getCollector($identifier)
    {
        return $this->registry->getItem($identifier);
    }
}
