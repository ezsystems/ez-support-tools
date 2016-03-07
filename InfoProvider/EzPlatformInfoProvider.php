<?php

/**
 * File containing the EzPlatformInfoProvider class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\InfoProvider;

use Symfony\Component\HttpKernel\Kernel;

class EzPlatformInfoProvider extends InfoProvider
{
    /**
     * An array containing the active bundles (keys) and the corresponding namespace.
     *
     * @var array
     */
    private $bundles;

    public function __construct($template, array $bundles)
    {
        $this->template = $template;
        $this->bundles = $bundles;
    }

    /**
     * Returns info provider identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'bundles';
    }

    /**
     * Returns information about eZ Platform, including installed bundles.
     *
     * @return Value\eZPlatform
     */
    public function getInfo()
    {
        $sortedBundles = $this->bundles;
        ksort($sortedBundles, SORT_FLAG_CASE | SORT_STRING);

        return new Value\EzPlatform([
            'eZPlatformVersion' => 'dev',
            'symfonyVersion' => Kernel::VERSION,
            'bundles' => $sortedBundles,
        ]);
    }
}
