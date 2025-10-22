<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Application\DTOs\Post\UpdatePostTranslationDTO;
use Blog\Application\DTOs\Post\PostTranslationResponseDTO;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\Post\ValueObjects\PostLocale;
use Blog\Domain\Post\Exceptions\PostNotFoundException;
use Blog\Domain\Post\Exceptions\TranslationNotFoundException;
use Blog\Infrastructure\Persistence\Eloquent\Models\PostTranslationModel;

final class UpdatePostTranslationUseCase
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    ) {}

    public function execute(UpdatePostTranslationDTO $dto): PostTranslationResponseDTO
    {
        // 1. Verificar que el post existe
        $post = $this->postRepository->findById(new PostId($dto->postId));
        if (!$post) {
            throw PostNotFoundException::withId($dto->postId);
        }

        // 2. Validar locale
        $locale = PostLocale::fromString($dto->locale);

        // 3. Buscar traducción existente
        $translation = PostTranslationModel::where('post_id', $dto->postId)
            ->where('locale', $dto->locale)
            ->first();

        if (!$translation) {
            throw TranslationNotFoundException::forPostAndLocale($dto->postId, $dto->locale);
        }

        // 4. Validar slug único (excluyendo la traducción actual)
        if ($translation->slug !== $dto->slug) {
            $slugExists = PostTranslationModel::where('slug', $dto->slug)
                ->where('id', '!=', $translation->id)
                ->exists();

            if ($slugExists) {
                throw new \DomainException("Slug '{$dto->slug}' already exists");
            }
        }

        // 5. Actualizar traducción
        $translation->update([
            'title' => $dto->title,
            'slug' => $dto->slug,
            'content' => $dto->content,
            'excerpt' => $dto->excerpt,
            'meta_description' => $dto->metaDescription,
        ]);

        $translation->refresh();

        // 6. Retornar DTO
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
    }
}
