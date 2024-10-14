<?php

namespace bpmj\wpidea\events;

class Event_Name
{
    public const DEBUG = 'wpi_debug_log';

    public const NEW_VALID_LICENSE_HAS_BEEN_ENTERED = 'wpi_new_valid_license_has_been_entered';

    public const USER_HAS_LOGGED_IN = 'wp_login';
    
    public const USER_LOGIN_FAILED = 'wp_login_failed';
    
    public const ORDER_CREATED = 'edd_insert_payment';
    
    public const ORDER_COMPLETED = 'edd_complete_purchase';
   
    public const INVOICE_HAS_BEEN_QUEUED_TO_BE_ISSUED = 'wpi_after_store_invoice';

    public const INVOICE_NOT_CREATED = 'wpi_after_invoice_created_error';

    public const INVOICE_CREATED_SUCCESSFULLY = 'wpi_after_invoice_created_success';

    public const ROUTE_OR_CONTROLLER_NOT_FOUND = 'wpi_route_or_controller_not_found';

    public const WP_MAIL_SEND_ATTEMPT_FAILED = 'wp_mail_failed';

    public const WP_MAIL_SEND_ATTEMPT = 'wp_mail';

    public const PROFILE_UPDATED = 'profile_update';

    public const USER_REGISTRED = 'user_register';

    public const WEBHOOK_INVALID_RESPONSE_RECEIVED = 'received_invalid_response_from_webhook';

    public const QUIZ_FINISHED = 'quiz_finished';

    public const CERTIFICATE_ISSUED = 'certificate_issued';

    public const STUDENT_ENROLLED_IN_COURSE = 'student_enrolled_in_course';

    public const COURSE_COMPLETED = 'course_completed';

    public const WEBHOOK_HAS_BEEN_CALLED = 'webhook_has_been_called';

    public const PAYMENT_UPDATED = 'edd_update_payment_status';

    public const RESOLVED_QUIZ_RESULT_UPDATED = 'resolved_quiz_result_updated';

    public const AUTHENTICATION_REQUESTED = 'wp_authenticate_user';

    public const PRODUCT_ADDED_TO_CART = 'edd_post_add_to_cart';

    public const CHECKOUT_INITIATED = 'initiate_checkout';

    public const PAGE_VIEWED = 'template_redirect';

    public const PRODUCT_REMOVED_FROM_CART = 'edd_post_remove_from_cart';

    public const PRODUCT_REMOVAL_FROM_CART_REQUESTED  =  'edd_pre_remove_from_cart';

    public const PRODUCT_ADDED_TO_CART_FROM_LINK = 'product_added_to_cart_from_link';

    public const SETTINGS_FIELD_VALUE_UPDATED = 'settings_field_value_update';

    public const COURSE_FIELD_VALUE_UPDATED = 'course_field_value_update';

    public const COURSE_VARIABLE_PRICES_UPDATED = 'course_variable_prices_update';

    public const COURSE_STRUCTURE_UPDATED = 'course_structure_update';

    public const SERVICES_FIELD_VALUE_UPDATED = 'services_field_value_update';

    public const DIGITAL_PRODUCT_FIELD_VALUE_UPDATED = 'digital_product_field_value_update';

    public const PHYSICAL_PRODUCT_FIELD_VALUE_UPDATED = 'physical_product_field_value_update';

    public const BUNDLE_FIELD_VALUE_UPDATED = 'bundle_field_value_update';

    public const BUNDLE_VARIABLE_PRICES_UPDATED = 'bundle_variable_prices_update';

    public const UPGRADER_PROCESS_COMPLETE = 'upgrader_process_complete';

    public const PLUGIN_DELETE = 'plugin_delete';

    public const PLUGIN_DELETED = 'plugin_deleted';

    public const PLUGIN_ACTIVATED = 'plugin_activated';

    public const PLUGIN_DEACTIVATED = 'plugin_deactivated';

    public const COURSE_DELETED = 'course_deleted';

    public const SERVICES_DELETED = 'services_deleted';

    public const DIGITAL_PRODUCT_DELETED = 'digital_product_deleted';

    public const PHYSICAL_PRODUCT_DELETED = 'physical_product_deleted';

    public const BUNDLE_DELETED = 'bundle_deleted';

    public const EXCEPTION_CAUGHT = 'exception_caught';
}
