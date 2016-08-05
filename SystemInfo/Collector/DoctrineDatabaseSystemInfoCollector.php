<?php

/**
 * File containing the DoctrineDatabaseSystemInfoCollector class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Collector;

use Doctrine\DBAL\Connection;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value;

/**
 * Collects database information using Doctrine.
 */
class DoctrineDatabaseSystemInfoCollector implements SystemInfoCollector
{
    /**
     * The database connection, only used to retrieve some information on the database itself.
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function __construct(Connection $db)
    {
        $this->connection = $db;
    }

    /**
     * Collects information about the database eZ Platform is using.
     *  - type
     *  - name
     *  - host
     *  - username
     *
     * @return Value\DatabaseSystemInfo
     */
    public function collect()
    {
        return new Value\DatabaseSystemInfo([
            'type' => $this->connection->getDatabasePlatform()->getName(),
            'name' => $this->connection->getDatabase(),
            'host' => $this->connection->getHost(),
            'username' => $this->connection->getUsername(),
        ]);
    }
}
