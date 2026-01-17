<?php

namespace Kishan\QueryIntel\Http\Controllers;

use Illuminate\Routing\Controller;
use Kishan\QueryIntel\Models\QueryIntelRequest;
use Kishan\QueryIntel\Models\QueryIntelQuery;

class DashboardController extends Controller
{
    public function index()
    {
        return view('query-intel::dashboard.index', [
            'requests' => QueryIntelRequest::latest()->paginate(20),
        ]);
    }

    public function show(string $requestId)
    {
        return view('query-intel::dashboard.show', [
            'request' => QueryIntelRequest::where('request_id', $requestId)->firstOrFail(),
            'queries' => QueryIntelQuery::where('request_id', $requestId)->get(),
        ]);
    }
}
