<?php

declare(strict_types=1);

namespace Blog\Domain\Post\ValueObjects;

use Blog\Domain\Post\Exceptions\InvalidPostLocaleException;
use Blog\Domain\Post\Exceptions\LocaleConfigurationException;

final class PostLocale
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function default(): self
    {
        $defaultLocale = self::getDefaultLocaleFromConfig();
        return new self($defaultLocale);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isDefault(): bool
    {
        $defaultLocale = self::getDefaultLocaleFromConfig();
        return $this->value === $defaultLocale;
    }

    public function equals(PostLocale $other): bool
    {
        return $this->value === $other->value;
    }

    public static function supportedLocales(): array
    {
        $supported = config('locales.supported');

        if ($supported === null) {
            throw LocaleConfigurationException::missingSupportedLocales();
        }

        if (!is_array($supported) || empty($supported)) {
            throw LocaleConfigurationException::invalidSupportedLocales();
        }

        return $supported;
    }

    public function getName(): string
    {
        $names = config('locales.names');

        if ($names === null) {
            throw LocaleConfigurationException::missingLocaleNames();
        }

        if (!is_array($names)) {
            throw LocaleConfigurationException::invalidLocaleNames();
        }

        return $names[$this->value] ?? $this->value;
    }

    public function getFlag(): string
    {
        $flags = config('locales.flags');

        // Flags son opcionales, pueden no estar definidos
        if ($flags === null || !is_array($flags)) {
            return '';
        }

        return $flags[$this->value] ?? '';
    }

    private static function getDefaultLocaleFromConfig(): string
    {
        $default = config('locales.default');

        if ($default === null) {
            throw LocaleConfigurationException::missingDefaultLocale();
        }

        if (!is_string($default) || empty($default)) {
            throw LocaleConfigurationException::invalidDefaultLocale();
        }

        return $default;
    }

    private function validate(): void
    {
        $supportedLocales = self::supportedLocales();

        if (!in_array($this->value, $supportedLocales, true)) {
            throw InvalidPostLocaleException::unsupportedLocale(
                $this->value,
                $supportedLocales
            );
        }
    }
}
