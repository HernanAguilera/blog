<?php

declare(strict_types=1);

namespace App\src\Infrastructure\Jobs;

use App\src\Application\UseCases\Post\PublishPostUseCase;
use App\src\Domain\Post\Repositories\PostRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ScheduledPostPublisher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 minutes

    public function __construct()
    {
        // No constructor parameters needed for this job
    }

    public function handle(
        PostRepositoryInterface $postRepository,
        PublishPostUseCase $publishPostUseCase
    ): void {
        Log::info('Starting scheduled post publishing job');

        try {
            $scheduledPosts = $postRepository->findScheduledReadyToPublish();

            if (empty($scheduledPosts)) {
                Log::info('No scheduled posts ready to publish');
                return;
            }

            $publishedCount = 0;
            $failedCount = 0;

            foreach ($scheduledPosts as $post) {
                try {
                    // Use the system/admin user ID for automatic publishing
                    // In a real scenario, this might be the original author or a system user
                    $publishPostUseCase->execute(
                        $post->getId()->value(),
                        $post->getAuthorId()->value() // Use original author
                    );

                    $publishedCount++;

                    Log::info('Successfully published scheduled post', [
                        'post_id' => $post->getId()->value(),
                        'title' => $post->getTitle()->value(),
                        'scheduled_at' => $post->getScheduledAt()?->format('Y-m-d H:i:s'),
                    ]);

                } catch (Throwable $e) {
                    $failedCount++;

                    Log::error('Failed to publish scheduled post', [
                        'post_id' => $post->getId()->value(),
                        'title' => $post->getTitle()->value(),
                        'error' => $e->getMessage(),
                        'exception' => get_class($e),
                    ]);

                    // Continue with other posts even if one fails
                    continue;
                }
            }

            Log::info('Completed scheduled post publishing job', [
                'total_scheduled' => count($scheduledPosts),
                'published' => $publishedCount,
                'failed' => $failedCount,
            ]);

        } catch (Throwable $e) {
            Log::error('Critical error in scheduled post publishing job', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to mark job as failed
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Scheduled post publishing job failed permanently', [
            'error' => $exception->getMessage(),
            'exception' => get_class($exception),
            'attempts' => $this->attempts(),
        ]);
    }
}