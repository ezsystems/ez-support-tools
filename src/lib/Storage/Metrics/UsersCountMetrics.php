<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\Storage\Metrics;

/**
 * @internal
 */
final class UsersCountMetrics extends RepositoryConnectionAwareMetrics
{
    private const USER_TABLE = 'ezuser';
    private const CONTENTOBJECT_ID_COLUMN = 'contentobject_id';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getValue(): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select($this->getCountExpression(self::CONTENTOBJECT_ID_COLUMN))
            ->from(self::USER_TABLE);

        return (int) $queryBuilder->execute()->fetchColumn();
    }
}
