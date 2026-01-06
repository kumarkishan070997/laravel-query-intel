<?php

namespace Kishan\QueryIntel\Analyzers;

class SlowQueryAnalyzer
{
    public function analyze(array $queries, int $threshold): array
    {
        return array_filter($queries, fn ($q) =>
            $q['time'] >= $threshold
        );
    }
}
