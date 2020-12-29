<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\Storage\Metrics;

use Doctrine\DBAL\ParameterType;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;

/**
 * @internal
 */
final class DraftsCountMetrics extends RepositoryConnectionAwareMetrics
{
    private const CONTENTOBJECT_VERSION_TABLE = 'ezcontentobject_version';
    private const CONTENTOBJECT_TABLE = 'ezcontentobject';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getValue(): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $expr = $queryBuilder->expr();

        $queryBuilder
            ->select($this->getCountExpression('v.id'))
            ->from(self::CONTENTOBJECT_VERSION_TABLE, 'v')
            ->innerJoin(
                'v',
                self::CONTENTOBJECT_TABLE,
                'c',
                $expr->and(
                    $expr->eq('c.id', 'v.contentobject_id'),
                    $expr->neq('c.status', ContentInfo::STATUS_TRASHED)
                )
            )
            ->where(
                $queryBuilder->expr()->eq('v.status', ':status')
            )
            ->setParameter('status', ContentInfo::STATUS_DRAFT, ParameterType::INTEGER);

        return (int) $queryBuilder->execute()->fetchColumn();
    }
}
