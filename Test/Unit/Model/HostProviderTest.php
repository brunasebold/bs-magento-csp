<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Test\Unit\Model;

use BS\MagentoCsp\Model\ApplicableHosts;
use BS\MagentoCsp\Model\HostProvider;
use BS\MagentoCsp\Test\Double\MemoryCache;
use BS\MagentoCsp\Test\Double\RecordingLogger;
use BS\MagentoCsp\Test\Double\WhitelistConnection;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\TestCase;

class HostProviderTest extends TestCase
{
    public function testStorefrontHostsAreReadCachedAndGrouped(): void
    {
        $connection = new WhitelistConnection(rows: $this->rows());
        $cache = new MemoryCache();
        $provider = $this->provider($connection, $cache, new RecordingLogger());

        $grouped = $provider->getHostsByDirective('frontend');

        $this->assertSame(
            ['script-src' => ['cdn.example.com', 'https://pay.example.com']],
            $grouped
        );
        $this->assertSame(
            [
                ['is_active = ?', 1],
                ['area IN (?)', ['global', 'frontend']],
            ],
            $connection->wheres
        );
        $this->assertSame(HostProvider::CACHE_TAG . '_frontend', $cache->stored[0]['identifier']);
        $this->assertSame([HostProvider::CACHE_TAG], $cache->stored[0]['tags']);
        $this->assertSame(86400, $cache->stored[0]['lifeTime']);
        $this->assertSame($grouped, json_decode($cache->stored[0]['data'], true));
    }

    public function testACacheHitDoesNotReadTheTable(): void
    {
        $payload = json_encode(['font-src' => ['fonts.example.com']]);
        $connection = new WhitelistConnection();
        $cache = new MemoryCache([HostProvider::CACHE_TAG . '_frontend' => $payload]);
        $provider = $this->provider($connection, $cache, new RecordingLogger());

        $this->assertSame(
            ['font-src' => ['fonts.example.com']],
            $provider->getHostsByDirective('frontend')
        );
        $this->assertSame(0, $connection->reads);
        $this->assertSame([], $cache->stored);
    }

    /**
     * @dataProvider unusableCache
     */
    public function testAnUnusableCacheEntryReadsTheTable(mixed $cached): void
    {
        $connection = new WhitelistConnection(rows: $this->rows());
        $provider = $this->provider($connection, new MemoryCache($cached), new RecordingLogger());

        $this->assertSame(
            ['script-src' => ['cdn.example.com', 'https://pay.example.com']],
            $provider->getHostsByDirective('frontend')
        );
        $this->assertSame(1, $connection->reads);
    }

    public function testAMissingTableIsCachedAsAnEmptyList(): void
    {
        $connection = new WhitelistConnection(tableExists: false);
        $cache = new MemoryCache();
        $provider = $this->provider($connection, $cache, new RecordingLogger());

        $this->assertSame([], $provider->getHostsByDirective('frontend'));
        $this->assertSame(0, $connection->reads);
        $this->assertSame('[]', $cache->stored[0]['data']);
    }

    public function testADatabaseFailureReturnsNoHostsAndSkipsTheCache(): void
    {
        $connection = new WhitelistConnection(failure: new \RuntimeException('database down'));
        $cache = new MemoryCache();
        $logger = new RecordingLogger();
        $provider = $this->provider($connection, $cache, $logger);

        $this->assertSame([], $provider->getHostsByDirective('frontend'));
        $this->assertSame([], $cache->stored);
        $this->assertSame('error', $logger->records[0][0]);
        $this->assertStringContainsString('could not be read', $logger->records[0][1]);
        $this->assertInstanceOf(\RuntimeException::class, $logger->records[0][2]['exception']);
    }

    public function testACacheWriteFailureStillReturnsTheHosts(): void
    {
        $connection = new WhitelistConnection(rows: $this->rows());
        $logger = new RecordingLogger();
        $provider = $this->provider(
            $connection,
            new MemoryCache(saveFailure: new \RuntimeException('cache down')),
            $logger
        );

        $this->assertSame(
            ['script-src' => ['cdn.example.com', 'https://pay.example.com']],
            $provider->getHostsByDirective('frontend')
        );
        $this->assertSame('warning', $logger->records[0][0]);
        $this->assertStringContainsString('could not be cached', $logger->records[0][1]);
    }

    /**
     * @return array<string, array{0: mixed}>
     */
    public function unusableCache(): array
    {
        return [
            'miss' => [false],
            'empty string' => [''],
            'broken json' => ['{'],
            'scalar json' => ['1'],
        ];
    }

    private function provider(
        WhitelistConnection $connection,
        MemoryCache $cache,
        RecordingLogger $logger
    ): HostProvider {
        return new HostProvider(
            new ResourceConnection($connection),
            $cache,
            new Json(),
            new ApplicableHosts(),
            $logger
        );
    }

    /**
     * @return list<array{policy_id: string, host: string, area: string}>
     */
    private function rows(): array
    {
        return [
            ['policy_id' => 'script-src', 'host' => 'cdn.example.com', 'area' => 'global'],
            ['policy_id' => 'script-src', 'host' => 'https://pay.example.com', 'area' => 'frontend'],
            ['policy_id' => 'script-src', 'host' => 'https://admin.example.com', 'area' => 'adminhtml'],
        ];
    }
}
