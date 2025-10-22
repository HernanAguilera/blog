<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Application\DTOs\Post\CreatePostTranslationDTO;
use Blog\Application\DTOs\Post\PostTranslationResponseDTO;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\Post\ValueObjects\PostLocale;
use Blog\Domain\Post\ValueObjects\PostSlug;
use Blog\Domain\Post\Exceptions\PostNotFoundException;
use Blog\Domain\Post\Exceptions\TranslationAlreadyExistsException;
use Blog\Infrastructure\Persistence\Eloquent\Models\PostTranslationModel;

final class CreatePostTranslationUseCase
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    ) {}

    public function execute(CreatePostTranslationDTO $dto): PostTranslationResponseDTO
    {
        // 1. Verificar que el post existe
        $post = $this->postRepository->findById(new PostId($dto->postId));
        if (!$post) {
            throw PostNotFoundException::withId($dto->postId);
        }

        // 2. Validar locale
        $locale = PostLocale::fromString($dto->locale);

        // 3. Verificar que no existe traducción para este locale
        $existingTranslation = PostTranslationModel::where('post_id', $dto->postId)
            ->where('locale', $dto->locale)
            ->first();

        if ($existingTranslation) {
            throw TranslationAlreadyExistsException::forLocale($dto->locale);
        }

        // 4. Validar que el slug es único
        $slug = new PostSlug($dto->slug);
        $slugExists = $this->postRepository->existsBySlug($slug);
        if ($slugExists) {
            throw new \DomainException("Slug '{$dto->slug}' already exists");
        }

        // 5. Crear la traducción
        $translation = PostTranslationModel::create([
            'post_id' => $dto->postId,
            'locale' => $dto->locale,
            'title' => $dto->title,
            'slug' => $dto->slug,
            'content' => $dto->content,
            'excerpt' => $dto->excerpt,
            'meta_description' => $dto->metaDescription,
        ]);

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
