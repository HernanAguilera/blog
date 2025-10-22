<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Domain\Post\ValueObjects\PostLocale;
use Blog\Domain\Post\ValueObjects\PostStatus;
use Blog\Infrastructure\Persistence\Eloquent\Models\PostModel;
use Blog\Infrastructure\Persistence\Eloquent\Mappers\PostMapper;

final class GetPostsByLocaleUseCase
{
    /**
     * Get published posts filtered by locale with pagination
     *
     * @return array{posts: array, total: int, page: int, perPage: int}
     */
    public function execute(
        string $locale,
        int $page = 1,
        int $perPage = 15
    ): array {
        // 1. Validar locale
        $localeVO = PostLocale::fromString($locale);

        // 2. Query con filtro de locale y publicados
        $query = PostModel::query()
            ->where('status', PostStatus::PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereHas('translations', function ($q) use ($locale) {
                $q->where('locale', $locale);
            })
            ->with(['translations' => function ($q) use ($locale) {
                $q->where('locale', $locale);
            }])
            ->orderBy('published_at', 'desc');

        // 3. Paginar
        $total = $query->count();
        $posts = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // 4. Mapear a dominio
        $domainPosts = $posts->map(fn($model) => PostMapper::toDomain($model))->toArray();

        return [
            'posts' => $domainPosts,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }
}
