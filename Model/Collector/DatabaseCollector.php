<?php

declare(strict_types=1);

namespace BS\MagentoCsp\Model\Collector;

use BS\MagentoCsp\Model\Area;
use BS\MagentoCsp\Model\Directive;
use BS\MagentoCsp\Model\HostProvider;
use Magento\Csp\Api\PolicyCollectorInterface;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Appends database hosts to the policies Magento already collected.
 *
 * Flags stay off on the appended policy. Magento's fetch merger ORs them, so an
 * existing self, inline or eval flag is kept and 'none' is not removed.
 */
class DatabaseCollector implements PolicyCollectorInterface
{
    public function __construct(
        private readonly HostProvider $hostProvider,
        private readonly State $appState,
        private readonly LoggerInterface $logger
    ) {
    }

    public function collect(array $defaultPolicies = []): array
    {
        try {
            $areaCode = $this->appState->getAreaCode();
        } catch (LocalizedException) {
            $areaCode = Area::GLOBAL_AREA;
        }

        try {
            $grouped = $this->hostProvider->getHostsByDirective($areaCode);
        } catch (\Throwable $exception) {
            $this->logger->error(
                'The CSP whitelist could not be collected. The response will be sent without the database hosts.',
                ['exception' => $exception]
            );

            return $defaultPolicies;
        }

        $policies = $defaultPolicies;
        foreach ($grouped as $directive => $hosts) {
            if (!Directive::isFetch((string) $directive) || $hosts === []) {
                continue;
            }
            $policies[] = new FetchPolicy((string) $directive, false, array_values($hosts));
        }

        return $policies;
    }
}
