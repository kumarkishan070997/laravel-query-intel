<?php

namespace Kishan\QueryIntel\Models;

use Illuminate\Database\Eloquent\Model;

class QueryIntelRequest extends Model
{
    protected $table = 'query_intel_requests';

    protected $fillable = [
        'request_id',
        'type',
        'method',
        'uri',
        'controller',
        'total_queries',
        'total_query_time',
        'memory_usage',
        'peak_memory',
    ];
}
