<?php

namespace bpmj\wpidea\shared\infrastructure\translator;

class Translations
{
    private const FALLBACK_LOCALE = 'en_US';
    private array $translations;

    public function add(array $translation, string $locale): void
    {
        if(!isset($this->translations[$locale])) {
            $this->translations[$locale] = [];
        }
        $this->translations[$locale] += $translation;
    }

    public function get_for_locale(string $locale): ?array
    {
        return $this->translations[$locale] ?? $this->translations[self::FALLBACK_LOCALE] ?? null;
    }
}