<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Test\Unit\Model\Validation;

use BS\MagentoCsp\Exception\ValidationException;
use BS\MagentoCsp\Model\Area;
use BS\MagentoCsp\Model\Validation\Host;
use BS\MagentoCsp\Model\Validation\WhitelistEntry;
use PHPUnit\Framework\TestCase;

class WhitelistEntryTest extends TestCase
{
    private WhitelistEntry $entry;

    protected function setUp(): void
    {
        $this->entry = new WhitelistEntry(new Host());
    }

    public function testNormalizesTheRowThatWillBeStored(): void
    {
        $normalized = $this->entry->normalize(
            'script-src',
            ' HTTPS://CDN.Example.com/app.js ',
            Area::STOREFRONT,
            '1',
            '  analytics  '
        );

        $this->assertSame(
            [
                'policy_id' => 'script-src',
                'host' => 'https://cdn.example.com',
                'area' => 'frontend',
                'is_active' => 1,
                'note' => 'analytics',
            ],
            $normalized
        );
    }

    /**
     * @param mixed $isActive
     * @dataProvider inactiveValues
     */
    public function testOnlyOneMeansActive(mixed $isActive): void
    {
        $normalized = $this->entry->normalize('img-src', 'cdn.example.com', Area::GLOBAL_AREA, $isActive, null);

        $this->assertSame(0, $normalized['is_active']);
        $this->assertNull($normalized['note']);
    }

    /**
     * @return array<string, array{0: mixed}>
     */
    public static function inactiveValues(): array
    {
        return [
            'zero' => [0],
            'string zero' => ['0'],
            'false' => [false],
            'null' => [null],
            'word' => ['yes'],
            'two' => [2],
        ];
    }

    public function testADirectiveMustMatchMagentoFetchPolicies(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Choose a valid directive.');

        $this->entry->normalize('Script-Src', 'cdn.example.com', Area::GLOBAL_AREA, 1, null);
    }

    public function testAnAreaMustBeGlobalStorefrontOrAdmin(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Choose a valid area.');

        $this->entry->normalize('script-src', 'cdn.example.com', 'crontab', 1, null);
    }

    public function testANoteCountsCharactersRatherThanBytes(): void
    {
        $note = str_repeat('á', 255);
        $normalized = $this->entry->normalize('font-src', 'cdn.example.com', Area::ADMIN, true, $note);

        $this->assertSame($note, $normalized['note']);
        $this->assertSame(1, $normalized['is_active']);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The note can have at most 255 characters.');

        $this->entry->normalize('font-src', 'cdn.example.com', Area::ADMIN, 1, $note . 'á');
    }

    public function testAnInvalidHostIsReportedAsAHostError(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Enter a host.');

        $this->entry->normalize('connect-src', '   ', Area::GLOBAL_AREA, 1, null);
    }
}
