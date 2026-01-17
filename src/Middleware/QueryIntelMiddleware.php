<?php

namespace Kishan\QueryIntel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Kishan\QueryIntel\Models\QueryIntelRequest;
use Kishan\QueryIntel\Support\QueryIntelTracker;

class QueryIntelMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! config('query-intel.enabled')) {
            return $next($request);
        }

        $requestId = (string) Str::uuid();
        $startMemory = memory_get_usage(true);

        // Store per-request values safely
        $request->attributes->set('query_intel.request_id', $requestId);
        $request->attributes->set('query_intel.start_memory', $startMemory);

        app()->instance('query-intel.request-id', $requestId);

        // Reset tracker (Octane-safe)
        app(QueryIntelTracker::class)->reset();

        QueryIntelRequest::create([
            'request_id' => $requestId,
            'method' => $request->method(),
            'uri' => $request->path(),
            'controller' => optional($request->route())->getActionName(),
            'type' => 'none',
            'total_queries' => 0,
            'total_query_time' => 0,
            'memory_usage' => 0,
            'peak_memory' => 0,
        ]);

        return $next($request);
    }

    public function terminate(Request $request, $response): void
    {
        if (! config('query-intel.enabled')) {
            return;
        }

        $tracker = app(QueryIntelTracker::class);

        $requestId = $request->attributes->get('query_intel.request_id');
        $startMemory = $request->attributes->get('query_intel.start_memory');

        if (! $requestId) {
            return;
        }

        $type =
            $tracker->hasRead && $tracker->hasWrite ? 'mixed'
            : ($tracker->hasWrite ? 'write'
            : ($tracker->hasRead ? 'read' : 'none'));

        QueryIntelRequest::where('request_id', $requestId)->update([
            'type' => $type,
            'total_queries' => $tracker->count,
            'total_query_time' => round($tracker->time, 2),
            'memory_usage' => memory_get_usage(true) - $startMemory,
            'peak_memory' => memory_get_peak_usage(true),
        ]);
    }
}
