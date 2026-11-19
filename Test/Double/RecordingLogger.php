<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Test\Double;

use Psr\Log\LoggerInterface;

class RecordingLogger implements LoggerInterface
{
    /** @var list<array{0: string, 1: string, 2: array<string, mixed>}> */
    public array $records = [];

    /**
     * @param array<string, mixed> $context
     */
    public function error($message, array $context = []): void
    {
        $this->records[] = ['error', (string) $message, $context];
    }

    /**
     * @param array<string, mixed> $context
     */
    public function warning($message, array $context = []): void
    {
        $this->records[] = ['warning', (string) $message, $context];
    }
}
