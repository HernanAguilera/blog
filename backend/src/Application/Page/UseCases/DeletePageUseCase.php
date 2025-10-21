<?php

declare(strict_types=1);

namespace Src\Application\Page\UseCases;

use Src\Domain\Page\Contracts\PageRepositoryInterface;
use Src\Domain\Page\Exceptions\PageNotFoundException;
use Src\Domain\Page\ValueObjects\PageId;

final readonly class DeletePageUseCase
{
    public function __construct(
        private PageRepositoryInterface $pageRepository
    ) {
    }

    public function execute(string $id): void
    {
        $pageId = PageId::fromString($id);
        $page = $this->pageRepository->findById($pageId);

        if ($page === null) {
            throw PageNotFoundException::withId($id);
        }

        $this->pageRepository->delete($pageId);
    }
}
