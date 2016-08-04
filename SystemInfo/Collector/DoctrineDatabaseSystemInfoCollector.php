<?php

/**
 * File containing the DoctrineDatabaseSystemInfoCollector class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\SystemInfo\Collector;

use Doctrine\DBAL\Connection;
use Doctrine\Bundle\DoctrineBundle\Registry as DoctrineRegistry;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzSupportToolsBundle\SystemInfo\Value;

/**
 * Collects database information using Doctrine.
 */
class DoctrineDatabaseSystemInfoCollector implements SystemInfoCollector
{
   /**
     * @var SiteAccess
     */
    private $siteAccess;

    /**
     * @var \Doctrine\DBAL\Connection[]
     */
    private $connectionList;

    /**
     * The database connection, only used to retrieve some information on the database itself.
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

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
        $this->connection = $this->connectionList[$this->siteAccess->name];

        return new Value\DatabaseSystemInfo([
            'type' => $this->connection->getDatabasePlatform()->getName(),
            'name' => $this->connection->getDatabase(),
            'host' => $this->connection->getHost(),
            'username' => $this->connection->getUsername(),
        ]);
    }

    public function setSiteAccess(SiteAccess $siteAccess = null)
    {
        $this->siteAccess = $siteAccess;
        $this->defaultScope = $siteAccess->name;
    }

    public function setDatabaseList( DoctrineRegistry $databaseList )
    {
        $this->connectionList = $databaseList->getConnections();
    }
}
