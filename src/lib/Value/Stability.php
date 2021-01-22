<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\Value;

final class Stability
{
    /**
     * @var array Hash of stability constant values to human readable stabilities, see Composer\Package\BasePackage.
     *
     * Needed as long as we don't want to depend on Composer.
     */
    public const STABILITIES = [
        0 => 'stable',
        5 => 'RC',
        10 => 'beta',
        15 => 'alpha',
        20 => 'dev',
    ];
}
