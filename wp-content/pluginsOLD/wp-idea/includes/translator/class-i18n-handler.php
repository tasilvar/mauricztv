<?php

namespace bpmj\wpidea\translator;

use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\shared\infrastructure\translator\Translations;

final class I18n_Handler implements Interface_Translator, Interface_Initiable
{
    private const PATH_TO_TRANSLATIONS = BPMJ_EDDCM_DIR . 'config/translations';

    private const FALLBACK_LOCALE = 'en_US';

    private string $locale;

    private Translations $translations;
    private array $translations_array;

    public function __construct(Translations $translations)
    {
        $this->translations = $translations;
    }

    public function translate($message): string
    {
        return $this->translations_array[$message] ?? $message;
    }

    public function translate_many(array $messages, ?string $section = null): array
    {
        $final_array = [];
        $prefix = ($section !== null)
            ? ($section . '.')
            : '';

        foreach ($messages as $message) {
            $final_array[$message] = $this->translate($prefix . $message);
        }

        return $final_array;
    }

    public function init(): void
    {
        $this->locale = $this->determine_locale();

        $this->translations_array = $this->get_translations_from_file();

        $external_translations = $this->get_external_translations();
        if ($external_translations) {
            $this->translations_array = array_merge($this->translations_array, $external_translations);
        }
    }

    private function determine_locale(): string
    {
        return determine_locale();
    }

    private function get_translations_from_file(): array
    {
        $file = $this->get_path_to_current_locale_translation_strings();

        if (file_exists($file)) {
            return include($file);
        }

        $fallback_file = $this->get_path_to_fallback_locale_translation_strings();

        if (file_exists($fallback_file)) {
            return include($fallback_file);
        }

        return [];
    }

    private function get_external_translations(): ?array
    {
        return $this->translations->get_for_locale($this->locale);
    }

    private function get_path_to_current_locale_translation_strings(): string
    {
        return self::PATH_TO_TRANSLATIONS . '/' . $this->locale . '.php';
    }

    private function get_path_to_fallback_locale_translation_strings(): string
    {
        return self::PATH_TO_TRANSLATIONS . '/' . self::FALLBACK_LOCALE . '.php';
    }
}
