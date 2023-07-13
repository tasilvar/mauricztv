<?php

/**
 *
 * The class responsible for certificates
 *
 */

namespace bpmj\wpidea\certificates;

use bpmj\wpidea\caps\Access_Filters;
use bpmj\wpidea\controllers\Certificate_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use WP_Query;

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

class Certificates
{
    const CERTIFICATES_SLUG = 'wpi_certificates';

    private $url_generator;

    public function __construct(Interface_Url_Generator $url_generator)
    {
        $this->url_generator = $url_generator;
    }

    /**
     * Get id of post containing pdf content of the certificate
     *
     * @param integer $certificate_id
     * @return integer or null
     */
    public function get_certificate_post_id($certificate_id): ?int
    {
        $post_id = null;

        if (Access_Filters::cannot_see_sensitive_data()) {
            $query = new WP_Query(array(
                'p' => sanitize_text_field($certificate_id),
                'post_type' => 'certificates',
                'meta_query' => array(
                    array(
                        'key' => 'user_id',
                        'value' => get_current_user_id()
                    ),
                ),
            ));

            $post_id = $query->have_posts() ? (int)$query->post->ID : null;
        } else {
            $post = get_post(sanitize_text_field($certificate_id));
            $post_id = !is_null($post) ? (int)$post->ID : null;
        }

        return $post_id;
    }

    /**
     * Get pdf content
     *
     * @param integer $post_id
     * @return mixed
     */
    public function get_pdf_content($post_id)
    {
        return get_post_meta($post_id, 'pdf_content', true);
    }

    /**
     * Get pdf orientation from WPI settings
     *
     * @return string
     */
    public function get_pdf_orientation()
    {
        $orientation = 'portrait';

        $wpidea_settings = get_option('wp_idea', array());
        if (isset($wpidea_settings['certificate_orientation']))
            $orientation = $wpidea_settings['certificate_orientation'];

        return $orientation;
    }

    public function get_download_url_for_current_user($certificate_id): string
    {
        $action_name = 'download_old';

        if(Certificate_Template::check_if_new_version_of_certificate_templates_is_enabled()){
            $action_name = 'download';
        }

        return $this->url_generator->generate(Certificate_Controller::class,
                                              $action_name,
                                              [
                                                  Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
                                                  'id' => $certificate_id
                                              ]);
    }
}
