<?php

namespace bpmj\wpidea\helpers;

use bpmj\wpidea\translator\Interface_Translator;

class Translator_Static_Helper
{
    private static Interface_Translator $translator;

    public static function init(Interface_Translator $translator): void
    {
        self::$translator = $translator;
    }

    public static function translate(string $key): string
    {
        return self::$translator->translate($key);
    }
}