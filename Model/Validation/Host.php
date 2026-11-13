<?php

/**
 * Copyright (c) 2026 Bruna Sebold
 *
 * @author  Bruna Sebold <sebold.bruna@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace BS\MagentoCsp\Model\Validation;

use BS\MagentoCsp\Exception\ValidationException;

/**
 * Turns a pasted host or URL into the host source stored on the whitelist.
 *
 * A full URL keeps its scheme and host. The path, query and fragment are discarded.
 */
class Host
{
    private const MAX_LENGTH = 255;

    private const DOMAIN = '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/';

    public function normalize(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            throw new ValidationException('Enter a host.');
        }

        if (preg_match('#^https?://#i', $value) === 1) {
            return $this->fromUrl($value);
        }

        return $this->canonicalize(strtolower($value));
    }

    private function fromUrl(string $value): string
    {
        $parts = parse_url($value);
        if (!is_array($parts) || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
            throw new ValidationException(
                'Enter a valid host. The path and the query string of a URL are discarded.'
            );
        }

        $host = strtolower((string) $parts['host']);
        if (isset($parts['port'])) {
            $host .= ':' . $parts['port'];
        }

        return strtolower((string) $parts['scheme']) . '://' . $this->canonicalize($host);
    }

    private function canonicalize(string $host): string
    {
        if (strlen($host) > self::MAX_LENGTH) {
            throw new ValidationException($this->exampleMessage());
        }

        [$name, $port] = $this->splitPort($host);
        $name = $this->canonicalName($name);
        if ($name === null) {
            throw new ValidationException($this->exampleMessage());
        }

        return $port === null ? $name : $name . ':' . $port;
    }

    /**
     * @return array{0: string, 1: int|null}
     */
    private function splitPort(string $host): array
    {
        $colon = strrpos($host, ':');
        if ($colon === false) {
            return [$host, null];
        }

        $name = substr($host, 0, $colon);
        $rawPort = substr($host, $colon + 1);
        if ($name === '' || preg_match('/^\d{1,5}$/', $rawPort) !== 1) {
            throw new ValidationException($this->exampleMessage());
        }

        $port = (int) $rawPort;
        if ($port < 1 || $port > 65535) {
            throw new ValidationException($this->exampleMessage());
        }

        return [$name, $port];
    }

    private function canonicalName(string $name): ?string
    {
        if (str_starts_with($name, '*.')) {
            $rest = substr($name, 2);
            if ($rest === '' || $this->isLocalOrIp($rest) || preg_match(self::DOMAIN, $rest) !== 1) {
                return null;
            }

            return $name;
        }

        if ($name === 'localhost') {
            return $name;
        }

        if ($this->isIpv4($name)) {
            $octets = [];
            foreach (explode('.', $name) as $octet) {
                $value = (int) $octet;
                if ($value > 255) {
                    return null;
                }
                $octets[] = (string) $value;
            }

            return implode('.', $octets);
        }

        if (preg_match(self::DOMAIN, $name) === 1) {
            return $name;
        }

        return null;
    }

    private function isLocalOrIp(string $name): bool
    {
        return $name === 'localhost' || $this->isIpv4($name);
    }

    private function isIpv4(string $name): bool
    {
        return preg_match('/^(?:\d{1,3}\.){3}\d{1,3}$/', $name) === 1;
    }

    private function exampleMessage(): string
    {
        return 'Use a host such as *.example.com, cdn.example.com or https://cdn.example.com.';
    }
}
