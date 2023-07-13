<?php
namespace bpmj\wpidea\admin\support\diagnostics\items;

use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\modules\conflicting_plugins_detector\api\Conflicting_Plugins_API_Static_Helper;

class Conflicting_Plugins extends Abstract_Diagnostics_Item {
    private const VALUE_OK = 0;
    private array $conflicting_plugins_names;

    public function __construct() {
        $this->conflicting_plugins_names = Conflicting_Plugins_API_Static_Helper::get_active_conflicting_plugins_names();

        $this->name = Translator_Static_Helper::translate('conflicting_plugins_detector.diagnostics.name');
        $this->fix_hint = sprintf(Translator_Static_Helper::translate('conflicting_plugins_detector.diagnostics.fix_hint'), implode(
            ', ',
            $this->conflicting_plugins_names)
        );
    }

    public function get_current_value()
    {
        return count($this->conflicting_plugins_names);
    }

    public function check_status(){
        if( $this->get_current_value() === self::VALUE_OK ) return self::STATUS_OK;

        return self::STATUS_ERROR;
    }
}
