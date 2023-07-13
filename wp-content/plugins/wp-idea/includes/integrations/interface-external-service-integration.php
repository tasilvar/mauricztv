<?php

namespace bpmj\wpidea\integrations;

interface Interface_External_Service_Integration{
    public function check_connection(): bool;
    public function get_service_name();
}
