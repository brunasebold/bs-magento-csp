<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Magento\Framework\App;

/**
 * Stand-in for Magento\Framework\App\ResourceConnection when Magento is not installed.
 */
class ResourceConnection
{
    public function __construct(
        private readonly object $connection,
        private readonly string $tableName = 'bs_csp_whitelist'
    ) {
    }

    public function getConnection(): object
    {
        return $this->connection;
    }

    /**
     * @param string $modelEntity
     * @param string|null $connectionName
     */
    public function getTableName($modelEntity, $connectionName = null): string
    {
        return $this->tableName;
    }
}
