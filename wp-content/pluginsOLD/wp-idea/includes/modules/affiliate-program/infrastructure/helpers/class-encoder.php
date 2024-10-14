<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\helpers;

use bpmj\wpidea\modules\affiliate_program\core\helpers\Interface_Encoder;


class Encoder implements Interface_Encoder
{
    public function base64_decode(string $value): string
    {
        return base64_decode($value);
    }

    public function base64_encode(string $value): string
    {
        return base64_encode($value);
    }
}
