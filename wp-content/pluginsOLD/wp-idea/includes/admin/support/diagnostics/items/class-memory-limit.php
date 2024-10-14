<?php
namespace bpmj\wpidea\admin\support\diagnostics\items;

use bpmj\wpidea\admin\support\diagnostics\items\Abstract_Diagnostics_Item;
use bpmj\wpidea\translator\Interface_Translator;

class Memory_Limit extends Abstract_Diagnostics_Item {

    private const MIN_VALUE = 256;

    public function __construct(Interface_Translator $translator) { 
        $this->name = $translator->translate('help.diagnostics.memory_limit');
        $this->fix_hint = $translator->translate('help.diagnostics.memory_limit.fix_hint');
        $this->solve_hint   = $translator->translate('help.diagnostics.memory_limit.solve_hint');
    }

    public function get_current_value()
    {
        return ini_get('memory_limit');
    }

    public function check_status(){
        if( $this->get_current_value() >= self::MIN_VALUE ) return self::STATUS_OK;

        return self::STATUS_ERROR;
    }
}
