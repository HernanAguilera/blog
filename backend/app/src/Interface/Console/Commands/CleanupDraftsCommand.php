<?php

declare(strict_types=1);

namespace App\src\Interface\Console\Commands;

use App\src\Infrastructure\Jobs\CleanupExpiredDraftsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupDraftsCommand extends Command
{
    protected $signature = 'posts:cleanup-drafts
                           {--force : Force cleanup without confirmation}
                           {--dry-run : Show what would be deleted without actually deleting}
                           {--older-than= : Cleanup drafts older than specified hours (default: expired only)}';

    protected $description = 'Clean up expired or old post drafts';

    public function handle(): int
    {
        $this->info('Post Drafts Cleanup Tool');
        $this->line('=============================');

        try {
            $dryRun = $this->option('dry-run');
            $force = $this->option('force');
            $olderThanHours = $this->option('older-than');

            // Determine cutoff time
            if ($olderThanHours) {
                $cutoffTime = Carbon::now()->subHours((int) $olderThanHours);
                $this->info("Finding drafts older than {$olderThanHours} hours ({$cutoffTime->format('Y-m-d H:i:s')})");
            } else {
                $cutoffTime = Carbon::now();
                $this->info("Finding expired drafts (expires_at < {$cutoffTime->format('Y-m-d H:i:s')})");
            }

            // Build query
            $query = DB::table('post_drafts');

            if ($olderThanHours) {
                $query->where('created_at', '<', $cutoffTime);
            } else {
                $query->where('expires_at', '<', $cutoffTime);
            }

            // Get statistics
            $draftsToDelete = $query->count();

            if ($draftsToDelete === 0) {
                $this->info('✅ No drafts found matching criteria.');
                return self::SUCCESS;
            }

            // Show what will be deleted
            $stats = $query->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users'),
                DB::raw('MIN(created_at) as oldest_draft'),
                DB::raw('MAX(created_at) as newest_draft')
            )->first();

            $this->table(['Metric', 'Value'], [
                ['Drafts to delete', $draftsToDelete],
                ['Unique users affected', $stats->unique_users],
                ['Oldest draft', $stats->oldest_draft],
                ['Newest draft', $stats->newest_draft]
            ]);

            if ($dryRun) {
                $this->warn('🔍 DRY RUN: No drafts were actually deleted.');
                return self::SUCCESS;
            }

            // Confirm deletion
            if (!$force && !$this->confirm('Do you want to proceed with the deletion?')) {
                $this->info('Cleanup cancelled.');
                return self::SUCCESS;
            }

            // Perform deletion
            $this->info('🗑️  Deleting drafts...');

            $startTime = microtime(true);
            $deletedCount = $query->delete();
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $this->info("✅ Successfully deleted {$deletedCount} drafts in {$executionTime}ms");

            if ($deletedCount !== $draftsToDelete) {
                $this->warn("⚠️  Warning: Expected to delete {$draftsToDelete} but actually deleted {$deletedCount}");
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