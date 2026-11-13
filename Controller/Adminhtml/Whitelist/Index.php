<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Controller\Adminhtml\Whitelist;

use BS\MagentoCsp\Controller\Adminhtml\Whitelist;
use Magento\Framework\Controller\ResultFactory;

class Index extends Whitelist
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('BS_MagentoCsp::whitelist');
        $resultPage->getConfig()->getTitle()->prepend(__('CSP Whitelist'));

        return $resultPage;
    }
}
