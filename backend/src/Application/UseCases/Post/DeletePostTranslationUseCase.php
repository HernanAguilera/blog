<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Post;

use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\Post\ValueObjects\PostLocale;
use Blog\Domain\Post\Exceptions\TranslationNotFoundException;
use Blog\Infrastructure\Persistence\Eloquent\Models\PostTranslationModel;

final class DeletePostTranslationUseCase
{
    public function execute(string $postId, string $locale): void
    {
        // 1. Validar locale
        $localeVO = PostLocale::fromString($locale);

        // 2. Prevenir eliminación del idioma por defecto
        if ($localeVO->isDefault()) {
            throw new \DomainException(
                "Cannot delete default locale translation. Delete the entire post instead."
            );
        }

        // 3. Buscar traducción
        $translation = PostTranslationModel::where('post_id', $postId)
            ->where('locale', $locale)
            ->first();

        if (!$translation) {
            throw TranslationNotFoundException::forPostAndLocale($postId, $locale);
        }

        // 4. Eliminar traducción
        $translation->delete();
    }
}
