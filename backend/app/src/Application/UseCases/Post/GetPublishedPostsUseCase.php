<?php

declare(strict_types=1);

namespace App\src\Application\UseCases\Post;

use App\src\Application\DTOs\Post\PostFilterDTO;
use App\src\Domain\Post\Repositories\PostRepositoryInterface;

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