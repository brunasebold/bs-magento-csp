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
 * Stand-in for the cache methods this module calls when Magento is not installed.
 */
interface CacheInterface
{
    /**
     * @param string $identifier
     * @return string|false
     */
    public function load($identifier);

    /**
     * @param string $data
     * @param string $identifier
     * @param list<string> $tags
     * @param int|null $lifeTime
     */
    public function save($data, $identifier, $tags = [], $lifeTime = null);
}
