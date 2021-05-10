<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Value;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Value for information about services used within the project.
 */
class ServicesSystemInfo extends ValueObject implements SystemInfo
{
    /**
     * Search engine.
     *
     * Example: Solr
     *
     * @var string
     */
    public $searchEngine;

    /**
     * Reverse proxy handling HTTP caching.
     *
     * Example: Fastly
     *
     * @var string
     */
    public $httpCacheProxy;

    /**
     * Persistence cache adapter.
     *
     * Example: Redis
     *
     * @var string
     */
    public $persistenceCacheAdapter;
}
