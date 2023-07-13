<?php

namespace bpmj\wpidea\events\filters;

class Filter_Name
{
    public const HANDLE_UPLOAD_PREFILTER = 'wp_handle_upload_prefilter';

    public const FILE_DOWNLOAD_METHOD = 'edd_file_download_method';
    public const FILE_DOWNLOAD_METHOD_REDIRECT = 'edd_file_download_method_redirect';

    public const INVOICE_FLAT_RATE_ENABLED = 'wpi_invoice_flat_rate_enabled';

    public const FILES_UPLOADED = 'wp_handle_upload';

    public const DEFAULT_APP_LOGO_URL = 'bpmj_eddcm_default_logo_url';

    public const EMAIL_LOGO_URL = 'email_logo_url';

    public const AJAX_DISCOUNT_RESPONSE = 'edd_ajax_discount_response';

    public const BREADCRUMBS_PARENTS_IDS = 'bpmj_eddcm_breadcrumbs_parents_ids';

    public const PAYMENT_META = 'edd_payment_meta';

    public const UPGRADER_PRE_INSTALL = 'upgrader_pre_install';
}