<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Test\Double;

use Magento\Framework\App\CacheInterface;

class MemoryCache implements CacheInterface
{
    /** @var list<array{data: string, identifier: string, tags: array<int, string>, lifeTime: int|null}> */
    public array $stored = [];

    public function __construct(
        private readonly mixed $loaded = false,
        private readonly ?\Throwable $saveFailure = null
    ) {
    }

    public function load($identifier)
    {
        if (is_array($this->loaded)) {
            return $this->loaded[$identifier] ?? false;
        }

        return $this->loaded;
    }

    /**
     * @param list<string> $tags
     */
    public function save($data, $identifier, $tags = [], $lifeTime = null): void
    {
        if ($this->saveFailure !== null) {
            throw $this->saveFailure;
        }

        $this->stored[] = [
            'data' => $data,
            'identifier' => $identifier,
            'tags' => $tags,
            'lifeTime' => $lifeTime,
        ];
    }
}
