<?php

declare(strict_types=1);

namespace PHPCloudSentry\Http;

final class RequestContext
{
    /**
     * @param list<string> $requiredScopes
     */
    public function __construct(
        public readonly string $token,
        public readonly string $resource,
        public readonly string $method,
        public readonly string $tenant,
        public readonly array $requiredScopes,
        public readonly string $ipAddress,
        public readonly string $region,
    ) {
    }
}
