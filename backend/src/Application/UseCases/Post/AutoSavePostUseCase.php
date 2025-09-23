<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Application\DTOs\Post\AutoSavePostDTO;
use Blog\Domain\Post\Services\HtmlSanitizerInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

final readonly class AutoSavePostUseCase
{
    public function __construct(
        private HtmlSanitizerInterface $htmlSanitizer
    ) {}

    public function execute(AutoSavePostDTO $dto): array
    {
        // Validate content size (prevent DoS)
        $maxContentSize = config('editor.max_content_size', 1048576); // 1MB default
        if (strlen($dto->content) > $maxContentSize) {
            throw new \InvalidArgumentException('Content exceeds maximum allowed size');
        }

        // Sanitize content
        $sanitizedContent = $this->htmlSanitizer->sanitize($dto->content);

        // Calculate expiration
        $draftTtlHours = config('editor.draft_ttl_hours', 24);
        $expiresAt = Carbon::now()->addHours($draftTtlHours);

        // Check if draft already exists for this user/post combination
        $existingDraft = DB::table('post_drafts')
            ->where('user_id', $dto->userId)
            ->where('post_id', $dto->postId)
            ->first();

        $draftData = [
            'user_id' => $dto->userId,
            'post_id' => $dto->postId,
            'title' => $dto->title,
            'content' => (string) $sanitizedContent,
            'excerpt' => $dto->excerpt,
            'slug' => $dto->slug,
            'status' => $dto->status,
            'featured_image' => $dto->featuredImage,
            'meta_description' => $dto->metaDescription,
            'scheduled_at' => $dto->scheduledAt ? Carbon::parse($dto->scheduledAt) : null,
            'expires_at' => $expiresAt,
            'updated_at' => Carbon::now()
        ];

        if ($existingDraft) {
            // Update existing draft
            DB::table('post_drafts')
                ->where('id', $existingDraft->id)
                ->update($draftData);

            $draftId = $existingDraft->id;
        } else {
            // Create new draft
            $draftData['created_at'] = Carbon::now();
            $draftId = DB::table('post_drafts')->insertGetId($draftData);
        }

        // Clean up expired drafts for this user
        $this->cleanupExpiredDrafts($dto->userId);

        return [
            'id' => $draftId,
            'saved_at' => Carbon::now()->toISOString(),
            'expires_at' => $expiresAt->toISOString(),
            'word_count' => $sanitizedContent->getWordCount(),
            'reading_time' => $sanitizedContent->getReadingTime()
        ];
    }

    private function cleanupExpiredDrafts(int $userId): void
    {
        DB::table('post_drafts')
            ->where('user_id', $userId)
            ->where('expires_at', '<', Carbon::now())
            ->delete();
    }
}