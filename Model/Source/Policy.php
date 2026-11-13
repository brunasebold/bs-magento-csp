<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Model\Source;

use BS\MagentoCsp\Model\Directive;
use Magento\Framework\Data\OptionSourceInterface;

class Policy implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $options = [];
        foreach (Directive::FETCH as $policy) {
            $options[] = ['value' => $policy, 'label' => $policy];
        }

        return $options;
    }
}
