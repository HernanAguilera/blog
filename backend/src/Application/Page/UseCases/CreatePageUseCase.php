<?php

declare(strict_types=1);

namespace Src\Application\Page\UseCases;

use Src\Application\Page\DTOs\CreatePageDTO;
use Src\Application\Page\DTOs\PageResponseDTO;
use Src\Domain\Page\Contracts\PageRepositoryInterface;
use Src\Domain\Page\Exceptions\PageAlreadyExistsException;
use Src\Domain\Page\Page;
use Src\Domain\Page\ValueObjects\PageSlug;
use Src\Domain\Page\ValueObjects\PageStatus;
use Src\Domain\Page\ValueObjects\PageTranslation;

final readonly class CreatePageUseCase
{
    public function __construct(
        private PageRepositoryInterface $pageRepository
    ) {
    }

    public function execute(CreatePageDTO $dto): PageResponseDTO
    {
        $slug = PageSlug::fromString($dto->slug);

        // Check if page with this slug already exists
        $existingPage = $this->pageRepository->findBySlug($slug->value());
        if ($existingPage !== null) {
            throw PageAlreadyExistsException::withSlug($slug->value());
        }

        // Create translations
        $translations = [];
        foreach ($dto->translations as $locale => $translationData) {
            $translations[] = PageTranslation::create(
                $locale,
                $translationData['title'],
                $translationData['content'],
                $translationData['meta_description'] ?? null
            );
        }

        // Create page
        $page = Page::create(
            $slug,
            PageStatus::fromString($dto->status),
            $translations
        );

        // Save
        $this->pageRepository->save($page);

        return PageResponseDTO::fromDomain($page);
    }
}
