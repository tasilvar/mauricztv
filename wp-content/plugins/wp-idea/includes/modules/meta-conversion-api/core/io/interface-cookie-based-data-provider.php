<?php

namespace bpmj\wpidea\modules\meta_conversion_api\core\io;

interface Interface_Cookie_Based_Data_Provider
{
    public function get_fbp(): ?string;

    public function get_fbc(): ?string;
}