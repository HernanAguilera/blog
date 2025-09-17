<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Add scheduled_at timestamp column
            $table->timestamp('scheduled_at')->nullable()->after('published_at');

            // Add index for scheduled posts queries
            $table->index('scheduled_at');
            $table->index(['status', 'scheduled_at']);
        });

        // Update enum constraint to include 'scheduled' status
        // For both PostgreSQL and MySQL, we need to recreate the constraint
        if (DB::getDriverName() === 'pgsql') {
            // Drop existing check constraint
            DB::statement("ALTER TABLE posts DROP CONSTRAINT IF EXISTS posts_status_check");
            // Add new check constraint with 'scheduled' value
            DB::statement("ALTER TABLE posts ADD CONSTRAINT posts_status_check CHECK (status IN ('draft', 'published', 'archived', 'scheduled'))");
        } else {
            // For MySQL
            DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'published', 'archived', 'scheduled') DEFAULT 'draft'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex(['status', 'scheduled_at']);
            $table->dropIndex(['scheduled_at']);

            // Remove scheduled_at column
            $table->dropColumn('scheduled_at');
        });

        // Restore original enum constraint (remove 'scheduled' status)
        // Note: This is risky if there are posts with 'scheduled' status
        if (DB::getDriverName() === 'pgsql') {
            // Drop current constraint
            DB::statement("ALTER TABLE posts DROP CONSTRAINT IF EXISTS posts_status_check");
            // Restore original constraint
            DB::statement("ALTER TABLE posts ADD CONSTRAINT posts_status_check CHECK (status IN ('draft', 'published', 'archived'))");
        } else {
            // For MySQL
            DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'published', 'archived') DEFAULT 'draft'");
        }
    }
};