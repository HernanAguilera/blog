<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            // Author type and data
            $table->enum('author_type', ['registered', 'anonymous'])->default('registered');
            $table->string('author_name')->nullable();
            $table->string('author_email')->nullable();
            $table->string('author_website')->nullable();

            // Comment content and status
            $table->text('content');
            $table->enum('status', ['pending_approval', 'approved', 'rejected', 'spam'])->default('pending_approval');

            // Thread support
            $table->uuid('parent_id')->nullable();

            // Security tracking
            $table->string('ip_address');
            $table->text('user_agent');

            // Moderation tracking
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Indexes for performance
            $table->index('post_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('user_id');
            $table->index(['post_id', 'status']);
            $table->index(['post_id', 'parent_id']);
        });

        // Add self-referencing foreign key after table creation
        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
