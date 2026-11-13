<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Model\ResourceModel\Whitelist;

use BS\MagentoCsp\Model\ResourceModel\Whitelist as WhitelistResource;
use BS\MagentoCsp\Model\Whitelist;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(Whitelist::class, WhitelistResource::class);
    }
}
