<?php

declare(strict_types=1);

namespace App\src\Infrastructure\Services;

use App\src\Domain\Post\Services\HtmlSanitizerInterface;
use App\src\Domain\Post\ValueObjects\HtmlContent;
use HTMLPurifier;
use HTMLPurifier_Config;

final class HtmlSanitizerService implements HtmlSanitizerInterface
{
    private HTMLPurifier $purifier;
    private HTMLPurifier $previewPurifier;

    private array $config;

    public function __construct()
    {
        $this->config = config('editor.html_sanitizer', []);
        $this->purifier = $this->createPurifier();
        $this->previewPurifier = $this->createPreviewPurifier();
    }

    public function sanitize(string $html): HtmlContent
    {
        $cleanHtml = $this->purifier->purify($html);
        return new HtmlContent($cleanHtml);
    }

    public function sanitizeForPreview(string $html): HtmlContent
    {
        $cleanHtml = $this->previewPurifier->purify($html);
        return new HtmlContent($cleanHtml);
    }

    public function getAllowedTags(): array
    {
        return [
            'p', 'br', 'strong', 'em', 'u', 'i', 'b',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', 'ol', 'li',
            'blockquote', 'cite',
            'a', 'img',
            'pre', 'code',
            'table', 'thead', 'tbody', 'tr', 'td', 'th',
            'span', 'div'
        ];
    }

    public function isContentSafe(string $html): bool
    {
        $cleanHtml = $this->purifier->purify($html);
        return $cleanHtml === $html;
    }

    private function createPurifier(): HTMLPurifier
    {
        $config = HTMLPurifier_Config::createDefault();

        // Basic configuration
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');

        // Allowed elements and attributes
        $allowedTags = $this->config['allowed_tags'] ?? $this->getDefaultAllowedTags();
        $config->set('HTML.Allowed', implode(',', $allowedTags));

        // Allow specific classes for syntax highlighting
        $config->set('Attr.AllowedClasses', $this->getAllowedClasses());

        // Link configuration
        $config->set('HTML.TargetBlank', true);
        $config->set('Attr.AllowedFrameTargets', ['_blank']);

        // Image configuration
        $config->set('HTML.MaxImgLength', 1200);

        // Security
        $config->set('Output.FlashCompat', false);
        $config->set('HTML.FlashAllowFullScreen', false);

        // Cache
        $config->set('Cache.SerializerPath', storage_path('framework/cache'));

        return new HTMLPurifier($config);
    }

    private function createPreviewPurifier(): HTMLPurifier
    {
        $config = HTMLPurifier_Config::createDefault();

        // More restrictive configuration for previews
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');

        // Limited allowed elements for previews
        $config->set('HTML.Allowed', implode(',', [
            'p',
            'br',
            'strong',
            'em',
            'h1',
            'h2',
            'h3',
            'ul',
            'ol',
            'li',
            'blockquote'
        ]));

        // No external links in previews
        $config->set('URI.DisableExternalResources', true);
        $config->set('URI.DisableResources', true);

        // Cache
        $config->set('Cache.SerializerPath', storage_path('framework/cache'));

        return new HTMLPurifier($config);
    }

    private function getDefaultAllowedTags(): array
    {
        return [
            'p',
            'br',
            'strong',
            'em',
            'u',
            'i',
            'b',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'ul',
            'ol',
            'li',
            'blockquote',
            'cite',
            'a[href|title|target]',
            'img[src|alt|width|height|class]',
            'pre[class]',
            'code[class]',
            'table[class]',
            'thead',
            'tbody',
            'tr',
            'td[colspan|rowspan|class]',
            'th[colspan|rowspan|class]',
            'span[class]',
            'div[class]'
        ];
    }

    private function getAllowedClasses(): array
    {
        return array_merge(
            $this->config['allowed_classes'] ?? [],
            [
                // Syntax highlighting classes
                'hljs',
                'language-*',
                'lang-*',
                // Common code block classes
                'code-block',
                'highlight',
                // Table classes
                'table',
                'table-responsive',
                // Content classes
                'text-center',
                'text-left',
                'text-right',
                'image-responsive'
            ]
        );
    }
}