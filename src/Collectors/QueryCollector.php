<?php

namespace Kishan\QueryIntel\Collectors;

use Illuminate\Support\Facades\DB;
use Kishan\QueryIntel\Storage\QueryStorage;

class QueryCollector
{
    public function __construct(protected QueryStorage $storage) {}

    public function register(): void
    {
        DB::listen(function ($query) {
            // Skip logging for our own internal tables
            $ignoredTables = [
                'query_intel_queries',
                'query_intel_requests',
            ];

            foreach ($ignoredTables as $table) {
                if (str_contains(strtolower($query->sql), $table)) {
                    return; // skip this query
                }
            }

            if (!app()->bound('query-intel.request-id')) {
                return;
            }

            $requestId = app('query-intel.request-id');

            $normalizedSql = strtolower(trim($query->sql));
            $type = $this->detectType($normalizedSql);

            // Increment counters
            $queryCount = app()->bound('query-intel.query-count') ? app('query-intel.query-count') : 0;
            app()->instance('query-intel.query-count', $queryCount + 1);

            $queryTime = app()->bound('query-intel.query-time') ? app('query-intel.query-time') : 0.0;
            app()->instance('query-intel.query-time', $queryTime + $query->time);

            // Update read/write flags
            if ($type === 'select') app()->instance('query-intel.has-read', true);
            elseif (in_array($type, ['insert','update','delete','truncate'], true)) app()->instance('query-intel.has-write', true);

            // Store query in DB
            $this->storage->store([
                'request_id' => $requestId,
                'type' => $type,
                'raw_sql' => $query->sql,
                'normalized_sql' => $normalizedSql,
                'bindings' => json_encode($query->bindings),
                'execution_time' => $query->time,
                'connection' => $query->connectionName,
                'location' => $this->getCaller(),
            ]);
        });

    }

    protected function detectType(string $sql): string
    {
        return match (true) {
            str_starts_with($sql, 'select') => 'select',
            str_starts_with($sql, 'insert') => 'insert',
            str_starts_with($sql, 'update') => 'update',
            str_starts_with($sql, 'delete') => 'delete',
            str_starts_with($sql, 'truncate') => 'truncate',
            str_starts_with($sql, 'begin'),
            str_starts_with($sql, 'commit'),
            str_starts_with($sql, 'rollback') => 'transaction',
            default => 'raw',
        };
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
