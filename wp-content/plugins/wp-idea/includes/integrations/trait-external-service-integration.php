<?php

namespace bpmj\wpidea\integrations;

trait Trait_External_Service_Integration{
    public function get_service_name()
    {
        return static::SERVICE_NAME;
    }
}
