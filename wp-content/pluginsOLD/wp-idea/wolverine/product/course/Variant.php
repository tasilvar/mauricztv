<?php

namespace bpmj\wpidea\wolverine\product\course;

use bpmj\wpidea\wolverine\product\Variant as BaseVariant;

class Variant extends BaseVariant
{
    const ACCESS_DURATION_UNIT_MINUTES = 'minutes';
    const ACCESS_DURATION_UNIT_HOURS = 'hours';
    const ACCESS_DURATION_UNIT_DAYS = 'days';
    const ACCESS_DURATION_UNIT_MONTHS = 'months';
    const ACCESS_DURATION_UNIT_YEARS = 'years';

    protected $accessDuration;
    protected $accessDurationUnit;

    public function getAccessDuration()
    {
        return $this->accessDuration;
    }

    public function getAccessDurationUnit()
    {
        return $this->accessDurationUnit;
    }

    public function setAccessDuration($accessDuration)
    {
        $this->accessDuration = $accessDuration;

        return $this;
    }

    public function setAccessDurationUnit($accessDurationUnit)
    {
        $this->accessDurationUnit = $accessDurationUnit;

        return $this;
    }
}
