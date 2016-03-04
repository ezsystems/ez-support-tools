<?php

/**
 * File containing the InfoProviderInterface interface.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\InfoProvider;

interface InfoProviderInterface
{
    /**
     * Returns info provider identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns display template.
     *
     * @return string
     */
    public function getTemplate();

    /**
     * Returns system information.
     *
     * @return Value
     */
    public function getInfo();
}
