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

$eventLog = dirname(__DIR__) . '/var/events.log';
if (!is_dir(dirname($eventLog))) {
    mkdir(dirname($eventLog), 0775, true);
}

$engine = new ControlEngine(
    new TokenService($_ENV['PCTS_SECRET'] ?? 'change-me-in-prod'),
    new AccessValidator(),
    new TenantBoundaryValidator(),
    new BehaviorMonitor(),
    new EventStream($eventLog),
);

$rawBody = (string) file_get_contents('php://input');
$input = json_decode($rawBody, true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    header('Content-Type: application/json');
    echo json_encode([
        'allow' => false,
        'reason' => 'method_not_allowed',
    ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

    return;
}

if ($rawBody !== '' && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode([
        'allow' => false,
        'reason' => 'invalid_json',
    ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

    return;
}

$input = is_array($input) ? $input : [];

$context = new RequestContext(
    token: (string) ($input['token'] ?? ''),
    resource: (string) ($input['resource'] ?? '/'),
    method: (string) ($input['method'] ?? 'GET'),
    tenant: (string) ($input['tenant'] ?? 'unknown'),
    requiredScopes: array_values($input['required_scopes'] ?? []),
    ipAddress: (string) ($input['ip'] ?? '0.0.0.0'),
    region: (string) ($input['region'] ?? 'unknown'),
);

header('Content-Type: application/json');
echo json_encode($engine->authorize($context), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
