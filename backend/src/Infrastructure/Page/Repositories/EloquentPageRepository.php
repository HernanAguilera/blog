<?php

declare(strict_types=1);

namespace Src\Infrastructure\Page\Repositories;

use App\Models\PageModel;
use Src\Domain\Page\Contracts\PageRepositoryInterface;
use Src\Domain\Page\Page;
use Src\Domain\Page\ValueObjects\PageId;
use Src\Infrastructure\Page\Mappers\PageMapper;

final readonly class EloquentPageRepository implements PageRepositoryInterface
{
    public function __construct(
        private PageMapper $mapper
    ) {
    }

    public function findById(PageId $id): ?Page
    {
        $model = PageModel::query()
            ->with('translations')
            ->find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function findBySlug(string $slug): ?Page
    {
        $model = PageModel::query()
            ->with('translations')
            ->where('slug', $slug)
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function findPublishedBySlug(string $slug): ?Page
    {
        $model = PageModel::query()
            ->with('translations')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function getAll(): array
    {
        $models = PageModel::query()
            ->with('translations')
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn(PageModel $model) => $this->mapper->toDomain($model))->all();
    }

    public function getPublished(): array
    {
        $models = PageModel::query()
            ->with('translations')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn(PageModel $model) => $this->mapper->toDomain($model))->all();
    }

    public function save(Page $page): void
    {
        $model = $this->mapper->toEloquent($page);
        $model->save();

        // Sync translations
        $this->mapper->syncTranslations($page, $model);

        // Reload to update the model with translations
        $model->load('translations');
    }

    public function delete(PageId $id): void
    {
        PageModel::query()
            ->where('id', $id->value())
            ->delete();
    }
}
