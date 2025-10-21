<?php

declare(strict_types=1);

namespace Src\Application\Page\UseCases;

use Src\Application\Page\DTOs\PageResponseDTO;
use Src\Domain\Page\Contracts\PageRepositoryInterface;

final readonly class GetAllPagesUseCase
{
    public function __construct(
        private PageRepositoryInterface $pageRepository
    ) {
    }

    /**
     * @return array<PageResponseDTO>
     */
    public function execute(): array
    {
        $pages = $this->pageRepository->getAll();

        return array_map(
            fn($page) => PageResponseDTO::fromDomain($page),
            $pages
        );
    }
}
