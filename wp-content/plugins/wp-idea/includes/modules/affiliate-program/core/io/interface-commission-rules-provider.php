<?php

namespace bpmj\wpidea\modules\affiliate_program\core\io;

use bpmj\wpidea\modules\affiliate_program\core\value_objects\Commission_Rate;

interface Interface_Commission_Rules_Provider
{
    public function get_rate(): Commission_Rate;
}