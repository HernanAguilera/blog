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
        Schema::create('pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 100)->unique();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('page_translations', function (Blueprint $table) {
            $table->id();
            $table->uuid('page_id');
            $table->string('locale', 5);
            $table->string('title', 255);
            $table->text('content');
            $table->string('meta_description', 160)->nullable();
            $table->timestamps();

            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
            $table->unique(['page_id', 'locale']);
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_translations');
        Schema::dropIfExists('pages');
    }
};
