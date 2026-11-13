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
use BS\MagentoCsp\Model\Validation\Host;
use PHPUnit\Framework\TestCase;

class HostTest extends TestCase
{
    private Host $host;

    protected function setUp(): void
    {
        $this->host = new Host();
    }

    /**
     * @dataProvider validHosts
     */
    public function testStoresTheHostMagentoCanPutInADirective(string $input, string $expected): void
    {
        $normalized = $this->host->normalize($input);

        $this->assertSame($expected, $normalized);
        $this->assertSame($normalized, $this->host->normalize($normalized));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function validHosts(): array
    {
        $label = str_repeat('a', 63);

        return [
            'bare host' => ['  CDN.Example.COM  ', 'cdn.example.com'],
            'left wildcard' => ['*.Example.com', '*.example.com'],
            'nested wildcard' => ['*.cdn.example.com', '*.cdn.example.com'],
            'https url drops the path and query' => [
                'https://CDN.Example.com/static/app.js?v=1#top',
                'https://cdn.example.com',
            ],
            'http is preserved' => ['HTTP://chart.googleapis.com/chart', 'http://chart.googleapis.com'],
            'scheme and wildcard' => ['https://*.example.com/lib.js', 'https://*.example.com'],
            'explicit port' => ['cdn.example.com:8443', 'cdn.example.com:8443'],
            'port on a url' => ['https://cdn.example.com:8443/a', 'https://cdn.example.com:8443'],
            'leading zeros on a port' => ['cdn.example.com:0443', 'cdn.example.com:443'],
            'highest port' => ['cdn.example.com:65535', 'cdn.example.com:65535'],
            'localhost' => ['LocalHost', 'localhost'],
            'localhost port' => ['localhost:8080', 'localhost:8080'],
            'ipv4' => ['127.000.000.001', '127.0.0.1'],
            'ipv4 url' => ['http://127.0.0.1:0080/health', 'http://127.0.0.1:80'],
            'longest label' => [$label . '.com', $label . '.com'],
        ];
    }

    /**
     * @dataProvider rejectedHosts
     */
    public function testRejectsAnythingThatIsNotAHost(string $input, string $message): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage($message);

        $this->host->normalize($input);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function rejectedHosts(): array
    {
        $example = 'Use a host such as *.example.com, cdn.example.com or https://cdn.example.com.';
        $url = 'Enter a valid host. The path and the query string of a URL are discarded.';

        return [
            'empty' => ['   ', 'Enter a host.'],
            'single label' => ['example', $example],
            'path without a scheme' => ['cdn.example.com/app.js', $example],
            'wildcard in the middle' => ['cdn.*.example.com', $example],
            'wildcard localhost' => ['*.localhost', $example],
            'wildcard address' => ['*.127.0.0.1', $example],
            'self keyword' => ["'self'", $example],
            'star' => ['*', $example],
            'data scheme' => ['data:', $example],
            'javascript scheme' => ['javascript:alert(1)', $example],
            'port zero' => ['cdn.example.com:0', $example],
            'port above the range' => ['cdn.example.com:65536', $example],
            'octet above 255' => ['999.1.1.1', $example],
            'label too long' => [str_repeat('a', 64) . '.com', $example],
            'leading hyphen' => ['-cdn.example.com', $example],
            'trailing hyphen' => ['cdn-.example.com', $example],
            'longer than the column' => [str_repeat('a', 256), $example],
            'userinfo' => ['https://user:secret@cdn.example.com/a', $url],
            'user without a password' => ['https://user@cdn.example.com', $url],
            'scheme only' => ['https://', $url],
        ];
    }
}
