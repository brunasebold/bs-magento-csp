<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Magento\Csp\Api;

/**
 * Stand-in for Magento\Csp\Api\PolicyCollectorInterface when Magento is not installed.
 */
interface PolicyCollectorInterface
{
    /**
     * @param array<int|string, mixed> $defaultPolicies
     * @return array<int|string, mixed>
     */
    public function collect(array $defaultPolicies = []): array;
}
