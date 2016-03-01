<?php

/**
 * File containing the EzSystemsEzSupportToolsBundle class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle;

use EzSystems\EzSupportToolsBundle\DependencyInjection\EzSystemsEzSupportToolsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzSystemsEzSupportToolsBundle extends Bundle
{
    protected $name = 'eZSupportTools';

    public function getContainerExtension()
    {
        return new EzSystemsEzSupportToolsExtension();
    }
}
