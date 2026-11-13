<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Whitelist extends Action
{
    public const ADMIN_RESOURCE = 'BS_MagentoCsp::whitelist';
}
