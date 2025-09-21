<?php

declare(strict_types=1);

namespace App\src\Application\UseCases\Post;

use App\src\Application\DTOs\Post\PostFilterDTO;
use App\src\Domain\Post\Repositories\PostRepositoryInterface;

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