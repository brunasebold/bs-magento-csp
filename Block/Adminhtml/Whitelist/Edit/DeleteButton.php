<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Block\Adminhtml\Whitelist\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        $id = $this->getWhitelistId();
        if (!$id) {
            return [];
        }

        $confirm = addcslashes((string) __('Remove this host from the policy?'), "'\\");
        $url = addcslashes($this->getUrl('*/*/delete', ['whitelist_id' => $id]), "'\\");

        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'on_click' => "deleteConfirm('" . $confirm . "', '" . $url . "', {\"data\": {}})",
            'sort_order' => 20,
        ];
    }
}
