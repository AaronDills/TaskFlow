<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'restricted.email' => \App\Http\Middleware\RestrictedEmailAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function ($schedule) {
        // Clean up tasks every Monday at 6 AM (before work starts)
        // - Deletes completed tasks from previous weeks
        // - Returns incomplete project tasks to recommended section
        // - Deletes incomplete standalone tasks
        $schedule->command('tasks:cleanup-weekly')
                 ->weeklyOn(1, '06:00')
                 ->withoutOverlapping()
                 ->description('Clean up tasks from previous weeks');

        // Process pending feature requests every minute
        // - Checks for pending feedback/feature requests
        // - Dispatches them to the queue for AI analysis
        $schedule->command('feedback:process --limit=5')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->description('Process pending feature requests');
    })
    ->create();
