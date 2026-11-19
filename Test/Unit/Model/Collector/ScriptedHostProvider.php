<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Test\Unit\Model\Collector;

use BS\MagentoCsp\Model\ApplicableHosts;
use BS\MagentoCsp\Model\HostProvider;
use BS\MagentoCsp\Test\Double\RecordingLogger;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Host list with a fixed result, so the collector test does not touch a database.
 */
class ScriptedHostProvider extends HostProvider
{
    /** @var list<string> */
    public array $areas = [];

    /**
     * @param array<string, array<string, list<string>>> $hostsByArea
     */
    public function __construct(private readonly array $hostsByArea, private readonly ?\Throwable $failure = null)
    {
        parent::__construct(
            new ResourceConnection(new class {
                public function isTableExists($table): bool
                {
                    return false;
                }
            }),
            new class implements CacheInterface {
                public function load($identifier)
                {
                    return false;
                }

                /**
                 * @param list<string> $tags
                 */
                public function save($data, $identifier, $tags = [], $lifeTime = null): void
                {
                }
            },
            new Json(),
            new ApplicableHosts(),
            new RecordingLogger()
        );
    }

    /**
     * @return array<string, list<string>>
     */
    public function getHostsByDirective(string $areaCode): array
    {
        $this->areas[] = $areaCode;
        if ($this->failure !== null) {
            throw $this->failure;
        }

        return $this->hostsByArea[$areaCode] ?? [];
    }
}
