<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Block\Adminhtml\Whitelist\Edit;

use Magento\Backend\Block\Widget\Context;

class GenericButton
{
    public function __construct(private readonly Context $context)
    {
    }

    public function getWhitelistId(): ?int
    {
        $id = $this->context->getRequest()->getParam('whitelist_id');

        return $id ? (int) $id : null;
    }

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
