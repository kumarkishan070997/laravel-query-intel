@extends('query-intel::layout.app')

@section('content')

<h3>Requests</h3>

<table class="queryintel-table table table-striped">
    <thead>
        <tr>
            <th>Method</th>
            <th>URI</th>
            <th>Type</th>
            <th>Total Queries</th>
            <th>Total Time (ms)</th>
            <th>Peak Memory (MB)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($requests as $request)
            <tr>
                <td>{{ $request->method }}</td>
                <td>
                    <a href="{{ route('query-intel.request.show', $request->request_id) }}">
                        {{ $request->uri }}
                    </a>
                </td>
                <td>{{ strtoupper($request->type) }}</td>
                <td>{{ $request->total_queries }}</td>
                <td>{{ $request->total_query_time }}</td>
                <td>{{ number_format($request->peak_memory / 1024 / 1024, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6">No requests captured yet.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3 d-flex justify-content-center">
    {{ $requests->links() }}
</div>

@endsection
