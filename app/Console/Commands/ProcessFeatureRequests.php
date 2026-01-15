<?php

namespace App\Console\Commands;

use App\Jobs\ProcessFeatureRequest;
use App\Models\FeatureRequest;
use Illuminate\Console\Command;

class ProcessFeatureRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedback:process
                            {--limit=10 : Maximum number of requests to process}
                            {--dry-run : Show what would be processed without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending feature requests and dispatch them to the queue for AI analysis';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $isDryRun = $this->option('dry-run');

        $pendingRequests = FeatureRequest::pending()
            ->orderBy('priority', 'desc') // Process high priority first
            ->orderBy('created_at', 'asc') // Then by submission time
            ->limit($limit)
            ->get();

        if ($pendingRequests->isEmpty()) {
            $this->info('No pending feature requests to process.');
            return Command::SUCCESS;
        }

        $this->info("Found {$pendingRequests->count()} pending feature requests:");

        foreach ($pendingRequests as $request) {
            $priorityLabel = strtoupper($request->priority);
            $typeLabel = str_replace('_', ' ', $request->type);

            $this->line("  [{$priorityLabel}] [{$typeLabel}] {$request->title}");
            $this->line("    Submitted by: {$request->user->email} at {$request->created_at->toDateTimeString()}");
        }

        if ($isDryRun) {
            $this->newLine();
            $this->info('DRY RUN - No jobs dispatched.');
            $this->info('Run without --dry-run to process these requests.');
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->info('Dispatching jobs...');

        $dispatched = 0;
        foreach ($pendingRequests as $request) {
            ProcessFeatureRequest::dispatch($request);
            $this->line("  Dispatched: {$request->title}");
            $dispatched++;
        }

        $this->newLine();
        $this->info("Successfully dispatched {$dispatched} jobs to the queue.");

        return Command::SUCCESS;
    }
}
