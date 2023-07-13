<?php

namespace bpmj\wpidea\ls_cache;

class LS_Cache_Status_Checker
{
    public function is_caching_enabled(): bool
    {
        $ls_conf = apply_filters('litespeed_conf', 'cache');
        if ($ls_conf === 'cache') {
            return false;
        }

        return true;
    }
}
