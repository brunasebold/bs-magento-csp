<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Magento\Framework\Serialize\Serializer;

/**
 * Stand-in for Magento\Framework\Serialize\Serializer\Json when Magento is not installed.
 */
class Json
{
    /**
     * @param mixed $data
     */
    public function serialize($data): string
    {
        $result = json_encode($data);
        if ($result === false) {
            throw new \InvalidArgumentException('Unable to serialize value.');
        }

        return $result;
    }

    /**
     * @param mixed $string
     * @return mixed
     */
    public function unserialize($string)
    {
        if (!is_string($string)) {
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }

        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }

        return $result;
    }
}
