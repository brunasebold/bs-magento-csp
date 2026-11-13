<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Model\Form;

/**
 * Reads the host fields out of the UI form post.
 */
class Payload
{
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function extract(array $data): array
    {
        if (isset($data['data']) && is_array($data['data']) && !isset($data['host'])) {
            $data = $data['data'];
        }
        if (isset($data['general']) && is_array($data['general'])) {
            $data = array_merge($data, $data['general']);
        }

        return $data;
    }
}
