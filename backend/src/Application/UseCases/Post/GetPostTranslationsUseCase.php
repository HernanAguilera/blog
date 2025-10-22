<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Application\DTOs\Post\PostTranslationResponseDTO;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\Post\Exceptions\PostNotFoundException;
use Blog\Infrastructure\Persistence\Eloquent\Models\PostTranslationModel;

final class GetPostTranslationsUseCase
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    ) {}

    /**
     * @return PostTranslationResponseDTO[]
     */
    public function execute(string $postId): array
    {
        // 1. Verificar que el post existe
        $post = $this->postRepository->findById(new PostId($postId));
        if (!$post) {
            throw PostNotFoundException::withId($postId);
        }

        // 2. Obtener todas las traducciones
        $translations = PostTranslationModel::where('post_id', $postId)
            ->orderBy('locale')
            ->get();

        // 3. Mapear a DTOs
        return $translations->map(function ($translation) {
            return new PostTranslationResponseDTO(
                postId: $translation->post_id,
                locale: $translation->locale,
                title: $translation->title,
                slug: $translation->slug,
                content: $translation->content,
                excerpt: $translation->excerpt,
                metaDescription: $translation->meta_description,
                createdAt: $translation->created_at->toISOString(),
                updatedAt: $translation->updated_at->toISOString()
            );
        })->toArray();
    }
}
