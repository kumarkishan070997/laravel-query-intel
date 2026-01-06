<?php

namespace Kishan\QueryIntel\Collectors;

use Illuminate\Support\Facades\DB;
use Kishan\QueryIntel\Storage\QueryStorage;

class QueryCollector
{
    public function __construct(
        protected QueryStorage $storage
    ) {}

    public function register(): void
    {
        DB::listen(function ($query) {
            $this->storage->store([
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
                'connection' => $query->connectionName,
                'location' => $this->getCaller(),
                'timestamp' => now()->toDateTimeString(),
            ]);
        });
    }

    protected function getCaller(): string
    {
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
            if (isset($trace['file']) && str_contains($trace['file'], '/app/')) {
                return $trace['file'] . ':' . $trace['line'];
            }
        }

        return 'unknown';
    }
}
