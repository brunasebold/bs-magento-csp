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
 * Fetch directives accepted by Magento_Csp on the 2.4 line.
 *
 * This is Magento\Csp\Model\Policy\FetchPolicy::POLICIES. The copy lives here so the
 * admin and the unit tests can reject an unknown directive without booting Magento.
 */
class Directive
{
    public const FETCH = [
        'default-src',
        'child-src',
        'connect-src',
        'font-src',
        'frame-src',
        'img-src',
        'manifest-src',
        'media-src',
        'object-src',
        'script-src',
        'style-src',
        'base-uri',
        'form-action',
        'frame-ancestors',
    ];

    public static function isFetch(string $directive): bool
    {
        return in_array($directive, self::FETCH, true);
    }
}
