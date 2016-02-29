<?php

/**
 * File containing the InfoProvider class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\SystemInfoBundle\InfoProvider;

abstract class InfoProvider implements InfoProviderInterface
{
    /**
     * @var string Display template
     */
    protected $template;

    /**
     * Returns info provider identifier.
     *
     * @return string
     */
    abstract public function getIdentifier();

    /**
     * Returns display template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Returns system information.
     *
     * @return array
     */
    abstract public function getInfo();
}
