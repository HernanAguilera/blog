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

        // Update enum to include 'scheduled' status
        // Note: For PostgreSQL, we need to add the new value to the enum
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TYPE post_status ADD VALUE IF NOT EXISTS 'scheduled'");
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

        // Note: Removing enum values is complex and risky in production
        // It's recommended to leave the 'scheduled' value for data integrity
        // If absolutely necessary, create a separate migration to handle this
    }
};