<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\View;

use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use eZ\Publish\Core\MVC\Symfony\View\View;

class SystemInfoView extends BaseView implements View
{
    /**
     * @var \EzSystems\EzSupportToolsBundle\SystemInfo\Value\SystemInfo
     */
    private $info;

    /**
     * @param \EzSystems\EzSupportToolsBundle\SystemInfo\Value\SystemInfo $info
     *
     * @return SystemInfoView
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * @return \EzSystems\EzSupportToolsBundle\SystemInfo\Value\SystemInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    protected function getInternalParameters()
    {
        return ['info' => $this->info];
    }
}
