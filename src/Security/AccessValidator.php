<?php

declare(strict_types=1);

namespace PHPCloudSentry\Security;

use RuntimeException;

final class AccessValidator
{
    /**
     * @param list<string> $tokenScopes
     * @param list<string> $requiredScopes
     */
    public function assertScopes(array $tokenScopes, array $requiredScopes): void
    {
        $missing = array_values(array_diff($requiredScopes, $tokenScopes));

        if ($missing !== []) {
            throw new RuntimeException(sprintf('Scope validation failed. Missing: %s', implode(',', $missing)));
        }
    }
}
