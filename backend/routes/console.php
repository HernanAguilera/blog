<?php

use App\src\Infrastructure\Jobs\CleanupExpiredDraftsJob;
use App\src\Infrastructure\Jobs\CleanupExpiredPreviewsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule cleanup jobs
Schedule::job(new CleanupExpiredDraftsJob())
    ->daily()
    ->at('02:00')
    ->name('cleanup-expired-drafts')
    ->withoutOverlapping()
    ->description('Clean up expired post drafts daily at 2 AM');

Schedule::job(new CleanupExpiredPreviewsJob())
    ->daily()
    ->at('02:30')
    ->name('cleanup-expired-previews')
    ->withoutOverlapping()
    ->description('Clean up expired post previews daily at 2:30 AM');
