<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Test\Double;

class WhitelistSelect
{
    /** @var list<array{0: string, 1: mixed}> */
    public array $wheres = [];

    /**
     * @param string $table
     * @param list<string>|string $columns
     */
    public function from($table, $columns = '*'): self
    {
        return $this;
    }

    public function where($condition, $value = null): self
    {
        $this->wheres[] = [$condition, $value];

        return $this;
    }
}
