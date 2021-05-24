<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\View;

use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use EzSystems\EzSupportToolsBundle\SystemInfo\Exception\SystemInfoException;
use EzSystems\EzSupportToolsBundle\SystemInfo\SystemInfoCollectorRegistry;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\InvalidSystemInfo;

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

        try {
            $collectedData = $collector->collect();
        } catch (SystemInfoException $e) {
            $collectedData = new InvalidSystemInfo();
            $collectedData->errorMessage = $e->getMessage();
        }

        $view->setInfo($collectedData);
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
