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
use BS\MagentoCsp\Model\ResourceModel\Whitelist\Grid\CollectionFactory;
use BS\MagentoCsp\Model\WhitelistFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class MassDelete extends Whitelist
{
    public function __construct(
        Context $context,
        private readonly Filter $filter,
        private readonly CollectionFactory $collectionFactory,
        private readonly WhitelistFactory $whitelistFactory,
        private readonly WhitelistResource $whitelistResource,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create()->setPath('*/*/');
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $deleted = 0;
            foreach ($collection->getAllIds() as $id) {
                $model = $this->whitelistFactory->create();
                $this->whitelistResource->load($model, (int) $id);
                if (!$model->getId()) {
                    continue;
                }
                $this->whitelistResource->delete($model);
                $deleted++;
            }
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());

            return $redirect;
        } catch (\Throwable $exception) {
            $this->logger->error('The CSP whitelist hosts could not be removed.', ['exception' => $exception]);
            $this->messageManager->addErrorMessage(__('The host could not be removed.'));

            return $redirect;
        }

        if ($deleted) {
            $this->messageManager->addSuccessMessage(
                __(
                    '%1 hosts were removed. Refresh the full page cache so cached pages send the new policy.',
                    $deleted
                )
            );
        }

        return $redirect;
    }
}
