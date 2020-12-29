<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\Storage\Metrics;

use eZ\Publish\SPI\Persistence\Content\ContentInfo;

/**
 * @internal
 */
final class PublishedContentObjectsCountMetrics extends RepositoryConnectionAwareMetrics
{
    private const CONTENTOBJECT_TABLE = 'ezcontentobject';
    private const ID_COLUMN = 'id';
    private const STATUS_COLUMN = 'status';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getValue(): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select($this->getCountExpression(self::ID_COLUMN))
            ->from(self::CONTENTOBJECT_TABLE)
            ->where(
                $queryBuilder->expr()->eq(self::STATUS_COLUMN, ContentInfo::STATUS_PUBLISHED)
            );

        return (int) $queryBuilder->execute()->fetchColumn();
    }
}
