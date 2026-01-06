<?php

namespace Kishan\QueryIntel\Commands;

use Illuminate\Console\Command;
use Kishan\QueryIntel\Storage\QueryStorage;
use Kishan\QueryIntel\Collectors\QueryCollector;
use Kishan\QueryIntel\Analyzers\SlowQueryAnalyzer;

class QueryIntelCommand extends Command
{
    protected $signature = 'query:intel';

    protected $description = 'Analyze executed database queries';

    public function handle()
{
    $storage = app(QueryStorage::class);
    $queries = $storage->all();

    $slow = app(SlowQueryAnalyzer::class)
        ->analyze($queries, config('query-intel.slow_query_threshold'));

    $this->info('📊 Query Intel Report');
    $this->line('Total Queries: ' . count($queries));
    $this->line('Slow Queries: ' . count($slow));
}
}
