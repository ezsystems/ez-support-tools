<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\Component\Dashboard;

use EzSystems\EzPlatformAdminUi\Component\Renderable;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value\IbexaSystemInfo;
use Twig\Environment;

class EzInfoTwigComponent implements Renderable
{
    /** @var string */
    protected $template;

    /** @var \Twig\Environment */
    protected $twig;

    /** @var array */
    protected $parameters;

    /** @var \EzSystems\EzSupportToolsBundle\SystemInfo\Value\IbexaSystemInfo */
    private $ibexaSystemInfo;

    /** @var array */
    private $urlList;

    /**
     * @param \Twig\Environment $twig
     * @param string $template
     * @param \EzSystems\EzSupportToolsBundle\SystemInfo\Value\IbexaSystemInfo $ibexaSystemInfo
     * @param array $urlList
     * @param array $parameters
     */
    public function __construct(
        Environment $twig,
        string $template,
        IbexaSystemInfo $ibexaSystemInfo,
        array $urlList,
        array $parameters = []
    ) {
        $this->twig = $twig;
        $this->template = $template;
        $this->parameters = $parameters;
        $this->ibexaSystemInfo = $ibexaSystemInfo;
        $this->urlList = $urlList;
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function render(array $parameters = []): string
    {
        $urls = $this->replaceUrlPlaceholders();

        return $this->twig->render(
            $this->template,
            $parameters + ['urls' => $urls, 'ez' => $this->ibexaSystemInfo] + $this->parameters
        );
    }

    /**
     * @return array
     */
    private function replaceUrlPlaceholders(): array
    {
        $urls = $this->urlList;
        foreach ($this->urlList as $urlName => $url) {
            foreach ($this->ibexaSystemInfo as $attribute => $value) {
                if (\is_string($value) && \strpos($url, '{ez.' . $attribute . '}') !== false) {
                    $urls[$urlName] = \str_replace('{ez.' . $attribute . '}', $value, $url);
                }
            }
        }

        return $urls;
    }
}
