<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Controller;

use EzSystems\EzSupportToolsBundle\View\SystemInfoView;

/**
 * Do we need that controller at all ?
 */
class SystemInfoController
{
    public function viewInfoAction(SystemInfoView $view)
    {
        return $view;
    }
}
