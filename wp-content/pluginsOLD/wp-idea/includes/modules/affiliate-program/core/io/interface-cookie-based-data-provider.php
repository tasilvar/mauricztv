<?php

namespace bpmj\wpidea\modules\affiliate_program\core\io;

interface Interface_Cookie_Based_Data_Provider
{
    public function get_affiliate_id(): ?string;

    public function get_campaign_name(): ?string;
}