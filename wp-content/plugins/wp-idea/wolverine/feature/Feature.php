<?php
namespace bpmj\wpidea\wolverine\feature;

use bpmj\wpidea\infrastructure\system\Flag;

class Feature
{

    const INVOICE_RECEIVER = 'BPMJ_EDDCM_FEAT_INVOICE_RECEIVER';

    public static function isEnabled($flag)
    {
        return Flag::is_enabled($flag);
    }
}
