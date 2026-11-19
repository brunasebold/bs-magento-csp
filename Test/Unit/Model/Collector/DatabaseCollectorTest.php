<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Test\Unit\Model\Collector;

use BS\MagentoCsp\Model\Area;
use BS\MagentoCsp\Model\Collector\DatabaseCollector;
use BS\MagentoCsp\Test\Double\RecordingLogger;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;

class DatabaseCollectorTest extends TestCase
{
    public function testActiveHostsAreAppendedWithoutOpeningUnsafeSources(): void
    {
        $existing = new \stdClass();
        [$collector] = $this->collector([
            'frontend' => [
                'script-src' => ['cdn.example.com', 'https://pay.example.com'],
                'worker-src' => ['ignored.example.com'],
                'connect-src' => [],
            ],
        ]);

        $policies = $collector->collect([$existing]);

        $this->assertSame($existing, $policies[0]);
        $this->assertCount(2, $policies);
        $policy = $policies[1];
        $this->assertInstanceOf(FetchPolicy::class, $policy);
        $this->assertSame('script-src', $policy->getId());
        $this->assertSame(
            ['cdn.example.com', 'https://pay.example.com'],
            $policy->getHostSources()
        );
        $this->assertSame([], $policy->getSchemeSources());
        $this->assertFalse($policy->isNoneAllowed());
        $this->assertFalse($policy->isSelfAllowed());
        $this->assertFalse($policy->isInlineAllowed());
        $this->assertFalse($policy->isEvalAllowed());
    }

    public function testTheRequestAreaSelectsTheHostList(): void
    {
        [$collector, $provider] = $this->collector(
            ['adminhtml' => ['script-src' => ['admin.example.com']]],
            null,
            null,
            'adminhtml'
        );

        $policies = $collector->collect([]);

        $this->assertSame(['adminhtml'], $provider->areas);
        $this->assertSame(['admin.example.com'], $policies[0]->getHostSources());
    }

    public function testAMissingAreaUsesGlobalHosts(): void
    {
        [$collector, $provider] = $this->collector(
            [Area::GLOBAL_AREA => ['font-src' => ['fonts.example.com']]],
            new LocalizedException('Area code is not set')
        );

        $policies = $collector->collect([]);

        $this->assertSame([Area::GLOBAL_AREA], $provider->areas);
        $this->assertSame('font-src', $policies[0]->getId());
        $this->assertSame(['fonts.example.com'], $policies[0]->getHostSources());
    }

    public function testAnUnexpectedAreaFailureIsNotHidden(): void
    {
        [$collector] = $this->collector([], new \RuntimeException('state broken'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('state broken');

        $collector->collect([]);
    }

    public function testAReadFailureKeepsThePoliciesMagentoAlreadyBuilt(): void
    {
        $existing = new \stdClass();
        [$collector, $provider, $logger] = $this->collector(
            [],
            null,
            new \RuntimeException('database down')
        );

        $this->assertSame([$existing], $collector->collect([$existing]));
        $this->assertSame(['frontend'], $provider->areas);
        $this->assertSame('error', $logger->records[0][0]);
        $this->assertStringContainsString('could not be collected', $logger->records[0][1]);
        $this->assertInstanceOf(\RuntimeException::class, $logger->records[0][2]['exception']);
    }

    /**
     * @param array<string, array<string, list<string>>> $hosts
     * @return array{0: DatabaseCollector, 1: ScriptedHostProvider, 2: RecordingLogger}
     */
    private function collector(
        array $hosts,
        ?\Throwable $areaFailure = null,
        ?\Throwable $readFailure = null,
        string $area = 'frontend'
    ): array {
        $provider = new ScriptedHostProvider($hosts, $readFailure);
        $logger = new RecordingLogger();
        $collector = new DatabaseCollector($provider, new State($area, $areaFailure), $logger);

        return [$collector, $provider, $logger];
    }
}
