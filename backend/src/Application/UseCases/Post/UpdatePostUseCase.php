<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Application\DTOs\Post\UpdatePostDTO;
use Blog\Domain\Post\Entities\Post;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\Post\ValueObjects\PostTitle;
use Blog\Domain\Post\ValueObjects\PostSlug;
use Blog\Domain\Post\ValueObjects\PostContent;
use Blog\Domain\Post\ValueObjects\PostStatus;
use Blog\Domain\Post\ValueObjects\MetaDescription;
use Blog\Domain\Post\Events\PostUpdated;
use Blog\Domain\Post\Exceptions\PostNotFoundException;
use Blog\Domain\Post\Exceptions\PostValidationException;
use Blog\Domain\Shared\Events\EventDispatcherInterface;
use DateTimeImmutable;
use Illuminate\Support\Str;

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
            $trimmedMeta = trim($dto->metaDescription);
            $metaDescription = null;

            // Only create MetaDescription if we have substantial content
            if (!empty($trimmedMeta) && mb_strlen($trimmedMeta) >= 50) {
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