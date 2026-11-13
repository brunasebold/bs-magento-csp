<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Model\Source;

use BS\MagentoCsp\Model\Area as AreaCode;
use Magento\Framework\Data\OptionSourceInterface;

class Area implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => AreaCode::GLOBAL_AREA, 'label' => __('All areas')],
            ['value' => AreaCode::STOREFRONT, 'label' => __('Storefront')],
            ['value' => AreaCode::ADMIN, 'label' => __('Admin')],
        ];
    }
}
