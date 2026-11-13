<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['whitelist_id'])) {
                continue;
            }
            $name = (string) $this->getData('name');
            $item[$name] = [
                'edit' => [
                    'href' => $this->urlBuilder->getUrl(
                        'bs_csp/whitelist/edit',
                        ['whitelist_id' => $item['whitelist_id']]
                    ),
                    'label' => __('Edit'),
                ],
                'delete' => [
                    'href' => $this->urlBuilder->getUrl(
                        'bs_csp/whitelist/delete',
                        ['whitelist_id' => $item['whitelist_id']]
                    ),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete host'),
                        'message' => __('Remove this host from the policy?'),
                    ],
                ],
            ];
        }

        return $dataSource;
    }
}
