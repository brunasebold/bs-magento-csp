<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Exception;

/**
 * A host or whitelist entry the admin cannot store.
 *
 * The message is an English phrase translated at the Magento boundary.
 */
class ValidationException extends \InvalidArgumentException
{
}
