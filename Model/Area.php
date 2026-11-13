<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Model;

/**
 * Areas stored on a whitelist row. Storefront and admin match Magento area codes.
 */
class Area
{
    public const GLOBAL_AREA = 'global';

    public const STOREFRONT = 'frontend';

    public const ADMIN = 'adminhtml';

    public const CODES = [
        self::GLOBAL_AREA,
        self::STOREFRONT,
        self::ADMIN,
    ];

    /**
     * Global hosts apply to every area. A storefront or admin host applies only there.
     *
     * @return list<string>
     */
    public static function codesFor(string $areaCode): array
    {
        $areas = [self::GLOBAL_AREA];
        if (in_array($areaCode, [self::STOREFRONT, self::ADMIN], true)) {
            $areas[] = $areaCode;
        }

        return $areas;
    }
}
