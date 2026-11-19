<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Magento\Framework\Exception;

/**
 * Stand-in for Magento\Framework\Exception\LocalizedException when Magento is not installed.
 */
class LocalizedException extends \Exception
{
}
