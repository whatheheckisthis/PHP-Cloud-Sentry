<?php

declare(strict_types=1);

use PHPCloudSentry\ControlEngine;
use PHPCloudSentry\Http\RequestContext;
use PHPCloudSentry\Security\AccessValidator;
use PHPCloudSentry\Security\TenantBoundaryValidator;
use PHPCloudSentry\Security\TokenService;
use PHPCloudSentry\Telemetry\EventStream;
use PHPCloudSentry\UEBA\BehaviorMonitor;

require_once dirname(__DIR__) . '/src/bootstrap.php';

$logFile = dirname(__DIR__) . '/var/test-events.log';
if (!is_dir(dirname($logFile))) {
    mkdir(dirname($logFile), 0775, true);
}

@unlink($logFile);

$tokenService = new TokenService('test-secret');
$engine = new ControlEngine(
    $tokenService,
    new AccessValidator(),
    new TenantBoundaryValidator(),
    new BehaviorMonitor(),
    new EventStream($logFile),
);

$token = $tokenService->issueToken('analyst-1', 'tenant-1', ['read:reports'], 600, 'AU');

$cases = [
    'allows_valid_scope' => [
        'ctx' => new RequestContext($token, '/reports', 'GET', 'tenant-1', ['read:reports'], '10.1.1.1', 'AU'),
        'allowed' => true,
    ],
    'blocks_missing_scope' => [
        'ctx' => new RequestContext($token, '/admin', 'POST', 'tenant-1', ['admin:write'], '10.1.1.1', 'AU'),
        'allowed' => false,
    ],
    'blocks_tenant_mismatch' => [
        'ctx' => new RequestContext($token, '/reports', 'GET', 'tenant-2', ['read:reports'], '10.1.1.1', 'AU'),
        'allowed' => false,
    ],
];

foreach ($cases as $name => $case) {
    $result = $engine->authorize($case['ctx']);

    if ($result['allowed'] !== $case['allowed']) {
        fwrite(STDERR, sprintf("Assertion failed for %s\n", $name));
        exit(1);
    }
}

echo "All control-path checks passed.\n";
