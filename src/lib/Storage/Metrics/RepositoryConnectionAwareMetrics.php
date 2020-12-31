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
abstract class RepositoryConnectionAwareMetrics implements Metrics
{
    /** @var \Doctrine\DBAL\Connection */
    protected $connection;

    abstract public function getValue(): int;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function getCountExpression(string $columnName): string
    {
        return $this->connection->getDatabasePlatform()->getCountExpression($columnName);
    }
}
