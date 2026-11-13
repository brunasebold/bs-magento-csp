<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Model;

use BS\MagentoCsp\Model\ResourceModel\Whitelist as WhitelistResource;
use Magento\Framework\Model\AbstractModel;

class Whitelist extends AbstractModel
{
    public const PERSISTOR_KEY = 'bs_csp_whitelist';

    protected function _construct(): void
    {
        $this->_init(WhitelistResource::class);
    }
}
