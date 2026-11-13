<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Enabled implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 1, 'label' => __('Yes')],
            ['value' => 0, 'label' => __('No')],
        ];
    }
}
