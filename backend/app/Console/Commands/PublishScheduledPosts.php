<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Blog\Infrastructure\Jobs\ScheduledPostPublisher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled
                            {--queue : Dispatch job to queue instead of running synchronously}
                            {--force : Force execution even if no scheduled posts are found}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled posts that are ready to be published';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting scheduled post publishing...');

        try {
            if ($this->option('queue')) {
                return $this->dispatchToQueue();
            }

            return $this->executeSynchronously();

        } catch (Throwable $e) {
            $this->error("Critical error during scheduled post publishing: {$e->getMessage()}");

            Log::error('Critical error in PublishScheduledPosts command', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }

    private function dispatchToQueue(): int
    {
        try {
            ScheduledPostPublisher::dispatch();

            $this->info('Scheduled post publishing job dispatched to queue successfully.');

            Log::info('Scheduled post publishing job dispatched via command');

            return Command::SUCCESS;

        } catch (Throwable $e) {
            $this->error("Failed to dispatch job to queue: {$e->getMessage()}");

            Log::error('Failed to dispatch ScheduledPostPublisher job', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return Command::FAILURE;
        }
    }

    private function executeSynchronously(): int
    {
        try {
            $this->info('Executing scheduled post publishing synchronously...');

            $job = new ScheduledPostPublisher();

            // Execute the job handle method directly
            $job->handle(
                app(\Blog\Domain\Post\Repositories\PostRepositoryInterface::class),
                app(\Blog\Application\UseCases\Post\PublishPostUseCase::class)
            );

            $this->info('Scheduled post publishing completed successfully.');

            Log::info('Scheduled post publishing executed synchronously via command');

            return Command::SUCCESS;

        } catch (Throwable $e) {
            $this->error("Failed to execute scheduled post publishing: {$e->getMessage()}");

            Log::error('Failed to execute ScheduledPostPublisher synchronously', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return Command::FAILURE;
        }
    }
}