<?php

namespace bpmj\wpidea\helpers;

class Text_Formatter
{
    public function shorten_long_text(string $text, int $max_length): string
    {
        if (mb_strlen($text) <= $max_length) {
            return $text;
        }

        return trim(mb_substr($text, 0, $max_length)) . '...';
    }
}