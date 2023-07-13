<?php

namespace bpmj\wpidea\ls_cache;

use bpmj\wpidea\events\actions\Action_Name;

class Ls_Cache_Cleaner
{
    private const WP_ACTIONS_TO_PURGE_ALL = [
        Action_Name::AFTER_SAVE_SETTINGS,
        Action_Name::AFTER_SAVE_VARIABLE_PRICES,
        Action_Name::AFTER_SAVE_DIGITAL_PRODUCT_DATA,
        Action_Name::AFTER_SAVE_SERVICE_DATA,
        Action_Name::LAYOUT_UPDATED,
        Action_Name::TEMPLATE_GROUP_SETTINGS_CHANGED,
        Action_Name::AFTER_PROMO_PRICES_UPDATE,
        Action_Name::PURCHASE_LIMIT_UPDATED
    ];

    /**
     * @var LS_Cache_Status_Checker
     */
    protected $status_checker;

    public function __construct( LS_Cache_Status_Checker $status_checker )
    {
        $this->status_checker = $status_checker;

        if (! $this->status_checker->is_caching_enabled()) {
            return;
        }

        $this->add_clear_actions();
    }

    public function ls_purge_all()
    {
        do_action( 'litespeed_purge_all' );
    }

    protected function add_clear_actions(): void
    {
        foreach ( self::WP_ACTIONS_TO_PURGE_ALL as $action ) {
            add_action( $action, [ $this, 'ls_purge_all' ] );
        }
    }
}
