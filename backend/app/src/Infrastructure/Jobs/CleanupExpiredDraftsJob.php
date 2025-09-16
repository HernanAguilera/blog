<?php

declare(strict_types=1);

namespace App\src\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupExpiredDraftsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // 5 minutes timeout
    public int $tries = 3;

    public function __construct()
    {
        $this->onQueue('low'); // Use low priority queue
    }

    public function handle(): void
    {
        $startTime = microtime(true);

        Log::info('Starting cleanup of expired drafts');

        try {
            $cutoffTime = Carbon::now();

            // Get count before deletion for logging
            $expiredCount = DB::table('post_drafts')
                ->where('expires_at', '<', $cutoffTime)
                ->count();

            if ($expiredCount === 0) {
                Log::info('No expired drafts found');
                return;
            }

            // Delete expired drafts
            $deletedCount = DB::table('post_drafts')
                ->where('expires_at', '<', $cutoffTime)
                ->delete();

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Cleanup of expired drafts completed', [
                'expired_drafts_found' => $expiredCount,
                'drafts_deleted' => $deletedCount,
                'execution_time_ms' => $executionTime,
                'cutoff_time' => $cutoffTime->toISOString()
            ]);

            // Alert if deletion count doesn't match expected count
            if ($deletedCount !== $expiredCount) {
                Log::warning('Draft deletion count mismatch', [
                    'expected' => $expiredCount,
                    'actual' => $deletedCount
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error during expired drafts cleanup', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ]);

            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Cleanup expired drafts job failed permanently', [
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }

    public function retryUntil(): \DateTime
    {
        return now()->addHours(1); // Don't retry after 1 hour
    }

    public function uniqueId(): string
    {
        return 'cleanup-expired-drafts';
    }
}