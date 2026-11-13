<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Controller\Adminhtml\Whitelist;

use BS\MagentoCsp\Controller\Adminhtml\Whitelist;
use BS\MagentoCsp\Model\ResourceModel\Whitelist as WhitelistResource;
use BS\MagentoCsp\Model\WhitelistFactory;
use Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface;

class Delete extends Whitelist
{
    public function __construct(
        Context $context,
        private readonly WhitelistFactory $whitelistFactory,
        private readonly WhitelistResource $whitelistResource,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int) $this->getRequest()->getParam('whitelist_id');
        if (!$id) {
            return $resultRedirect->setPath('*/*/');
        }

        $model = $this->whitelistFactory->create();
        $this->whitelistResource->load($model, $id);
        if (!$model->getId()) {
            $this->messageManager->addErrorMessage(__('This host does not exist.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->whitelistResource->delete($model);
            $this->messageManager->addSuccessMessage(
                __(
                    'The host was removed. Refresh the full page cache so cached pages send the new policy.'
                )
            );
        } catch (\Throwable $exception) {
            $this->logger->error('The CSP whitelist host could not be removed.', ['exception' => $exception]);
            $this->messageManager->addErrorMessage(__('The host could not be removed.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
