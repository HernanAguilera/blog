<?php

declare(strict_types=1);

namespace App\src\Application\UseCases\Post;

use App\src\Application\DTOs\Post\UpdatePostDTO;
use App\src\Domain\Post\Entities\Post;
use App\src\Domain\Post\Repositories\PostRepositoryInterface;
use App\src\Domain\Post\ValueObjects\PostId;
use App\src\Domain\Post\ValueObjects\PostTitle;
use App\src\Domain\Post\ValueObjects\PostSlug;
use App\src\Domain\Post\ValueObjects\PostContent;
use App\src\Domain\Post\ValueObjects\PostStatus;
use App\src\Domain\Post\ValueObjects\MetaDescription;
use App\src\Domain\Post\Events\PostUpdated;
use App\src\Domain\Post\Exceptions\PostNotFoundException;
use App\src\Domain\Post\Exceptions\PostValidationException;
use DateTimeImmutable;
use Illuminate\Support\Str;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class UpdatePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function execute(UpdatePostDTO $dto): Post
    {
        // Find existing post
        $postId = new PostId($dto->id);
        $post = $this->postRepository->findById($postId);

        if (!$post) {
            throw new PostNotFoundException("Post with ID {$dto->id} not found");
        }

        // Update title if provided
        if ($dto->title !== null) {
            $post->updateTitle(new PostTitle($dto->title));
        }

        // Update slug if provided
        if ($dto->slug !== null) {
            $newSlug = new PostSlug($dto->slug);

            // Check slug uniqueness (excluding current post)
            if (!$this->postRepository->isSlugUniqueForPost($newSlug, $postId)) {
                throw new PostValidationException("Slug '{$dto->slug}' is already in use");
            }

            $post->updateSlug($newSlug);
        }

        // Update content if provided
        if ($dto->content !== null) {
            $content = new PostContent($dto->content, $dto->excerpt);
            $post->updateContent($content);
        }

        // Update status if provided
        if ($dto->status !== null) {
            $newStatus = new PostStatus($dto->status);

            // Handle status changes
            if ($newStatus->isPublished() && !$post->getStatus()->isPublished()) {
                $post->publish();
            } elseif ($newStatus->isDraft() && !$post->getStatus()->isDraft()) {
                $post->makeDraft();
            } elseif ($newStatus->isArchived() && !$post->getStatus()->isArchived()) {
                $post->archive();
            }
        }

        // Update meta description if provided - treat empty strings as null
        if ($dto->metaDescription !== null) {
            $metaDescription = null;
            if (!empty(trim($dto->metaDescription))) {
                $metaDescription = new MetaDescription($dto->metaDescription);
            }
            $post->updateMetaDescription($metaDescription);
        }

        // Update featured image if provided
        if ($dto->featuredImage !== null) {
            $post->updateFeaturedImage($dto->featuredImage);
        }

        // Save updated post
        $updatedPost = $this->postRepository->save($post);

        // Dispatch domain event
        $this->eventDispatcher->dispatch(
            new PostUpdated($updatedPost->getId(), $updatedPost->getTitle(), $updatedPost->getAuthorId())
        );

        return $updatedPost;
    }
}