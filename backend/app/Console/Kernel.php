<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\PublishScheduledPosts;
use App\Console\Commands\UnblockIPCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UnblockIPCommand::class,
        PublishScheduledPosts::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Publish scheduled posts every minute
        $schedule->command('posts:publish-scheduled --queue')
                 ->everyMinute()
                 ->runInBackground()
                 ->withoutOverlapping(5) // Prevent overlap, timeout after 5 minutes
                 ->appendOutputTo(storage_path('logs/scheduled-posts.log'))
                 ->emailOutputOnFailure(['admin@example.com']); // Configure email

        // Alternative: Run synchronously every 5 minutes for testing
        // $schedule->command('posts:publish-scheduled')
        //          ->everyFiveMinutes()
        //          ->withoutOverlapping(2)
        //          ->appendOutputTo(storage_path('logs/scheduled-posts.log'));

        // Clean up old logs weekly
        $schedule->command('log:clear --name=scheduled-posts')
                 ->weeklyOn(1, '2:00'); // Monday at 2 AM
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}