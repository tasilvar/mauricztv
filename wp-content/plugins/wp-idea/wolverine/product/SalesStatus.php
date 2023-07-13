<?php
namespace bpmj\wpidea\wolverine\product;

class SalesStatus
{
    protected $isDisabled;

    protected $reason;

    protected $reasonDescription;

    public function getIsDisabled()
    {
        return $this->isDisabled;
    }

    public function setIsDisabled($isDisabled)
    {
        $this->isDisabled = $isDisabled;

        return $this;
    }

    public function getReason()
    {
        return $this->reason;
    }

    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    public function getReasonDescription()
    {
        return $this->reasonDescription;
    }

    public function setReasonDescription($reasonDescription)
    {
        $this->reasonDescription = $reasonDescription;

        return $this;
    }
}