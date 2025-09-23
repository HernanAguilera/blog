<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Application\DTOs\Post\PostFilterDTO;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostStatus;
use Blog\Domain\User\ValueObjects\UserId;

final readonly class GetDraftPostsUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {}

    public function execute(PostFilterDTO $filter): array
    {
        $status = new PostStatus('draft');

        if ($filter->search) {
            return [
                'posts' => $this->postRepository->search($filter->search, $filter->page, $filter->perPage),
                'total' => $this->postRepository->count(),
                'page' => $filter->page,
                'perPage' => $filter->perPage
            ];
        }

        if ($filter->authorId) {
            $authorId = new UserId($filter->authorId);
            return $this->postRepository->findByAuthorAndStatus($authorId, $status, $filter->page, $filter->perPage);
        }

        return $this->postRepository->findByStatus($status, $filter->page, $filter->perPage);
    }
}