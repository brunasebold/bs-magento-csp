<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Model\Validation;

use BS\MagentoCsp\Exception\ValidationException;
use BS\MagentoCsp\Model\Area;
use BS\MagentoCsp\Model\Directive;

/**
 * Normalizes one whitelist row before it is stored.
 */
class WhitelistEntry
{
    private const NOTE_MAX = 255;

    public function __construct(private readonly Host $host)
    {
    }

    /**
     * @return array{policy_id: string, host: string, area: string, is_active: int, note: string|null}
     */
    public function normalize(
        string $policyId,
        string $host,
        string $area,
        mixed $isActive,
        ?string $note
    ): array {
        if (!Directive::isFetch($policyId)) {
            throw new ValidationException('Choose a valid directive.');
        }
        if (!in_array($area, Area::CODES, true)) {
            throw new ValidationException('Choose a valid area.');
        }

        $note = trim((string) $note);
        if (mb_strlen($note) > self::NOTE_MAX) {
            throw new ValidationException('The note can have at most 255 characters.');
        }

        return [
            'policy_id' => $policyId,
            'host' => $this->host->normalize($host),
            'area' => $area,
            'is_active' => (int) $isActive === 1 ? 1 : 0,
            'note' => $note === '' ? null : $note,
        ];
    }
}
