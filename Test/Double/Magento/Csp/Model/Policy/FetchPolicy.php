<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Magento\Csp\Model\Policy;

/**
 * Stand-in for Magento\Csp\Model\Policy\FetchPolicy on the 2.4 line.
 *
 * The second argument defaults to true, as in Magento. Omitting it allows 'none'.
 */
class FetchPolicy
{
    private string $id;

    private bool $noneAllowed;

    /** @var list<string> */
    private array $hostSources;

    /** @var list<string> */
    private array $schemeSources;

    private bool $selfAllowed;

    private bool $inlineAllowed;

    private bool $evalAllowed;

    /**
     * @param list<string> $hostSources
     * @param list<string> $schemeSources
     */
    public function __construct(
        string $id,
        bool $noneAllowed = true,
        array $hostSources = [],
        array $schemeSources = [],
        bool $selfAllowed = false,
        bool $inlineAllowed = false,
        bool $evalAllowed = false
    ) {
        $this->id = $id;
        $this->noneAllowed = $noneAllowed;
        $this->hostSources = array_values(array_unique($hostSources));
        $this->schemeSources = array_values(array_unique($schemeSources));
        $this->selfAllowed = $selfAllowed;
        $this->inlineAllowed = $inlineAllowed;
        $this->evalAllowed = $evalAllowed;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return list<string>
     */
    public function getHostSources(): array
    {
        return $this->hostSources;
    }

    /**
     * @return list<string>
     */
    public function getSchemeSources(): array
    {
        return $this->schemeSources;
    }

    public function isSelfAllowed(): bool
    {
        return $this->selfAllowed;
    }

    public function isInlineAllowed(): bool
    {
        return $this->inlineAllowed;
    }

    public function isEvalAllowed(): bool
    {
        return $this->evalAllowed;
    }

    public function isNoneAllowed(): bool
    {
        return $this->noneAllowed;
    }
}
