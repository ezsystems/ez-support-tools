<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo;

/**
 * A registry of SystemInfoCollectors.
 */
interface SystemInfoCollectorRegistry
{
    /**
     * @param \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\SystemInfoCollector[] $items Hash of SystemInfoCollectors, with identifier string as key.
     */
    public function __construct(array $items = []);

    /**
     * Returns the SystemInfoCollector matching the argument.
     *
     * @param string $identifier An identifier string.
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\NotFoundException If no SystemInfoCollector exists with this identifier
     *
     * @return \EzSystems\EzSupportToolsBundle\SystemInfo\Collector\SystemInfoCollector The SystemInfoCollector given by the identifier.
     */
    public function getItem($identifier);

    /**
     * Returns the identifiers of all registered SystemInfoCollectors.
     *
     * @return string[] Array of identifier strings.
     */
    public function getIdentifiers();
}
