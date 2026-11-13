<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Controller\Adminhtml\Whitelist;

use BS\MagentoCsp\Controller\Adminhtml\Whitelist;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends Whitelist
{
    public function execute()
    {
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $resultForward->forward('edit');
    }
}
