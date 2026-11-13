<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Test\Unit\Model;

use BS\MagentoCsp\Model\Area;
use PHPUnit\Framework\TestCase;

class AreaTest extends TestCase
{
    public function testGlobalHostsApplyToEveryResponseArea(): void
    {
        $this->assertSame(['global', 'frontend'], Area::codesFor(Area::STOREFRONT));
        $this->assertSame(['global', 'adminhtml'], Area::codesFor(Area::ADMIN));
        $this->assertSame(['global'], Area::codesFor(Area::GLOBAL_AREA));
        $this->assertSame(['global'], Area::codesFor('crontab'));
    }

    public function testTheStoredCodesAreTheOnesTheAdminCanChoose(): void
    {
        $this->assertSame(['global', 'frontend', 'adminhtml'], Area::CODES);
    }
}
