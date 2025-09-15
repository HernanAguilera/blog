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
        Schema::table('users', function (Blueprint $table) {
            // Add social authentication fields
            $table->string('social_id')->nullable()->after('email_verified_at');
            $table->enum('social_provider', ['google', 'facebook', 'twitter'])->nullable()->after('social_id');

            // Add unique index for social_id + social_provider combination
            $table->unique(['social_id', 'social_provider'], 'users_social_unique');

            // Add index for social_provider for faster queries
            $table->index('social_provider', 'users_social_provider_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('users_social_unique');
            $table->dropIndex('users_social_provider_index');

            // Drop social authentication fields
            $table->dropColumn(['social_id', 'social_provider']);
        });
    }
};
