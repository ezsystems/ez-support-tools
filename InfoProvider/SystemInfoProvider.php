<?php

/**
 * File containing the SystemInfoProvider class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzSupportToolsBundle\InfoProvider;

use Doctrine\DBAL\Connection;
use ezcSystemInfo;

class SystemInfoProvider extends InfoProvider
{
    /**
     * The database connection, only used to retrieve some information on the database itself.
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function __construct($template, Connection $db)
    {
        $this->template = $template;
        $this->connection = $db;
    }

    /**
     * Returns info provider identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'server';
    }

    /**
     * Returns information about the system eZ Platform is installed on.
     *  - cpu information
     *  - memory size
     *  - php version
     *  - php accelerator info
     *  - database related info.
     *
     * @return Value\System
     */
    public function getInfo()
    {
        $info = ezcSystemInfo::getInstance();
        $accelerator = false;
        if ($info->phpAccelerator) {
            $accelerator = [
                'name' => $info->phpAccelerator->name,
                'url' => $info->phpAccelerator->url,
                'enabled' => $info->phpAccelerator->isEnabled,
                'versionString' => $info->phpAccelerator->versionString,
            ];
        }

        return new Value\System([
            'cpuType' => $info->cpuType,
            'cpuSpeed' => $info->cpuSpeed,
            'cpuCount' => $info->cpuCount,
            'memorySize' => $info->memorySize,
            'phpVersion' => phpversion(),
            'phpAccelerator' => $accelerator,
            'database' => [
                'type' => $this->connection->getDatabasePlatform()->getName(),
                'name' => $this->connection->getDatabase(),
                'host' => $this->connection->getHost(),
                'username' => $this->connection->getUsername(),
            ],
        ]);
    }
}
