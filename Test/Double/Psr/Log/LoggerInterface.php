<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Psr\Log;

/**
 * Stand-in for the PSR-3 methods this module calls when psr/log is not installed.
 */
interface LoggerInterface
{
    /**
     * @param string|\Stringable $message
     * @param array<string, mixed> $context
     */
    public function error($message, array $context = []);

    /**
     * @param string|\Stringable $message
     * @param array<string, mixed> $context
     */
    public function warning($message, array $context = []);
}
