<?php

namespace Kishan\QueryIntel\Support;

class QueryIntelTracker
{
    public int $count = 0;
    public float $time = 0.0;
    public bool $hasRead = false;
    public bool $hasWrite = false;
}
