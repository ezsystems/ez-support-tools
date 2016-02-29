<?php

/**
 * File containing the EzSystemsEzSupportTools class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportTools;

use EzSystems\EzSupportTools\DependencyInjection\EzSystemsEzSupportToolsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzSystemsEzSupportTools extends Bundle
{
    protected $name = 'eZSupportTools';

    public function getContainerExtension()
    {
        return new EzSystemsEzSupportToolsExtension();
    }
}
