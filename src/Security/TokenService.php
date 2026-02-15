<?php

declare(strict_types=1);

namespace PHPCloudSentry\Security;

use RuntimeException;

final class TokenService
{
    public function __construct(private readonly string $secret)
    {
    }

    /**
     * @param list<string> $scopes
     */
    public function issueToken(string $subject, string $tenant, array $scopes, int $ttlSeconds, string $issuedFromRegion): string
    {
        $now = time();
        $payload = [
            'sub' => $subject,
            'tenant' => $tenant,
            'scopes' => $scopes,
            'iat' => $now,
            'exp' => $now + $ttlSeconds,
            'jti' => bin2hex(random_bytes(10)),
            'region' => $issuedFromRegion,
        ];

        $header = ['alg' => 'HS256', 'typ' => 'PCTS'];

        $encodedHeader = $this->base64UrlEncode((string) json_encode($header, JSON_THROW_ON_ERROR));
        $encodedPayload = $this->base64UrlEncode((string) json_encode($payload, JSON_THROW_ON_ERROR));
        $signature = $this->sign("{$encodedHeader}.{$encodedPayload}");

        return "{$encodedHeader}.{$encodedPayload}.{$signature}";
    }

    /**
     * @return array{sub:string,tenant:string,scopes:list<string>,iat:int,exp:int,jti:string,region:string}
     */
    public function verifyToken(string $token, ?string $observedRegion = null): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new RuntimeException('Malformed token.');
        }

        [$header, $payload, $signature] = $parts;
        $expectedSignature = $this->sign("{$header}.{$payload}");

        if (!hash_equals($expectedSignature, $signature)) {
            throw new RuntimeException('Token signature mismatch.');
        }

        $decodedPayload = json_decode($this->base64UrlDecode($payload), true, flags: JSON_THROW_ON_ERROR);

        if (!is_array($decodedPayload) || ($decodedPayload['exp'] ?? 0) < time()) {
            throw new RuntimeException('Token is expired.');
        }

        if ($observedRegion !== null && isset($decodedPayload['region']) && $decodedPayload['region'] !== $observedRegion) {
            throw new RuntimeException('Token region mismatch.');
        }

        return $decodedPayload;
    }

    private function sign(string $data): string
    {
        return $this->base64UrlEncode(hash_hmac('sha256', $data, $this->secret, true));
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        $padding = 4 - (strlen($data) % 4);
        if ($padding < 4) {
            $data .= str_repeat('=', $padding);
        }

        return (string) base64_decode(strtr($data, '-_', '+/'));
    }
}
