<?php

declare(strict_types=1);

namespace Src\Application\Page\UseCases;

use Src\Application\Page\DTOs\PageResponseDTO;
use Src\Domain\Page\Contracts\PageRepositoryInterface;
use Src\Domain\Page\Exceptions\PageNotFoundException;

final readonly class GetPublishedPageBySlugUseCase
{
    public function __construct(
        private PageRepositoryInterface $pageRepository
    ) {
    }

    public function execute(string $slug): PageResponseDTO
    {
        $page = $this->pageRepository->findPublishedBySlug($slug);

        if ($page === null) {
            throw PageNotFoundException::withSlug($slug);
        }

        return PageResponseDTO::fromDomain($page);
    }
}
