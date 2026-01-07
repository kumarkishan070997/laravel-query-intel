<?php

namespace Kishan\QueryIntel\Analyzers;

class NPlusOneAnalyzer
{
    public function detect(array $groups, int $threshold): array
    {
        return array_filter($groups, fn ($group) =>
            $group['count'] >= $threshold
        );
    }
}
