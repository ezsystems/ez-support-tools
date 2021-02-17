<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Collector;

use EzSystems\EzSupportToolsBundle\SystemInfo\Value\ServicesSystemInfo;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Collects information about the services used within the project.
 */
class ServicesSystemInfoCollector implements SystemInfoCollector
{
    private const SEARCH_ENGINE_CONFIG_KEY = 'search_engine';
    private const HTTP_CACHE_CONFIG_KEY = 'purge_type';
    private const PERSISTENCE_CACHE_CONFIG_KEY = 'cache_pool';

    /** @var \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * Collects information about the utilized services.
     *
     * @return \EzSystems\EzSupportToolsBundle\SystemInfo\Value\ServicesSystemInfo
     */
    public function collect(): ServicesSystemInfo
    {
        return new ServicesSystemInfo([
            'searchEngine' => $this->parameterBag->get(self::SEARCH_ENGINE_CONFIG_KEY),
            'httpCacheProxy' => $this->parameterBag->get(self::HTTP_CACHE_CONFIG_KEY),
            'persistenceCacheAdapter' => $this->parameterBag->get(self::PERSISTENCE_CACHE_CONFIG_KEY),
        ]);
    }
}
