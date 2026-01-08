<?php

namespace Kishan\QueryIntel\Analyzers;

class QueryGrouper
{
    public function group(array $queries): array
    {
        $groups = [];

        foreach ($queries as $query) {
            $key = $query['normalized_sql'];

            $groups[$key]['sql'] = $key;
            $groups[$key]['count'] = ($groups[$key]['count'] ?? 0) + 1;
            $groups[$key]['total_time'] =
                ($groups[$key]['total_time'] ?? 0) + $query['execution_time'];
        }

        return $groups;
    }
}
