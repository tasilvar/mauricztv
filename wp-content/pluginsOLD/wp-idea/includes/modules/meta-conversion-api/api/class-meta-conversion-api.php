<?php

namespace bpmj\wpidea\modules\meta_conversion_api\api;

use bpmj\wpidea\modules\meta_conversion_api\core\services\Unique_ID_Generator;

class Meta_Conversion_API
{
    private Unique_ID_Generator $unique_id_generator;
    private string $unique_id;

    public function __construct(Unique_ID_Generator $unique_id_generator)
    {
        $this->unique_id_generator = $unique_id_generator;
    }

    public function get_unique_id_for_request(): string
    {
        if (!isset($this->unique_id)) {
            $this->unique_id = $this->unique_id_generator->get_unique_id();
        }

        return $this->unique_id;
    }
}