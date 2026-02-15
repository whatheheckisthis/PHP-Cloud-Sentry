<?php

declare(strict_types=1);

namespace PHPCloudSentry\UEBA;

final class BehaviorMonitor
{
    /**
     * @var array<string,array{regions:array<string,bool>,ips:array<string,bool>,requests:int}>
     */
    private array $profiles = [];

    /**
     * @return array{score:int,reasons:list<string>}
     */
    public function evaluate(string $tenant, string $subject, string $ipAddress, string $region): array
    {
        $key = sprintf('%s:%s', $tenant, $subject);
        $profile = $this->profiles[$key] ?? ['regions' => [], 'ips' => [], 'requests' => 0];

        $score = 0;
        $reasons = [];

        if ($profile['requests'] > 0 && !isset($profile['regions'][$region])) {
            $score += 40;
            $reasons[] = 'new_region';
        }

        if ($profile['requests'] > 0 && !isset($profile['ips'][$ipAddress])) {
            $score += 30;
            $reasons[] = 'new_ip';
        }

        $profile['regions'][$region] = true;
        $profile['ips'][$ipAddress] = true;
        $profile['requests']++;
        $this->profiles[$key] = $profile;

        return ['score' => $score, 'reasons' => $reasons];
    }
}
