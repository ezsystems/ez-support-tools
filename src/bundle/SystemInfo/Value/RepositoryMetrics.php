<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Value;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Value for various repository metrics (content object count, user count etc.).
 */
class RepositoryMetrics extends ValueObject implements SystemInfo
{
    /**
     * Count of all published content objects.
     *
     * @var int
     */
    public $publishedCount;

    /**
     * Count of users.
     *
     * @var int
     */
    public $usersCount;

    /**
     * Count of drafts.
     *
     * @var int
     */
    public $draftsCount;

    /**
     * Total count of versions.
     *
     * @var int
     */
    public $versionsCount;

    /**
     * Count of Content Types.
     *
     * @var int
     */
    public $contentTypesCount;
}
