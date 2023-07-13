<?php

namespace bpmj\wpidea\modules\affiliate_program\core\helpers;

interface Interface_Encoder
{
    public function base64_decode(string $value): string;

    public function base64_encode(string $value): string;
}
