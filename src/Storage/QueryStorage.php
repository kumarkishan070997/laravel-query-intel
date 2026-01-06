<?php

namespace Kishan\QueryIntel\Storage;

class QueryStorage
{
    protected $path;

    public function __construct()
    {
        $this->path = storage_path('query-intel/queries.json');

        if (!is_dir(dirname($this->path))) {
            mkdir(dirname($this->path), 0755, true);
        }

        if (!file_exists($this->path)) {
            file_put_contents($this->path, json_encode([]));
        }
    }

    public function store(array $query): void
    {
        $queries = $this->all();
        $queries[] = $query;

        file_put_contents($this->path, json_encode($queries, JSON_PRETTY_PRINT));
    }

    public function all(): array
    {
        return json_decode(file_get_contents($this->path), true) ?? [];
    }

    public function clear(): void
    {
        file_put_contents($this->path, json_encode([]));
    }
}
