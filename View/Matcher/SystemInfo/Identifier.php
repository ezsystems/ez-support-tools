<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\View\Matcher\SystemInfo;

use Doctrine\Common\Inflector\Inflector;
use eZ\Publish\Core\MVC\Symfony\Matcher\ViewMatcherInterface;
use eZ\Publish\Core\MVC\Symfony\View\View;
use EzSystems\EzSupportToolsBundle\View\SystemInfoView;

class Identifier implements ViewMatcherInterface
{
    /**
     * Matched SystemInfo identifier. Example: 'php', 'hardware'...
     *
     * @var string
     */
    private $identifier;

    /**
     * Registers the matching configuration for the matcher.
     * It's up to the implementor to validate $matchingConfig since it can be anything configured by the end-developer.
     *
     * @param mixed $matchingConfig
     *
     * @throws \InvalidArgumentException Should be thrown if $matchingConfig is not valid.
     */
    public function setMatchingConfig($matchingConfig)
    {
        $this->identifier = $matchingConfig;
    }

    /**
     * Matches the $view against a set of matchers.
     *
     * @param \EzSystems\EzSupportToolsBundle\View\SystemInfoView $view
     *
     * @return bool
     */
    public function match(View $view)
    {
        if (!$view instanceof SystemInfoView) {
            return false;
        }

        return $this->toIdentifier($view->getInfo()) === $this->identifier;
    }

    private function toIdentifier($object)
    {
        $className = \get_class($object);
        $className = substr($className, strrpos($className, '\\') + 1);
        $className = str_replace('SystemInfo', '', $className);

        return Inflector::tableize($className);
    }
}
