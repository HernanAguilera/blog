<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Application\DTOs\Post\PreviewPostDTO;
use Blog\Domain\Post\Services\HtmlSanitizerInterface;
use Blog\Domain\Post\ValueObjects\PreviewToken;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

final readonly class PreviewPostUseCase
{
    public function __construct(
        private HtmlSanitizerInterface $htmlSanitizer
    ) {}

    public function execute(PreviewPostDTO $dto): array
    {
        // Validate content size (prevent DoS)
        $maxContentSize = config('editor.max_content_size', 1048576); // 1MB default
        if (strlen($dto->content) > $maxContentSize) {
            throw new \InvalidArgumentException('Content exceeds maximum allowed size');
        }

        // Generate unique preview token
        $token = PreviewToken::generate();

        // Sanitize content for preview (more restrictive)
        $sanitizedContent = $this->htmlSanitizer->sanitizeForPreview($dto->content);

        // Calculate expiration
        $expiresAt = Carbon::now()->addHours($dto->ttlHours);

        // Store preview data
        $previewId = DB::table('post_previews')->insertGetId([
            'token' => (string) $token,
            'user_id' => $dto->userId,
            'title' => $dto->title,
            'content' => (string) $sanitizedContent,
            'excerpt' => $dto->excerpt,
            'slug' => $dto->slug,
            'status' => $dto->status,
            'featured_image' => $dto->featuredImage,
            'meta_description' => $dto->metaDescription,
            'scheduled_at' => $dto->scheduledAt ? Carbon::parse($dto->scheduledAt) : null,
            'expires_at' => $expiresAt,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // Clean up expired previews
        $this->cleanupExpiredPreviews();

        return [
            'id' => $previewId,
            'token' => (string) $token,
            'preview_url' => "/preview/{$token}",
            'expires_at' => $expiresAt->toISOString(),
            'created_at' => Carbon::now()->toISOString()
        ];
    }

    public function getPreviewByToken(string $token): ?array
    {
        $preview = DB::table('post_previews')
            ->where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$preview) {
            return null;
        }

        return [
            'id' => $preview->id,
            'title' => $preview->title,
            'content' => $preview->content,
            'excerpt' => $preview->excerpt,
            'slug' => $preview->slug,
            'status' => $preview->status,
            'featured_image' => $preview->featured_image,
            'meta_description' => $preview->meta_description,
            'scheduled_at' => $preview->scheduled_at,
            'created_at' => $preview->created_at,
            'expires_at' => $preview->expires_at
        ];
    }

    private function cleanupExpiredPreviews(): void
    {
        DB::table('post_previews')
            ->where('expires_at', '<', Carbon::now())
            ->delete();
    }
}