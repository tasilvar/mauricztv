<?php
namespace bpmj\wpidea\wolverine\product;

class Discount
{
    const TYPE_FLAT = 'flat';
    const TYPE_PERCENTAGE = 'percentage';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_EXPIRED = 'expired';

    protected $name;
    protected $code;
    protected $value;
    protected $type;
    protected $status;

    public function save()
    {
        $details = [
            'name' => $this->name ?? $this->code,
            'code' => $this->code,
            'amount' => $this->value,
            'type' => $this->type,
            'status' => $this->status ?? self::STATUS_ACTIVE
        ];
        edd_store_discount($details);
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public static function hasActiveCodes()
    {
        return edd_has_active_discounts();
    }
}