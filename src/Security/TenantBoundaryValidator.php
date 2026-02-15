<?php

declare(strict_types=1);

namespace PHPCloudSentry\Security;

use RuntimeException;

final class TenantBoundaryValidator
{
    public function assertTenant(string $declaredTenant, string $tokenTenant): void
    {
        if ($declaredTenant !== $tokenTenant) {
            throw new RuntimeException('Tenant boundary validation failed.');
        }
    }
}
