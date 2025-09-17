<?php

declare(strict_types=1);

namespace App\src\Application\UseCases\Post;

use App\src\Application\DTOs\Post\CreatePostDTO;
use App\src\Domain\Post\Entities\Post;
use App\src\Domain\Post\Repositories\PostRepositoryInterface;
use App\src\Domain\Post\ValueObjects\PostTitle;
use App\src\Domain\Post\ValueObjects\PostSlug;
use App\src\Domain\Post\ValueObjects\PostContent;
use App\src\Domain\Post\ValueObjects\PostStatus;
use App\src\Domain\Post\ValueObjects\MetaDescription;
use App\src\Domain\User\ValueObjects\UserId;
use App\src\Domain\Post\Events\PostCreated;
use App\src\Domain\Shared\Events\EventDispatcherInterface;
use DateTimeImmutable;
use Illuminate\Support\Str;

final readonly class CreatePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function execute(CreatePostDTO $dto): Post
    {
        // Generate slug if not provided
        $slug = $dto->slug ?: Str::slug($dto->title);

        // Validate slug uniqueness
        $postSlug = new PostSlug($slug);
        if ($this->postRepository->existsBySlug($postSlug)) {
            $slug = $slug . '-' . time();
            $postSlug = new PostSlug($slug);
        }

        // Create value objects
        $title = new PostTitle($dto->title);
        $content = new PostContent($dto->content, $dto->excerpt);
        $status = new PostStatus($dto->status);
        $authorId = new UserId($dto->authorId);
        // Handle meta description - treat empty strings as null
        $metaDescription = null;
        if (!empty(trim($dto->metaDescription ?? ''))) {
            $metaDescription = new MetaDescription($dto->metaDescription);
        }

        // Create post entity
        $post = new Post(
            null, // ID will be set by repository
            $title,
            $postSlug,
            $content,
            $status,
            $authorId,
            $metaDescription
        );

        // Handle publishing if needed
        if ($status->isPublished()) {
            $post->publish();
        }

        // Save post
        $savedPost = $this->postRepository->save($post);

        // Dispatch domain event
        $this->eventDispatcher->dispatch(
            new PostCreated($savedPost)
        );

        return $savedPost;
    }
}