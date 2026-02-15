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

$logFile = dirname(__DIR__) . '/var/demo-events.log';
if (!is_dir(dirname($logFile))) {
    mkdir(dirname($logFile), 0775, true);
}

$tokenService = new TokenService('demo-secret');
$engine = new ControlEngine(
    $tokenService,
    new AccessValidator(),
    new TenantBoundaryValidator(),
    new BehaviorMonitor(),
    new EventStream($logFile),
);

$token = $tokenService->issueToken('alice', 'tenant-a', ['read:reports'], 3600, 'AU');

$scenarios = [
    'baseline_access' => new RequestContext($token, '/reports', 'GET', 'tenant-a', ['read:reports'], '10.0.0.1', 'AU'),
    'privilege_escalation_attempt' => new RequestContext($token, '/admin', 'POST', 'tenant-a', ['admin:write'], '10.0.0.1', 'AU'),
    'lateral_tenant_movement' => new RequestContext($token, '/reports', 'GET', 'tenant-b', ['read:reports'], '10.0.0.1', 'AU'),
    'anomalous_region' => new RequestContext($token, '/reports', 'GET', 'tenant-a', ['read:reports'], '10.5.0.2', 'US'),
];

foreach ($scenarios as $name => $scenario) {
    $result = $engine->authorize($scenario);
    echo sprintf("[%s] %s\n", $name, json_encode($result, JSON_THROW_ON_ERROR));
}
