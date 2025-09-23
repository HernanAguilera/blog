<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Application\DTOs\Post\PostFilterDTO;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;

final readonly class GetPublishedPostsUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {}

    public function execute(PostFilterDTO $filter): array
    {
        if ($filter->search) {
            return $this->postRepository->searchPublished(
                $filter->search,
                $filter->page,
                $filter->perPage
            );
        }

        return $this->postRepository->findPublished($filter->page, $filter->perPage);
    }
}