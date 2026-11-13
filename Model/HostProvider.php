<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Active hosts for one area, cached so the CSP header does not query on every hit.
 */
class HostProvider
{
    public const CACHE_TAG = 'bs_csp_whitelist';

    private const CACHE_LIFETIME = 86400;

    public function __construct(
        private readonly ResourceConnection $resource,
        private readonly CacheInterface $cache,
        private readonly Json $serializer,
        private readonly ApplicableHosts $applicableHosts,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @return array<string, list<string>>
     */
    public function getHostsByDirective(string $areaCode): array
    {
        $cacheKey = self::CACHE_TAG . '_' . $areaCode;
        $cached = $this->load($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        try {
            $rows = $this->readRows($areaCode);
        } catch (\Throwable $exception) {
            $this->logger->error(
                'The CSP whitelist could not be read. The response will be sent without the database hosts.',
                ['exception' => $exception]
            );

            return [];
        }

        $grouped = $this->applicableHosts->group($areaCode, $rows);
        $this->store($cacheKey, $grouped);

        return $grouped;
    }

    /**
     * @return array<string, list<string>>|null
     */
    private function load(string $cacheKey): ?array
    {
        $cached = $this->cache->load($cacheKey);
        if (!is_string($cached) || $cached === '') {
            return null;
        }

        try {
            $decoded = $this->serializer->unserialize($cached);
        } catch (\Throwable) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function readRows(string $areaCode): array
    {
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('bs_csp_whitelist');
        if (!$connection->isTableExists($table)) {
            return [];
        }

        return $connection->fetchAll(
            $connection->select()
                ->from($table, ['policy_id', 'host', 'area'])
                ->where('is_active = ?', 1)
                ->where('area IN (?)', Area::codesFor($areaCode))
        );
    }

    /**
     * @param array<string, list<string>> $grouped
     */
    private function store(string $cacheKey, array $grouped): void
    {
        try {
            $this->cache->save(
                $this->serializer->serialize($grouped),
                $cacheKey,
                [self::CACHE_TAG],
                self::CACHE_LIFETIME
            );
        } catch (\Throwable $exception) {
            $this->logger->warning(
                'The CSP whitelist could not be cached. The hosts were still applied to this response.',
                ['exception' => $exception]
            );
        }
    }
}
