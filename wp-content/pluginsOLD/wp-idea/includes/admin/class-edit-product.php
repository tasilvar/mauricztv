<?php
/**
 *
 * The class responsible for edit product
 *
 */

namespace bpmj\wpidea\admin;

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

class Edit_Product
{

    function __construct()
    {
        add_filter('edd_receipt_show_download_files', array($this, 'receipt'), 9, 2); // sprawdzane przez weryfikacją czy jest to voucher
        add_filter('edd_email_receipt_download_title', array($this, 'email_receipt'), 10, 3);
        add_filter('edd_receipt_no_files_found_text', array($this, 'no_files_found_text'), 10, 2);

        add_action('admin_head', array($this, 'init'));
    }

    public function init()
    {

        if (!$this->is_editing_course()) {
            remove_action('edd_meta_box_fields', 'bpmj_eddpc_metabox_start_date');
            remove_action('edd_price_field', 'bpmj_eddpc_single_price_access_time', 20);
            remove_action('edd_download_price_table_head', 'bpmj_eddpc_access_time_header', 800);
            remove_action('edd_download_price_table_row', 'bpmj_eddpc_option_price_access_time', 800, 3);
        } else {
            remove_meta_box('edd_product_settings', 'download', 'side');
        }
    }

    /**
     * Prevent receipt from listing download files
     * @param $enabled default true
     * @param int $item_id ID of download
     * @return boolean
     */
    public function receipt($enabled, $item_id)
    {
        if ($this->is_service($item_id)) {
            return false;
        }

        return true;
    }

    /**
     * Modify email template to remove dash if the item is a service
     *
     * @param string $title
     * @param array $item
     * @param int $price_id
     *
     * @return string
     */
    public function email_receipt($title, $item, $price_id)
    {
        $item_id = $item['id'];
        if ($this->is_service($item_id)) {
            $title = get_the_title($item_id);

            if ($price_id > 0) {
                $title .= "&nbsp;" . edd_get_price_option_name($item_id, $price_id);
            }
        }

        return $title;
    }

    /**
     * Is service
     * @param int $item_id ID of download
     * @return boolean true if service, false otherwise
     * @return boolean
     */
    public function is_service($item_id)
    {
        global $edd_receipt_args;

        // get payment
        $payment = isset($edd_receipt_args['id']) ? get_post($edd_receipt_args['id']) : null;
        $cart = !empty($payment) ? edd_get_payment_meta_cart_details($payment->ID, true) : '';

        if ($cart) {
            foreach ($cart as $key => $item) {
                $price_id = edd_get_cart_item_price_id($item);

                $download_files = edd_get_download_files($item_id, $price_id);

                // if the service has a file attached, we still want to show it
                if ($download_files) {
                    return false;
                }
            }
        }

        // service by default :)
        return true;
    }

    /**
     * Remove "No downloadable files found." text for download services without a file
     *
     * @param string $text The text that should appear when no downloadable files are found
     * @param int $item_id The ID of the download
     *
     * @return string $text The text that should appear when no downloadable files are found
     */
    public function no_files_found_text($text, $item_id)
    {

        // Remove the text for download services without a file
        if ($this->is_service($item_id)) {
            $text = '';
        }

        return $text;
    }

    /**
     * Check if the screen currently displays post edit page for a course
     *
     * @return bool
     */
    private function is_editing_course()
    {
        global $post, $pagenow;

        return in_array($pagenow, array(
                'post-new.php',
                'post.php'
            )) && $post && 'courses' === get_post_type($post);
    }

}
