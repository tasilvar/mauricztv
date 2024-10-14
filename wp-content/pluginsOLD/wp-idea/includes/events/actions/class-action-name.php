<?php

namespace bpmj\wpidea\events\actions;

class Action_Name
{
    public const ADMIN_INIT = 'admin_init';

    public const INIT = 'init';

    public const AFTER_SAVE_DIGITAL_PRODUCT_DATA = 'after_save_digital_product_data';

    public const AFTER_SAVE_SERVICE_DATA = 'after_save_service_data';

    public const PROCESS_VERIFIED_DOWNLOAD = 'edd_process_verified_download';

    public const AFTER_SAVE_SETTINGS = 'wpi_after_save_settings';

    public const AFTER_SAVE_VARIABLE_PRICES = 'wpi_after_save_variable_prices';

    public const LAYOUT_UPDATED = 'save_post_wpi_page_templates';

    public const TEMPLATE_GROUP_SETTINGS_CHANGED = 'bpmj_eddcm_layout_template_settings_save';

    public const FILES_DELETED = 'delete_attachment';

    public const AMIN_PRINT_FOOTER_SCRIPTS = 'admin_print_footer_scripts';

    public const SHOW_USER_PROFILE = 'show_user_profile';

    public const EDIT_USER_PROFILE = 'edit_user_profile';

    public const TEMPLATE_REDIRECT = 'template_redirect';

    public const ENQUEUE_BLOCK_EDITOR_ASSETS = 'enqueue_block_editor_assets';

    public const HEAD = 'wp_head';

    public const FOOTER = 'wp_footer';

    public const AFTER_BODY_OPEN_TAG = 'bpmj_eddc_after_body_open_tag';

    public const PRINT_FOOTER_SCRIPT = 'wp_print_footer_scripts';

    public const PURCHASE_FORM_AFTER_CC_FORM = 'edd_purchase_form_after_cc_form';

    public const CHECKOUT_ERROR_CHECKS = 'edd_checkout_error_checks';

    public const ENQUEUE_SCRIPTS = 'wp_enqueue_scripts';

    public const DISPLAY_BUTTON_GET_DATA_FROM_GUS = 'display_button_get_data_from_gus';
    
    public const AFTER_PROMO_PRICES_UPDATE = 'after_promo_prices_update';

    public const PURCHASE_LIMIT_UPDATED = 'purchase_limit_updated';

	public const POST_UPDATED = 'post_updated';
}