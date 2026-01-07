<?php

namespace Kishan\QueryIntel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Kishan\QueryIntel\Models\QueryIntelRequest;

class QueryIntelMiddleware
{
    protected float $startTime;
    protected int $startMemory;
    protected string $requestId;

    public function handle(Request $request, Closure $next)
    {
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage(true);
        $this->requestId = (string)Str::uuid();

        // Initialize request-level tracking
        app()->instance('query-intel.request-id', $this->requestId);
        app()->instance('query-intel.query-count', 0);
        app()->instance('query-intel.query-time', 0.0);
        app()->instance('query-intel.has-read', false);
        app()->instance('query-intel.has-write', false);

        // Insert initial request record
        QueryIntelRequest::create([
            'request_id' => $this->requestId,
            'type' => 'none',
            'method' => $request->method(),
            'uri' => '/' . ltrim($request->path(), '/'),
            'controller' => optional($request->route())->getActionName(),
            'total_queries' => 0,
            'total_query_time' => 0,
            'memory_usage' => 0,
            'peak_memory' => 0,
        ]);

        return $next($request);
    }

    public function terminate(Request $request, $response): void
    {
        if (!isset($this->requestId, $this->startTime)) {
            return;
        }

        $durationMs = (microtime(true) - $this->startTime) * 1000;

        // Determine request type based on flags
        $hasRead = app('query-intel.has-read', false);
        $hasWrite = app('query-intel.has-write', false);

        $requestType = $hasRead && $hasWrite
            ?'mixed'
            : ($hasWrite
                ?'write'
                : ($hasRead
                    ?'read'
                    : 'none'));


        $totalQueries = app()->bound('query-intel.query-count') ?app('query-intel.query-count') : 0;
        $totalQueryTime = app()->bound('query-intel.query-time') ?app('query-intel.query-time') : 0.0;

        QueryIntelRequest::where('request_id', $this->requestId)->update([
            'type' => $requestType,
            'total_queries' => $totalQueries,
            'total_query_time' => round($totalQueryTime, 2),
            'memory_usage' => memory_get_usage(true) - $this->startMemory,
            'peak_memory' => memory_get_peak_usage(true),
        ]);
    }
}
