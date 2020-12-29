<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\Storage\Metrics;

use Doctrine\DBAL\Connection;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use EzSystems\EzSupportTools\Storage\Metrics;

final class PublishedContentObjectsCountMetrics implements Metrics
{
    private const CONTENTOBJECT_TABLE = 'ezcontentobject';
    private const ID_COLUMN = 'id';
    private const STATUS_COLUMN = 'status';

    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    /** @var \Doctrine\DBAL\Platforms\AbstractPlatform */
    private $databasePlatform;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->databasePlatform = $connection->getDatabasePlatform();
    }

    public function getValue(): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select($this->databasePlatform->getCountExpression(self::ID_COLUMN))
            ->from(self::CONTENTOBJECT_TABLE)
            ->where(
                $queryBuilder->expr()->eq(self::STATUS_COLUMN, ContentInfo::STATUS_PUBLISHED)
            );

        return (int) $queryBuilder->execute()->fetchColumn();
    }
}
