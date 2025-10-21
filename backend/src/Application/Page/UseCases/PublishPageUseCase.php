<?php

declare(strict_types=1);

namespace Src\Application\Page\UseCases;

use Src\Application\Page\DTOs\PageResponseDTO;
use Src\Domain\Page\Contracts\PageRepositoryInterface;
use Src\Domain\Page\Exceptions\PageNotFoundException;
use Src\Domain\Page\ValueObjects\PageId;

final readonly class PublishPageUseCase
{
    public function __construct(
        private PageRepositoryInterface $pageRepository
    ) {
    }

    public function execute(string $id): PageResponseDTO
    {
        $pageId = PageId::fromString($id);
        $page = $this->pageRepository->findById($pageId);

        if ($page === null) {
            throw PageNotFoundException::withId($id);
        }

        $page->publish();
        $this->pageRepository->save($page);

        return PageResponseDTO::fromDomain($page);
    }
}
