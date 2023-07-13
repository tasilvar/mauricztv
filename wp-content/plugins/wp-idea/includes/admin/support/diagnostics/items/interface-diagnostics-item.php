<?php

namespace bpmj\wpidea\admin\support\diagnostics\items;

interface Interface_Diagnostics_Item {
    public function get_name();

    public function get_icon();

    public function get_status();

    public function check_status();

    public function get_fix_hint();

    public function get_current_value();

    public function get_solve_hint();

    public function get_solve_instructions();

    public function get_solve_icon();

}
