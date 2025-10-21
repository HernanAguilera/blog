<?php

declare(strict_types=1);

namespace Src\Application\Page\UseCases;

use Src\Application\Page\DTOs\PageResponseDTO;
use Src\Application\Page\DTOs\UpdatePageDTO;
use Src\Domain\Page\Contracts\PageRepositoryInterface;
use Src\Domain\Page\Exceptions\PageNotFoundException;
use Src\Domain\Page\ValueObjects\PageId;
use Src\Domain\Page\ValueObjects\PageStatus;

final readonly class UpdatePageUseCase
{
    public function __construct(
        private PageRepositoryInterface $pageRepository
    ) {
    }

    public function execute(UpdatePageDTO $dto): PageResponseDTO
    {
        $pageId = PageId::fromString($dto->id);
        $page = $this->pageRepository->findById($pageId);

        if ($page === null) {
            throw PageNotFoundException::withId($dto->id);
        }

        // Update status if provided
        if ($dto->status !== null) {
            $status = PageStatus::fromString($dto->status);
            if ($status->isPublished()) {
                $page->publish();
            } else {
                $page->unpublish();
            }
        }

        // Update translations if provided
        if ($dto->translations !== null) {
            foreach ($dto->translations as $locale => $translationData) {
                $page->updateTranslation(
                    $locale,
                    $translationData['title'],
                    $translationData['content'],
                    $translationData['meta_description'] ?? null
                );
            }
        }

        // Save
        $this->pageRepository->save($page);

        return PageResponseDTO::fromDomain($page);
    }
}
