<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Exception;

use Exception;
use eZ\Publish\Core\Base\Exceptions\NotFoundException as BaseNotFoundException;

class MetricsNotFoundException extends BaseNotFoundException
{
    public function __construct(string $identifier, Exception $previous = null)
    {
        parent::__construct('Metrics', $identifier, $previous);
    }
}
