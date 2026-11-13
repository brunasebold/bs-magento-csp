<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Model\ResourceModel;

use BS\MagentoCsp\Exception\ValidationException;
use BS\MagentoCsp\Model\HostProvider;
use BS\MagentoCsp\Model\Validation\WhitelistEntry;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\PageCache\Model\Cache\Type as FullPageCache;

class Whitelist extends AbstractDb
{
    public function __construct(
        Context $context,
        private readonly WhitelistEntry $entry,
        private readonly CacheInterface $cache,
        private readonly TypeListInterface $cacheTypeList,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    protected function _construct(): void
    {
        $this->_init('bs_csp_whitelist', 'whitelist_id');
    }

    /**
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $note = $object->getData('note');
        try {
            $normalized = $this->entry->normalize(
                (string) $object->getData('policy_id'),
                (string) $object->getData('host'),
                (string) $object->getData('area'),
                $object->getData('is_active'),
                $note === null ? null : (string) $note
            );
        } catch (ValidationException $exception) {
            throw new LocalizedException(__($exception->getMessage()));
        }

        $object->addData($normalized);

        return parent::_beforeSave($object);
    }

    protected function _afterSave(AbstractModel $object)
    {
        $this->invalidateCache();

        return parent::_afterSave($object);
    }

    protected function _afterDelete(AbstractModel $object)
    {
        $this->invalidateCache();

        return parent::_afterDelete($object);
    }

    private function invalidateCache(): void
    {
        $this->cache->clean([HostProvider::CACHE_TAG]);
        $this->cacheTypeList->invalidate(FullPageCache::TYPE_IDENTIFIER);
    }
}
