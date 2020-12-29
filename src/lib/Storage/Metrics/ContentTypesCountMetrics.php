<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\Storage\Metrics;

use Doctrine\DBAL\Connection;
use eZ\Publish\SPI\Persistence\Content\Type;
use EzSystems\EzSupportTools\Storage\Metrics;

final class ContentTypesCountMetrics implements Metrics
{
    private const CONTENT_TYPE_TABLE = 'ezcontentclass';
    private const ID_COLUMN = 'id';
    private const VERSION_COLUMN = 'version';

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
            ->from(self::CONTENT_TYPE_TABLE)
            ->where(
                $queryBuilder->expr()->eq(self::VERSION_COLUMN, Type::STATUS_DEFINED)
            );

        return (int) $queryBuilder->execute()->fetchColumn();
    }
}
