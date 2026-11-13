<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Test\Unit\Model;

use BS\MagentoCsp\Model\ApplicableHosts;
use BS\MagentoCsp\Model\Area;
use PHPUnit\Framework\TestCase;

class ApplicableHostsTest extends TestCase
{
    private ApplicableHosts $hosts;

    protected function setUp(): void
    {
        $this->hosts = new ApplicableHosts();
    }

    public function testStorefrontReceivesGlobalAndStorefrontHosts(): void
    {
        $grouped = $this->hosts->group(Area::STOREFRONT, $this->rows());

        $this->assertSame(
            [
                'script-src' => ['cdn.example.com', 'https://pay.example.com'],
                'connect-src' => ['api.example.com'],
            ],
            $grouped
        );
    }

    public function testAdminDoesNotReceiveStorefrontHosts(): void
    {
        $grouped = $this->hosts->group(Area::ADMIN, $this->rows());

        $this->assertSame(
            [
                'script-src' => ['cdn.example.com', 'https://admin.example.com'],
                'connect-src' => ['api.example.com'],
            ],
            $grouped
        );
    }

    public function testOtherAreasReceiveOnlyGlobalHosts(): void
    {
        $this->assertSame(
            [
                'script-src' => ['cdn.example.com'],
                'connect-src' => ['api.example.com'],
            ],
            $this->hosts->group('crontab', $this->rows())
        );
        $this->assertSame(
            $this->hosts->group(Area::GLOBAL_AREA, $this->rows()),
            $this->hosts->group('webapi_rest', $this->rows())
        );
    }

    public function testUnknownDirectivesEmptyHostsAndDuplicatesAreLeftOut(): void
    {
        $grouped = $this->hosts->group(Area::STOREFRONT, [
            ['policy_id' => 'worker-src', 'host' => 'cdn.example.com', 'area' => 'global'],
            ['policy_id' => 'script-src', 'host' => '', 'area' => 'global'],
            ['policy_id' => 'script-src', 'host' => 'cdn.example.com', 'area' => 'global'],
            ['policy_id' => 'script-src', 'host' => 'cdn.example.com', 'area' => 'frontend'],
            ['policy_id' => 'script-src', 'host' => 'cdn.example.com', 'area' => 'somewhere'],
            'not-a-row',
            ['host' => 'cdn.example.com'],
        ]);

        $this->assertSame(['script-src' => ['cdn.example.com']], $grouped);
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
            ['policy_id' => 'connect-src', 'host' => 'api.example.com', 'area' => 'global'],
        ];
    }
}
