<?php

declare(strict_types=1);

namespace PHPCloudSentry;

use PHPCloudSentry\Http\RequestContext;
use PHPCloudSentry\Security\AccessValidator;
use PHPCloudSentry\Security\TenantBoundaryValidator;
use PHPCloudSentry\Security\TokenService;
use PHPCloudSentry\Telemetry\EventStream;
use PHPCloudSentry\UEBA\BehaviorMonitor;
use RuntimeException;

final class ControlEngine
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly AccessValidator $accessValidator,
        private readonly TenantBoundaryValidator $tenantValidator,
        private readonly BehaviorMonitor $behaviorMonitor,
        private readonly EventStream $eventStream,
    ) {
    }

    /**
     * @return array{allowed:bool,risk_score:int,reasons:list<string>,subject:?string,tenant:string}
     */
    public function authorize(RequestContext $request): array
    {
        try {
            $claims = $this->tokenService->verifyToken($request->token, $request->region);
            $this->tenantValidator->assertTenant($request->tenant, (string) $claims['tenant']);
            $this->accessValidator->assertScopes($claims['scopes'], $request->requiredScopes);

            $risk = $this->behaviorMonitor->evaluate(
                (string) $claims['tenant'],
                (string) $claims['sub'],
                $request->ipAddress,
                $request->region,
            );

            $this->eventStream->emit([
                'event' => 'access_granted',
                'tenant' => $request->tenant,
                'subject' => (string) $claims['sub'],
                'resource' => $request->resource,
                'method' => $request->method,
                'risk_score' => $risk['score'],
                'risk_reasons' => $risk['reasons'],
            ]);

            return [
                'allowed' => true,
                'risk_score' => $risk['score'],
                'reasons' => $risk['reasons'],
                'subject' => (string) $claims['sub'],
                'tenant' => $request->tenant,
            ];
        } catch (RuntimeException $exception) {
            $this->eventStream->emit([
                'event' => 'access_denied',
                'tenant' => $request->tenant,
                'resource' => $request->resource,
                'method' => $request->method,
                'error' => $exception->getMessage(),
            ]);

            return [
                'allowed' => false,
                'risk_score' => 100,
                'reasons' => [$exception->getMessage()],
                'subject' => null,
                'tenant' => $request->tenant,
            ];
        }
    }
}
