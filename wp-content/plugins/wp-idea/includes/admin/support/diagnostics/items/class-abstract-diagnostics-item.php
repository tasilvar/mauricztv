<?php

namespace bpmj\wpidea\admin\support\diagnostics\items;

use bpmj\wpidea\admin\support\Rules;
use ReflectionClass;

abstract class Abstract_Diagnostics_Item implements Interface_Diagnostics_Item {

    const STATUS_OK = 'ok';
    const STATUS_ERROR = 'error';

    protected $name = '';

    protected $fix_hint = '';

    /**
     * Solve hint used as rule title in the 'Support rules' section
     *
     * @var string
     */
    protected $solve_hint = null;

    /**
     * Solve instructions used as rule description in the 'Support rules' section
     *
     * @var string
     */
    protected $solve_instructions = null;

    /**
     * Solve icon used as rule icon in the 'Support rules' section
     *
     * @var string
     */
    protected $solve_icon = null;

    /**
     * @var Rules
     */
    protected $rules;

    protected $icons = array(
        self::STATUS_OK => 'yes-alt',
        self::STATUS_ERROR => 'dismiss'
    );

    /**
     * WARNING: Returns all class constants
     *
     * @return array
     */
    static function get_statuses() {
        $oClass = new ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }

    public function get_name(){
        return $this->name;
    }

    public function get_icon(){
        $status = $this->get_status();

        return array_key_exists( $status, $this->icons ) ? $this->icons[ $status ] : '';
    }

    public function get_status(){
        $status = $this->check_status();

        if( ! in_array( $status, self::get_statuses(), true ) ){
            $status = self::STATUS_ERROR;
        }

        return $status;
    }

    public function get_fix_hint(){
        return $this->get_status() != self::STATUS_OK ? $this->fix_hint : '';
    }

    public function get_solve_hint()
    {
        return $this->solve_hint;
    }

    public function get_solve_instructions()
    {
        return isset($this->solve_instructions) ? $this->solve_instructions : '';
    }

    public function get_solve_icon()
    {
        return $this->solve_icon;
    }
}
