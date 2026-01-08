<?php

return [
    'enabled' => env('QUERY_INTEL_ENABLED', true),

    'storage' => 'database',

    'slow_query_threshold' => 200,

    'nplus_threshold' => 10,
];
