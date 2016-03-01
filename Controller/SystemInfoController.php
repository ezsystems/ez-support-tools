<?php

/**
 * File containing the SystemInfoController class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportTools\Controller;

use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\PlatformUIBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SystemInfoController extends Controller
{
    /**
     * @var \EzSystems\EzSupportTools\InfoProvider\InfoProviderInterface[] Info providers
     */
    protected $infoProviders;

    public function __construct(...$infoProviders) // TODO pretty sure this isn't the way to do this
    {
        $this->infoProviders = $infoProviders;
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
        foreach ($this->infoProviders as $infoProvider) {
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
