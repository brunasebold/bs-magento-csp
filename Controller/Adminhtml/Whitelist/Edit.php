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
use BS\MagentoCsp\Model\ResourceModel\Whitelist as WhitelistResource;
use BS\MagentoCsp\Model\WhitelistFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Edit extends Whitelist
{
    public function __construct(
        Context $context,
        private readonly WhitelistFactory $whitelistFactory,
        private readonly WhitelistResource $whitelistResource
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('whitelist_id');
        if ($id) {
            $model = $this->whitelistFactory->create();
            $this->whitelistResource->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This host does not exist.'));

                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('BS_MagentoCsp::whitelist');
        $resultPage->getConfig()->getTitle()->prepend($id ? __('Edit Host') : __('New Host'));

        return $resultPage;
    }
}
