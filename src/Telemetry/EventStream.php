<?php

declare(strict_types=1);

namespace PHPCloudSentry\Telemetry;

final class EventStream
{
    /** @var list<array<string,mixed>> */
    private array $events = [];

    public function __construct(private readonly string $logFile)
    {
    }

    /**
     * @param array<string,mixed> $event
     */
    public function emit(array $event): void
    {
        $event['timestamp'] = gmdate('c');
        $this->events[] = $event;

        $line = json_encode($event, JSON_THROW_ON_ERROR) . PHP_EOL;
        file_put_contents($this->logFile, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * @return list<array<string,mixed>>
     */
    public function all(): array
    {
        return $this->events;
    }
}
