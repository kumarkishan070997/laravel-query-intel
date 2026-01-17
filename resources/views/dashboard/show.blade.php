@extends('query-intel::layout.app')

@section('content')

<h3>Request Details</h3>

<p><strong>URI:</strong> {{ $request->uri }}</p>
<p><strong>Method:</strong> {{ $request->method }}</p>
<p><strong>Type:</strong> {{ strtoupper($request->type) }}</p>
<p><strong>Total Queries:</strong> {{ $request->total_queries }}</p>
<p><strong>Total Query Time:</strong> {{ $request->total_query_time }} ms</p>
<p><strong>Peak Memory:</strong> {{ number_format($request->peak_memory / 1024 / 1024, 2) }} MB</p>

<hr>

<h4>Queries</h4>

@foreach ($queries as $query)
    <div class="p-2 mb-2" style="background:#fff; border-left:4px solid {{ $query->is_slow ? '#dc2626' : '#16a34a' }}">
        <pre style="white-space:pre-wrap;">{{ $query->raw_sql }}</pre>
        <small>
            ⏱ {{ $query->execution_time }} ms
            |
            {{ $query->is_slow ? 'Slow Query' : 'Fast Query' }}
        </small>
    </div>
@endforeach

<div class="mt-3 d-flex justify-content-center">
    {{ $queries->links() }}
</div>

@endsection
