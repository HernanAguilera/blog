<?php

declare(strict_types=1);

namespace Src\Domain\Page\Contracts;

use Src\Domain\Page\Page;
use Src\Domain\Page\ValueObjects\PageId;

interface PageRepositoryInterface
{
    /**
     * Find a page by its ID
     *
     * @param PageId $id
     * @return Page|null
     */
    public function findById(PageId $id): ?Page;

    /**
     * Find a page by its slug (any status)
     *
     * @param string $slug
     * @return Page|null
     */
    public function findBySlug(string $slug): ?Page;

    /**
     * Find a published page by its slug
     *
     * @param string $slug
     * @return Page|null
     */
    public function findPublishedBySlug(string $slug): ?Page;

    /**
     * Get all pages
     *
     * @return array<Page>
     */
    public function getAll(): array;

    /**
     * Get only published pages
     *
     * @return array<Page>
     */
    public function getPublished(): array;

    /**
     * Save a page (create or update)
     *
     * @param Page $page
     * @return void
     */
    public function save(Page $page): void;

    /**
     * Delete a page
     *
     * @param PageId $id
     * @return void
     */
    public function delete(PageId $id): void;
}
