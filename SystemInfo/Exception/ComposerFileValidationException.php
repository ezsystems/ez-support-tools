<?php

/**
 * File containing the ComposerJsonFileNotFoundException class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Exception;

use Exception;

class ComposerFileValidationException extends Exception
{
    public function __construct(string $path, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Composer file %s is not valid.', $path);

        parent::__construct($message, $code, $previous);
    }
}
