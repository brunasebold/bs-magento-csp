<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Model\Whitelist;

use BS\MagentoCsp\Model\ResourceModel\Whitelist\CollectionFactory;
use BS\MagentoCsp\Model\Whitelist;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    private ?array $loadedData = null;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        private readonly DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }

        $this->loadedData = [];
        foreach ($this->getCollection()->getItems() as $item) {
            $this->loadedData[$item->getId()] = $item->getData();
        }

        $persisted = $this->dataPersistor->get(Whitelist::PERSISTOR_KEY);
        if (is_array($persisted) && $persisted !== []) {
            $item = $this->getCollection()->getNewEmptyItem();
            $item->setData($persisted);
            $this->loadedData[$item->getId()] = $item->getData();
            $this->dataPersistor->clear(Whitelist::PERSISTOR_KEY);
        }

        return $this->loadedData;
    }
}
