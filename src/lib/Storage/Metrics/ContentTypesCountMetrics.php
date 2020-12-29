<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\Storage\Metrics;

use eZ\Publish\SPI\Persistence\Content\Type;

/**
 * @internal
 */
final class ContentTypesCountMetrics extends RepositoryConnectionAwareMetrics
{
    private const CONTENT_TYPE_TABLE = 'ezcontentclass';
    private const ID_COLUMN = 'id';
    private const VERSION_COLUMN = 'version';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getValue(): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select($this->getCountExpression(self::ID_COLUMN))
            ->from(self::CONTENT_TYPE_TABLE)
            ->where(
                $queryBuilder->expr()->eq(self::VERSION_COLUMN, Type::STATUS_DEFINED)
            );

        return (int) $queryBuilder->execute()->fetchColumn();
    }
}
