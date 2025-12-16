<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupWeeklyTimeSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:cleanup-weekly {--dry-run : Show what would be done without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up completed tasks from previous weeks and return incomplete project tasks to recommended';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentMonday = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $isDryRun = $this->option('dry-run');

        // Find tasks from previous weeks that are in must/may categories
        $oldTasks = Task::whereIn('category', ['must', 'may'])
            ->where(function ($query) use ($currentMonday) {
                // Tasks with scheduled dates before this week
                $query->where('scheduled_date', '<', $currentMonday->toDateString())
                    // Or tasks created before this week (for unscheduled tasks)
                    ->orWhere(function ($q) use ($currentMonday) {
                        $q->whereNull('scheduled_date')
                          ->where('created_at', '<', $currentMonday);
                    });
            })
            ->get();

        if ($oldTasks->isEmpty()) {
            $this->info('No tasks to clean up.');
            return;
        }

        // Separate completed and incomplete tasks
        $completedTasks = $oldTasks->where('completed', true);
        $incompleteTasks = $oldTasks->where('completed', false);

        // Further separate incomplete tasks into project tasks and standalone
        $incompleteProjectTasks = $incompleteTasks->filter(fn($t) => $t->project_id && $t->parent_id);
        $incompleteStandaloneTasks = $incompleteTasks->filter(fn($t) => !$t->project_id || !$t->parent_id);

        $this->info("Found {$oldTasks->count()} tasks from previous weeks:");
        $this->line("  - {$completedTasks->count()} completed tasks (will be deleted)");
        $this->line("  - {$incompleteProjectTasks->count()} incomplete project tasks (will return to recommended)");
        $this->line("  - {$incompleteStandaloneTasks->count()} incomplete standalone tasks (will be deleted)");

        if ($isDryRun) {
            $this->newLine();
            $this->info('DRY RUN - No changes made.');

            if ($completedTasks->isNotEmpty()) {
                $this->newLine();
                $this->line('Completed tasks to delete:');
                foreach ($completedTasks as $task) {
                    $this->line("  - [{$task->category}] {$task->title}");
                }
            }

            if ($incompleteProjectTasks->isNotEmpty()) {
                $this->newLine();
                $this->line('Project tasks to return to recommended:');
                foreach ($incompleteProjectTasks as $task) {
                    $this->line("  - [{$task->category}] {$task->title} (Project: {$task->project->name})");
                }
            }

            if ($incompleteStandaloneTasks->isNotEmpty()) {
                $this->newLine();
                $this->line('Standalone tasks to delete:');
                foreach ($incompleteStandaloneTasks as $task) {
                    $this->line("  - [{$task->category}] {$task->title}");
                }
            }

            $this->newLine();
            $this->info('Run without --dry-run to execute cleanup.');
            return;
        }

        // Delete completed tasks
        $deletedCompleted = 0;
        foreach ($completedTasks as $task) {
            $task->delete();
            $deletedCompleted++;
        }

        // Return incomplete project tasks to recommended
        $returnedToRecommended = 0;
        foreach ($incompleteProjectTasks as $task) {
            $task->update([
                'category' => 'recommended',
                'scheduled_date' => null,
                'scheduled_time' => null,
                'order' => null,
            ]);
            $returnedToRecommended++;
        }

        // Delete incomplete standalone tasks
        $deletedStandalone = 0;
        foreach ($incompleteStandaloneTasks as $task) {
            $task->delete();
            $deletedStandalone++;
        }

        $this->newLine();
        $this->info("Cleanup complete:");
        $this->line("  - Deleted {$deletedCompleted} completed tasks");
        $this->line("  - Returned {$returnedToRecommended} incomplete project tasks to recommended");
        $this->line("  - Deleted {$deletedStandalone} incomplete standalone tasks");
        $this->line("  - Current week starts: {$currentMonday->toDateString()}");
    }
}
