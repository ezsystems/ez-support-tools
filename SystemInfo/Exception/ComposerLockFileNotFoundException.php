<?php

/**
 * File containing the ComposerLockFileNotFoundException class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Exception;

use Exception;
use eZ\Publish\Core\Base\Exceptions\NotFoundException as BaseNotFoundException;

class ComposerLockFileNotFoundException extends BaseNotFoundException
{
    public function __construct($path, Exception $previous = null)
    {
        parent::__construct('composer.lock file', $path, $previous);
    }
}
