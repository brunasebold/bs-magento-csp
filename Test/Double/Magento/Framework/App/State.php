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
 * Stand-in for Magento\Framework\App\State when Magento is not installed.
 */
class State
{
    public function __construct(
        private readonly string $areaCode = 'frontend',
        private readonly ?\Throwable $failure = null
    ) {
    }

    public function getAreaCode(): string
    {
        if ($this->failure !== null) {
            throw $this->failure;
        }

        return $this->areaCode;
    }
}
