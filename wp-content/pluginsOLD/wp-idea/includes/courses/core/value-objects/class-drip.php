<?php

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\value_objects;

class Drip
{
    private Drip_Value $drip_value;
    private Drip_Unit $drip_unit;

    public function __construct(
        Drip_Value $drip_value,
        Drip_Unit $drip_unit
    ) {
        $this->drip_value = $drip_value;
        $this->drip_unit = $drip_unit;
    }

    public function get_drip_value(): Drip_Value
    {
        return $this->drip_value;
    }

    public function get_drip_unit(): Drip_Unit
    {
        return $this->drip_unit;
    }

}