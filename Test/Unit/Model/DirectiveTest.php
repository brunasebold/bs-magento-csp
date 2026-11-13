<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Test\Unit\Model;

use BS\MagentoCsp\Model\Directive;
use PHPUnit\Framework\TestCase;

class DirectiveTest extends TestCase
{
    public function testTheListIsTheMagento24FetchDirectiveList(): void
    {
        $this->assertSame(
            [
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
            ],
            Directive::FETCH
        );
    }

    public function testMatchingIsCaseSensitive(): void
    {
        $this->assertTrue(Directive::isFetch('script-src'));
        $this->assertFalse(Directive::isFetch('Script-Src'));
        $this->assertFalse(Directive::isFetch('worker-src'));
        $this->assertFalse(Directive::isFetch(''));
    }
}
