<?php

/**
 * File containing the SystemInfoController class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\Controller;

use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzSupportToolsBundle\InfoProvider\InfoProviderCollection;
use EzSystems\PlatformUIBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SystemInfoController extends Controller
{
    /**
     * @var \EzSystems\EzSupportToolsBundle\InfoProvider\InfoProviderCollection
     */
    protected $infoProviderCollection;

    public function __construct(InfoProviderCollection $infoProviderCollection)
    {
        $this->infoProviderCollection = $infoProviderCollection;
    }

    public function performAccessChecks()
    {
        parent::performAccessChecks();
        $this->denyAccessUnlessGranted(new Attribute('setup', 'system_info'));
    }

    /**
     * Renders the system information page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function infoAction()
    {
        $infoArray = ['infoProviders' => []];
        foreach ($this->infoProviderCollection->infoProviders() as $infoProvider) {
            $infoArray['infoProviders'][$infoProvider->getIdentifier()] = [
                'template' => $infoProvider->getTemplate(),
                'info' => $infoProvider->getInfo(),
            ];
        }

        return $this->render('eZSupportTools:SystemInfo:info.html.twig', $infoArray);
    }

    /**
     * Renders a PHP info page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function phpinfoAction()
    {
        ob_start();
        phpinfo();
        $response = new Response(ob_get_clean());

        return $response;
    }
}
