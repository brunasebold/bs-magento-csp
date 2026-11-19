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
use BS\MagentoCsp\Exception\ValidationException;
use BS\MagentoCsp\Model\Form\Payload;
use BS\MagentoCsp\Model\ResourceModel\Whitelist as WhitelistResource;
use BS\MagentoCsp\Model\Validation\WhitelistEntry;
use BS\MagentoCsp\Model\Whitelist as WhitelistModel;
use BS\MagentoCsp\Model\WhitelistFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Save extends Whitelist implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private readonly WhitelistFactory $whitelistFactory,
        private readonly WhitelistResource $whitelistResource,
        private readonly WhitelistEntry $entry,
        private readonly Payload $payload,
        private readonly DataPersistorInterface $dataPersistor,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $posted = $this->getRequest()->getPostValue();
        if (!is_array($posted) || $posted === []) {
            return $resultRedirect->setPath('*/*/');
        }

        $data = $this->payload->extract($posted);
        $id = (int) ($data['whitelist_id'] ?? 0);
        $model = $this->whitelistFactory->create();
        if ($id) {
            $this->whitelistResource->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This host does not exist.'));

                return $resultRedirect->setPath('*/*/');
            }
        }

        try {
            $note = $data['note'] ?? null;
            $normalized = $this->entry->normalize(
                (string) ($data['policy_id'] ?? ''),
                (string) ($data['host'] ?? ''),
                (string) ($data['area'] ?? ''),
                $data['is_active'] ?? 0,
                $note === null ? null : (string) $note
            );
            $data = array_merge($data, $normalized);
            $this->assertUnique($normalized['policy_id'], $normalized['host'], $normalized['area'], $id);
            $model->addData($normalized);
            $this->whitelistResource->save($model);
            $this->dataPersistor->clear(WhitelistModel::PERSISTOR_KEY);
            $this->messageManager->addSuccessMessage(
                __(
                    'The host was saved. Refresh the full page cache so cached pages send the new policy.'
                )
            );

            return $resultRedirect->setPath('*/*/');
        } catch (ValidationException $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Throwable $exception) {
            $this->logger->error('The CSP whitelist host could not be saved.', ['exception' => $exception]);
            $this->messageManager->addErrorMessage(__('The host could not be saved.'));
        }

        $this->dataPersistor->set(WhitelistModel::PERSISTOR_KEY, $data);

        return $resultRedirect->setPath('*/*/edit', $id ? ['whitelist_id' => $id] : []);
    }

    /**
     * @throws LocalizedException
     */
    private function assertUnique(string $policyId, string $host, string $area, int $id): void
    {
        $connection = $this->whitelistResource->getConnection();
        $select = $connection->select()
            ->from($this->whitelistResource->getMainTable(), 'whitelist_id')
            ->where('policy_id = ?', $policyId)
            ->where('host = ?', $host)
            ->where('area = ?', $area);
        if ($id) {
            $select->where('whitelist_id <> ?', $id);
        }
        if ($connection->fetchOne($select)) {
            throw new LocalizedException(__('This host is already registered for that directive and area.'));
        }
    }
}
