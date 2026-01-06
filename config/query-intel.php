<?php

return [
    'enabled' => env('QUERY_INTEL_ENABLED', true),

    'slow_query_threshold' => 200,

    'nplus_threshold' => 10,

    'storage' => 'file',
];
