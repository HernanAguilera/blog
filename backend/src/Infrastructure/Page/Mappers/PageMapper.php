<?php

declare(strict_types=1);

namespace Src\Infrastructure\Page\Mappers;

use App\Models\PageModel;
use App\Models\PageTranslationModel;
use DateTimeImmutable;
use Src\Domain\Page\Page;
use Src\Domain\Page\ValueObjects\PageId;
use Src\Domain\Page\ValueObjects\PageSlug;
use Src\Domain\Page\ValueObjects\PageStatus;
use Src\Domain\Page\ValueObjects\PageTranslation;

final class PageMapper
{
    public function toDomain(PageModel $model): Page
    {
        $translations = [];
        foreach ($model->translations as $translationModel) {
            $translations[] = PageTranslation::create(
                $translationModel->locale,
                $translationModel->title,
                $translationModel->content,
                $translationModel->meta_description
            );
        }

        return new Page(
            PageId::fromString($model->id),
            PageSlug::fromString($model->slug),
            PageStatus::fromString($model->status),
            $translations,
            new DateTimeImmutable($model->created_at->toDateTimeString()),
            new DateTimeImmutable($model->updated_at->toDateTimeString())
        );
    }

    public function toEloquent(Page $page): PageModel
    {
        $model = PageModel::query()->find($page->id()->value());

        if ($model === null) {
            $model = new PageModel();
            $model->id = $page->id()->value();
        }

        $model->slug = $page->slug()->value();
        $model->status = $page->status()->value();

        return $model;
    }

    public function syncTranslations(Page $page, PageModel $model): void
    {
        // Delete existing translations
        $model->translations()->delete();

        // Create new translations
        foreach ($page->translations() as $translation) {
            $translationModel = new PageTranslationModel();
            $translationModel->page_id = $model->id;
            $translationModel->locale = $translation->locale();
            $translationModel->title = $translation->title()->value();
            $translationModel->content = $translation->content()->value();
            $translationModel->meta_description = $translation->metaDescription();
            $translationModel->save();
        }
    }
}
