<?php

/**
 * File containing the InfoProviderCollection class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\InfoProvider;

class InfoProviderCollection
{
    /**
     * @var \EzSystems\EzSupportTools\InfoProvider\InfoProviderInterface[] Info providers
     */
    protected $infoProviders;

    public function __construct(...$infoProviders) // TODO pretty sure this isn't the way to do this
    {
        $this->infoProviders = $infoProviders;
    }

    /**
     * Get all configured InfoProviders.
     *
     * @return InfoProvider[]
     */
    public function infoProviders()
    {
        return $this->infoProviders;
    }

    /**
     * Get list of identifiers of InfoProviders.
     *
     * @return string[]
     */
    public function infoProviderIdentifiers()
    {
        $infoProviderIdentifiers = [];
        foreach ($this->infoProviders as $infoProvider) {
            $infoProviderIdentifiers[] = $infoProvider->getIdentifier();
        }

        return $infoProviderIdentifiers;
    }
}
