<?php

namespace bpmj\wpidea\admin\subscription\models;

use bpmj\wpidea\options\Interface_Options;

class Metadata implements Interface_Readable_Subscription_System_Data
{
    private const METADATA_OPTION_NAME = 'wpi_metadata';

    protected $options;

    public function __construct(Interface_Options $options) {
        $this->options = $options;
    }

    public function get(string $name)
    {
        $meta_data = $this->options->get(self::METADATA_OPTION_NAME);

        if (isset($meta_data[$name])){
            return $meta_data[$name];
        }

        return null;
    }

}
