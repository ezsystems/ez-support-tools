<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\Storage\Metrics;

use Doctrine\DBAL\Connection;
use EzSystems\EzSupportTools\Storage\Metrics;

/**
 * @internal
 */
final class UsersCountMetrics implements Metrics
{
    private const USER_TABLE = 'ezuser';
    private const CONTENTOBJECT_ID_COLUMN = 'contentobject_id';

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
            ->select($this->databasePlatform->getCountExpression(self::CONTENTOBJECT_ID_COLUMN))
            ->from(self::USER_TABLE);

        return (int) $queryBuilder->execute()->fetchColumn();
    }
}
