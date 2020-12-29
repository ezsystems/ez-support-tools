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
final class VersionsCountMetrics extends RepositoryConnectionAwareMetrics
{
    private const CONTENTOBJECT_VERSION_TABLE = 'ezcontentobject_version';
    private const ID_COLUMN = 'id';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getValue(): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select($this->getCountExpression(self::ID_COLUMN))
            ->from(self::CONTENTOBJECT_VERSION_TABLE);

        return (int) $queryBuilder->execute()->fetchColumn();
    }
}
