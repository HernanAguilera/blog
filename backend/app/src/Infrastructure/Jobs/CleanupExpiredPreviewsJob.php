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

class CleanupExpiredPreviewsJob implements ShouldQueue
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

        Log::info('Starting cleanup of expired previews');

        try {
            $cutoffTime = Carbon::now();

            // Get count before deletion for logging
            $expiredCount = DB::table('post_previews')
                ->where('expires_at', '<', $cutoffTime)
                ->count();

            if ($expiredCount === 0) {
                Log::info('No expired previews found');
                return;
            }

            // Get some stats before deletion for security monitoring
            $previewStats = DB::table('post_previews')
                ->where('expires_at', '<', $cutoffTime)
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw('COUNT(DISTINCT user_id) as unique_users'),
                    DB::raw('MIN(created_at) as oldest_preview'),
                    DB::raw('MAX(created_at) as newest_preview')
                )
                ->first();

            // Delete expired previews
            $deletedCount = DB::table('post_previews')
                ->where('expires_at', '<', $cutoffTime)
                ->delete();

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Cleanup of expired previews completed', [
                'expired_previews_found' => $expiredCount,
                'previews_deleted' => $deletedCount,
                'unique_users_affected' => $previewStats->unique_users ?? 0,
                'oldest_preview' => $previewStats->oldest_preview ?? null,
                'newest_preview' => $previewStats->newest_preview ?? null,
                'execution_time_ms' => $executionTime,
                'cutoff_time' => $cutoffTime->toISOString()
            ]);

            // Alert if deletion count doesn't match expected count
            if ($deletedCount !== $expiredCount) {
                Log::warning('Preview deletion count mismatch', [
                    'expected' => $expiredCount,
                    'actual' => $deletedCount
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error during expired previews cleanup', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ]);

            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Cleanup expired previews job failed permanently', [
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
        return 'cleanup-expired-previews';
    }
}