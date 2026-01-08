<?php

namespace Kishan\QueryIntel\Collectors;

use Illuminate\Support\Facades\DB;
use Kishan\QueryIntel\Storage\QueryStorage;
use Kishan\QueryIntel\Support\QueryIntelTracker;
use Illuminate\Support\Facades\Log;

class QueryCollector
{
    public function __construct(protected QueryStorage $storage) {}

    public function register(): void
    {
        DB::listen(function ($query) {
            if (!app()->bound('query-intel.request-id')) {
                return;
            }

            // Skip internal tables to prevent loop
            $ignoredTables = ['query_intel_queries', 'query_intel_requests'];
            foreach ($ignoredTables as $table) {
                if (str_contains(strtolower($query->sql), $table)) {
                    return;
                }
            }

            $requestId = app('query-intel.request-id');
            $normalizedSql = strtolower(trim($query->sql));
            $type = $this->detectType($normalizedSql);

            // Increment counters in singleton tracker
            $tracker = app(QueryIntelTracker::class);
            $tracker->count++;
            $tracker->time += $query->time;
            if ($type === 'select') $tracker->hasRead = true;
            elseif (in_array($type, ['insert','update','delete','truncate'], true)) $tracker->hasWrite = true;

            Log::info("query executed",[
                'request_id' => $requestId,
                'type' => $type,
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
                'connection' => $query->connectionName,
            ]);

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
