<?php

namespace Kishan\QueryIntel\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Kishan\QueryIntel\Models\QueryIntelRequest;
use Kishan\QueryIntel\Support\QueryIntelTracker;
use Illuminate\Support\Facades\Log;

class QueryIntelMiddleware
{
    protected float $startTime;
    protected int $startMemory;
    protected string $requestId;

    public function handle(Request $request, Closure $next)
    {
        $requestId = (string)\Illuminate\Support\Str::uuid();
        $startMemory = memory_get_usage(true);

        // Store on request (Laravel 11 safe)
        $request->attributes->set('query_intel.request_id', $requestId);
        $request->attributes->set('query_intel.start_memory', $startMemory);

        // Also expose request ID for QueryCollector
        app()->instance('query-intel.request-id', $requestId);

        QueryIntelRequest::create([
            'request_id' => $requestId,
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
        $tracker = app(QueryIntelTracker::class);

        // Retrieve from request attributes
        $requestId = $request->attributes->get('query_intel.request_id');
        $startMemory = $request->attributes->get('query_intel.start_memory');

        if (!$requestId || !$startMemory) {
            return; // safety guard
        }

        $requestType =
            $tracker->hasRead && $tracker->hasWrite ?'mixed'
            : ($tracker->hasWrite ?'write'
                : ($tracker->hasRead ?'read' : 'none'));

        QueryIntelRequest::where('request_id', $requestId)->update([
            'type' => $requestType,
            'total_queries' => $tracker->count,
            'total_query_time' => round($tracker->time, 2),
            'memory_usage' => memory_get_usage(true) - $startMemory,
            'peak_memory' => memory_get_peak_usage(true),
        ]);
    }
}
