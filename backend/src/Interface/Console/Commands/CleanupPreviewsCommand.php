<?php

declare(strict_types=1);

namespace Blog\Interface\Console\Commands;

use Blog\Infrastructure\Jobs\CleanupExpiredPreviewsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupPreviewsCommand extends Command
{
    protected $signature = 'posts:cleanup-previews
                           {--force : Force cleanup without confirmation}
                           {--dry-run : Show what would be deleted without actually deleting}
                           {--older-than= : Cleanup previews older than specified hours (default: expired only)}';

    protected $description = 'Clean up expired or old post previews';

    public function handle(): int
    {
        $this->info('Post Previews Cleanup Tool');
        $this->line('==============================');

        try {
            $dryRun = $this->option('dry-run');
            $force = $this->option('force');
            $olderThanHours = $this->option('older-than');

            // Determine cutoff time
            if ($olderThanHours) {
                $cutoffTime = Carbon::now()->subHours((int) $olderThanHours);
                $this->info("Finding previews older than {$olderThanHours} hours ({$cutoffTime->format('Y-m-d H:i:s')})");
            } else {
                $cutoffTime = Carbon::now();
                $this->info("Finding expired previews (expires_at < {$cutoffTime->format('Y-m-d H:i:s')})");
            }

            // Build query
            $query = DB::table('post_previews');

            if ($olderThanHours) {
                $query->where('created_at', '<', $cutoffTime);
            } else {
                $query->where('expires_at', '<', $cutoffTime);
            }

            // Get statistics
            $previewsToDelete = $query->count();

            if ($previewsToDelete === 0) {
                $this->info('✅ No previews found matching criteria.');
                return self::SUCCESS;
            }

            // Show what will be deleted
            $stats = $query->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users'),
                DB::raw('MIN(created_at) as oldest_preview'),
                DB::raw('MAX(created_at) as newest_preview'),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, expires_at)) as avg_ttl_hours')
            )->first();

            $this->table(['Metric', 'Value'], [
                ['Previews to delete', $previewsToDelete],
                ['Unique users affected', $stats->unique_users],
                ['Oldest preview', $stats->oldest_preview],
                ['Newest preview', $stats->newest_preview],
                ['Average TTL (hours)', round($stats->avg_ttl_hours ?? 0, 1)]
            ]);

            if ($dryRun) {
                $this->warn('🔍 DRY RUN: No previews were actually deleted.');

                // Show sample of tokens that would be deleted (for security audit)
                $sampleTokens = $query->limit(5)->pluck('token');
                if ($sampleTokens->isNotEmpty()) {
                    $this->line('Sample tokens that would be deleted:');
                    foreach ($sampleTokens as $token) {
                        $this->line('  - ' . substr($token, 0, 8) . '...');
                    }
                }

                return self::SUCCESS;
            }

            // Confirm deletion
            if (!$force && !$this->confirm('Do you want to proceed with the deletion?')) {
                $this->info('Cleanup cancelled.');
                return self::SUCCESS;
            }

            // Perform deletion
            $this->info('🗑️  Deleting previews...');

            $startTime = microtime(true);
            $deletedCount = $query->delete();
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $this->info("✅ Successfully deleted {$deletedCount} previews in {$executionTime}ms");

            if ($deletedCount !== $previewsToDelete) {
                $this->warn("⚠️  Warning: Expected to delete {$previewsToDelete} but actually deleted {$deletedCount}");
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error during cleanup: ' . $e->getMessage());

            if ($this->option('verbose')) {
                $this->line($e->getTraceAsString());
            }

            return self::FAILURE;
        }
    }
}