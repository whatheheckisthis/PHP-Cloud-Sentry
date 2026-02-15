# PHP-Cloud-Sentry

Deterministic, multi-tenant security controls for PHP workloads.

## Current Direction

PHP-Cloud-Sentry is currently focused on being a **practical reference implementation** of core control paths:

1. **Deterministic token verification** (HMAC signing + TTL + region validation)
2. **Explicit authorization and tenant boundary enforcement**
3. **Lightweight behavior/risk scoring (UEBA-style signals)**
4. **Append-only JSON event evidence for auditability**

Instead of framework-theory-only documentation, the project now prioritizes runnable PHP code that teams can execute, adapt, and extend.

## What Exists Today

- `TokenService`: issues and verifies signed tokens with expiration and region checks.
- `AccessValidator`: checks required scopes against token scopes.
- `TenantBoundaryValidator`: blocks cross-tenant access attempts.
- `BehaviorMonitor`: tracks subject baseline by tenant and raises risk on new IP/region activity.
- `EventStream`: writes structured JSON events to append-only log files.
- `ControlEngine`: orchestrates control decisions and evidence logging.
- `public/index.php`: HTTP entry point for authorization decisions.
- `bin/demo.php`: scenario walkthrough for common attack/abuse paths.
- `tests/run.php`: lightweight end-to-end control-path checks.

## Quick Start

### 1) Run control-path checks

```bash
php tests/run.php
```

### 2) Run the demo scenarios

```bash
php bin/demo.php
```

### 3) Run the HTTP endpoint

```bash
php -S 127.0.0.1:8080 -t public
```

Then POST JSON to `http://127.0.0.1:8080`:

```json
{
  "token": "<issued_token>",
  "resource": "/reports",
  "method": "GET",
  "tenant": "tenant-a",
  "required_scopes": ["read:reports"],
  "ip": "10.0.0.1",
  "region": "AU"
}
```

## Example Directional Use Cases

- Block privilege escalation attempts when required scopes are missing.
- Prevent lateral movement by rejecting tenant mismatch.
- Flag suspicious behavior with incremental risk scores.
- Generate machine-readable evidence for downstream SIEM/audit pipelines.

## Near-Term Priorities

- Stabilize module interfaces and error semantics.
- Add replay protection and stronger token claim hardening.
- Expand risk heuristics and optional persistent profile storage.
- Add richer policy input (resource/method/condition-aware rules).
- Improve deployment guidance for production-grade secret handling and log routing.

## Positioning

This repository is best treated as a **security control reference and integration starter** for multi-tenant PHP services, not a drop-in full IAM platform.
