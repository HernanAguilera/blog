<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Application\DTOs\Post\PostFilterDTO;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;

final readonly class GetAllPostsUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {}

    public function execute(PostFilterDTO $filter): array
    {
        return $this->postRepository->findWithFilters($filter);
    }
}