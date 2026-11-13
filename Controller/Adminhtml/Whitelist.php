<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Whitelist extends Action
{
    public const ADMIN_RESOURCE = 'BS_MagentoCsp::whitelist';
}
