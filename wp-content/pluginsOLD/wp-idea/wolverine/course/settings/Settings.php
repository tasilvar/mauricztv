<?php
namespace bpmj\wpidea\wolverine\course\settings;

class Settings
{

    protected $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setStartDate($startDate)
    {
        update_post_meta($this->id, '_bpmj_eddpc_access_start', $startDate);
    }
}
