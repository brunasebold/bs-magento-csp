<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Test\Unit\Model\Form;

use BS\MagentoCsp\Model\Form\Payload;
use PHPUnit\Framework\TestCase;

class PayloadTest extends TestCase
{
    private Payload $payload;

    protected function setUp(): void
    {
        $this->payload = new Payload();
    }

    public function testAFlatPostIsKept(): void
    {
        $data = ['policy_id' => 'script-src', 'host' => 'cdn.example.com'];

        $this->assertSame($data, $this->payload->extract($data));
    }

    public function testTheUiDataWrapperIsUnwrapped(): void
    {
        $fields = ['policy_id' => 'img-src', 'host' => 'cdn.example.com', 'whitelist_id' => '4'];

        $this->assertSame($fields, $this->payload->extract(['data' => $fields, 'form_key' => 'abc']));
    }

    public function testAFieldsetIsMergedOverTheWrapper(): void
    {
        $extracted = $this->payload->extract([
            'data' => [
                'whitelist_id' => '4',
                'general' => [
                    'host' => 'cdn.example.com',
                    'policy_id' => 'connect-src',
                    'note' => 'checkout',
                ],
            ],
        ]);

        $this->assertSame('4', $extracted['whitelist_id']);
        $this->assertSame('cdn.example.com', $extracted['host']);
        $this->assertSame('connect-src', $extracted['policy_id']);
        $this->assertSame('checkout', $extracted['note']);
    }

    public function testAHostAlreadyAtTheTopLevelIsNotReplacedByTheWrapper(): void
    {
        $extracted = $this->payload->extract([
            'host' => 'cdn.example.com',
            'data' => ['host' => 'other.example.com'],
        ]);

        $this->assertSame('cdn.example.com', $extracted['host']);
    }
}
