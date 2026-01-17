<?php

namespace Kishan\QueryIntel\Support;

class QueryIntelTracker
{
    public int $count = 0;
    public float $time = 0.0;

    public bool $hasRead = false;
    public bool $hasWrite = false;

    public array $queryFingerprints = [];

    public function reset(): void
    {
        $this->count = 0;
        $this->time = 0.0;
        $this->hasRead = false;
        $this->hasWrite = false;
        $this->queryFingerprints = [];
    }
}
