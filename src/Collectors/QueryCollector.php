<?php

namespace Kishan\QueryIntel\Collectors;

use Illuminate\Support\Facades\DB;
use Kishan\QueryIntel\Models\QueryIntelQuery;
use Kishan\QueryIntel\Support\QueryIntelTracker;

class QueryCollector
{
    public function register(): void
    {
        DB::listen(function ($query) {

            if (! config('query-intel.enabled')) {
                return;
            }

            // ❌ Ignore QueryIntel internal tables
            foreach (config('query-intel.ignore_tables') as $table) {
                if (str_contains(strtolower($query->sql), $table)) {
                    return;
                }
            }

            $tracker = app(QueryIntelTracker::class);

            // Count & time
            $tracker->count++;
            $tracker->time += $query->time;

            // Read / Write detection
            $sql = strtolower(trim($query->sql));

            if (str_starts_with($sql, 'select')) {
                $tracker->hasRead = true;
            } else {
                $tracker->hasWrite = true;
            }

            // Normalize SQL (for N+1 detection)
            $normalized = preg_replace('/\s+/', ' ', $sql);

            $tracker->queryFingerprints[$normalized] =
                ($tracker->queryFingerprints[$normalized] ?? 0) + 1;

            QueryIntelQuery::create([
                'request_id' => app('query-intel.request-id'),
                'raw_sql' => $query->sql,
                'normalized_sql' => $normalized,
                'bindings' => json_encode($query->bindings),
                'execution_time' => $query->time,
                'connection' => $query->connectionName,
                'location' => $this->getCaller(),
                'is_slow' => $query->time >= config('query-intel.slow_query_threshold'),
            ]);
        });
    }

    protected function getCaller(): string
    {
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
            if (isset($trace['file']) && str_contains($trace['file'], '/app/')) {
                return $trace['file'] . ':' . ($trace['line'] ?? 0);
            }
        }

        return 'unknown';
    }
}
