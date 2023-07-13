<?php

namespace bpmj\wpidea\modules\meta_conversion_api\core\services;

class Unique_ID_Generator
{
    private const PREFIX = 'fb';

    public function get_unique_id(): string
    {
        return uniqid(self::PREFIX, true);
    }
}
