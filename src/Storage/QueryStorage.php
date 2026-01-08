<?php

namespace Kishan\QueryIntel\Storage;

use Kishan\QueryIntel\Models\QueryIntelQuery;

class QueryStorage
{
    public function store(array $query): void
    {
        QueryIntelQuery::create($query); // DB insert, no array accumulation
    }

    public function clear(): void
    {
        QueryIntelQuery::truncate(); // optional
    }
}
