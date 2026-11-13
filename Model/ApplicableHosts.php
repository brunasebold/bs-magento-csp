<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Model;

/**
 * Chooses the hosts a response area should send.
 */
class ApplicableHosts
{
    /**
     * @param list<array<string, mixed>> $rows
     * @return array<string, list<string>>
     */
    public function group(string $areaCode, array $rows): array
    {
        $areas = Area::codesFor($areaCode);
        $grouped = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $policyId = (string) ($row['policy_id'] ?? '');
            $host = (string) ($row['host'] ?? '');
            $area = (string) ($row['area'] ?? '');
            if ($host === '' || !in_array($area, $areas, true) || !Directive::isFetch($policyId)) {
                continue;
            }
            if (isset($grouped[$policyId]) && in_array($host, $grouped[$policyId], true)) {
                continue;
            }

            $grouped[$policyId][] = $host;
        }

        return $grouped;
    }
}
