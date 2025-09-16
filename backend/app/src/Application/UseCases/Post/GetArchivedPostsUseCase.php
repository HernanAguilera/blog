<?php

declare(strict_types=1);

namespace App\src\Application\UseCases\Post;

use App\src\Application\DTOs\Post\PostFilterDTO;
use App\src\Domain\Post\Repositories\PostRepositoryInterface;
use App\src\Domain\Post\ValueObjects\PostStatus;
use App\src\Domain\User\ValueObjects\UserId;

final readonly class GetArchivedPostsUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {}

    public function execute(PostFilterDTO $filter): array
    {
        $status = new PostStatus('archived');

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