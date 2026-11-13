<?php

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
