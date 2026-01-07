<?php

namespace Kishan\QueryIntel\Models;

use Illuminate\Database\Eloquent\Model;

class QueryIntelQuery extends Model
{
    protected $table = 'query_intel_queries';

    protected $fillable = [
        'request_id',
        'normalized_sql',
        'raw_sql',
        'bindings',
        'execution_time',
        'connection',
        'location',
    ];

    protected $casts = [
        'bindings' => 'array',
    ];
}
