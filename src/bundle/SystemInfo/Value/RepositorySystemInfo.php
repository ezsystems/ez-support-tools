<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Value;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Value for information about the database we are using.
 */
class RepositorySystemInfo extends ValueObject implements SystemInfo
{
    /**
     * Database type.
     *
     * Example: mysql
     *
     * @var string
     */
    public $type;

    /**
     * Database name.
     *
     * Example: ezp_platform
     *
     * @var string
     */
    public $name;

    /**
     * Database host.
     *
     * Example: localhost
     *
     * @var string
     */
    public $host;

    /**
     * Database username.
     *
     * Example: ezp_user
     *
     * @var string
     */
    public $username;

    /**
     * RepositoryMetrics contains counts of content objects, users etc.
     *
     * @var \EzSystems\EzSupportToolsBundle\SystemInfo\Value\RepositoryMetrics
     */
    public $repositoryMetrics;
}
