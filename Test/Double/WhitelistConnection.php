<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Test\Double;

class WhitelistConnection
{
    /** @var list<array{0: string, 1: mixed}> */
    public array $wheres = [];

    public int $reads = 0;

    /**
     * @param list<array<string, mixed>> $rows
     */
    public function __construct(
        private readonly bool $tableExists = true,
        private readonly array $rows = [],
        private readonly ?\Throwable $failure = null
    ) {
    }

    public function isTableExists($table): bool
    {
        return $this->tableExists;
    }

    public function select(): WhitelistSelect
    {
        return new WhitelistSelect();
    }

    public function fetchAll(WhitelistSelect $select): array
    {
        $this->reads++;
        $this->wheres = $select->wheres;
        if ($this->failure !== null) {
            throw $this->failure;
        }

        return $this->rows;
    }
}
