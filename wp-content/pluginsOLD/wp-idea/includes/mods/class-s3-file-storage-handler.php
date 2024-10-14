<?php

namespace bpmj\wpidea\mods;

use bpmj\wpidea\admin\subscription\models\Interface_Readable_Subscription_System_Data;
use bpmj\wpidea\app\digital_products\Digital_Products_App_Service;
use bpmj\wpidea\controllers\S3_Controller;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\filters\Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\model\Product_ID;

class S3_File_Storage_Handler implements Interface_Initiable
{
    private Interface_Filters $filters;
    private Interface_Actions $actions;
    private Interface_Readable_Subscription_System_Data $subscription_system_data;
    private Digital_Products_App_Service $digital_products_app_service;
    private Interface_Url_Generator $url_generator;

    private ?int $instance_id;

    public function __construct(
        Interface_Filters $filters,
        Interface_Actions $actions,
        Interface_Readable_Subscription_System_Data $subscription_system_data,
        Digital_Products_App_Service $digital_products_app_service,
        Interface_Url_Generator $url_generator
    ) {
        $this->filters = $filters;
        $this->actions = $actions;
        $this->subscription_system_data = $subscription_system_data;
        $this->digital_products_app_service = $digital_products_app_service;
        $this->url_generator = $url_generator;
    }

    public function init(): void
    {
        if (!$this->should_run()) {
            return;
        }

        $this->instance_id = defined(
            'S3_UPLOADS_DBG_INSTANCE_ID'
        ) ? S3_UPLOADS_DBG_INSTANCE_ID : $this->subscription_system_data->get('id');

        if (!$this->instance_id) {
            return;
        }

        $this->register_hooks();

        $this->define('UPLOADS', 'wp-content/' . $this->instance_id);

        require_once BPMJ_EDDCM_DIR . 'vendor/upsell/s3-uploads/s3-uploads.php';
    }

    private function register_hooks(): void
    {
        if (defined('S3_UPLOADS_FILTER_LOCAL_CONTENT')) {
            $this->filters->add('wp_get_attachment_url', [$this, 'filter_local_attachment_url']);
            $this->filters->add('get_attached_file', [$this, 'filter_local_attachment_url']);
            $this->filters->add('wp_calculate_image_srcset', [$this, 'filter_local_image_srcset']);
        }

        if (defined('S3_UPLOADS_ENDPOINT')) {
            $this->filters->add('s3_uploads_s3_client_params', [$this, 'use_custom_endpoint']);
        }

        $this->filters->add('wpi_encrypt_anchors_pattern', [$this, 'filter_cdn_anchors_in_content']);

        $this->filters->add('certificate_replace_params', [$this, 'filter_certificate_replace_params']);

        $this->actions->add(
            'wpi_process_encrypted_url_before_final_redirection',
            [$this, 'force_file_download_when_masked_link_is_processed'],
            10,
            2
        );

        $this->actions->add(
            Action_Name::PROCESS_VERIFIED_DOWNLOAD,
            [$this, 'filter_download_method']
        );
    }

    public function filter_local_attachment_url(string $url): string
    {
        return $this->maybe_parse_to_local_url($url);
    }

    public function filter_local_image_srcset(array $sources): array
    {
        foreach ($sources as $key => $source) {
            $sources[$key]['url'] = $this->maybe_parse_to_local_url($source['url']);
        }

        return $sources;
    }

    public function filter_certificate_replace_params(string $content): string
    {
        $image_proxy_url = $this->url_generator->generate(
                S3_Controller::class,
                'certificate_image',
                [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]
            ) . '&img=';
        $content = preg_replace(
            '/background: url\(' . str_replace('/', '\/', S3_UPLOADS_BUCKET_URL) . '\/' . $this->instance_id . '\/(.*?)\)/',
            'background: url(' . $image_proxy_url . '$1)',
            $content
        );

        return $content;
    }

    public function get_remote_url(): string
    {
        return S3_UPLOADS_BUCKET_URL . '/' . $this->instance_id;
    }

    private function maybe_parse_to_local_url(string $url): string
    {
        $url_analyzed = preg_split('/(20[0-9][0-9])\\/([0-9][0-9])\\//', $url, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($url_analyzed) > 1) {
            $url_analyzed[0] = get_site_url() . '/wp-content/uploads';
            $url = implode('/', $url_analyzed);
        }

        return $url;
    }

    public function use_custom_endpoint(array $params): array
    {
        $params['endpoint'] = S3_UPLOADS_ENDPOINT;
        $params['use_path_style_endpoint'] = true;
        $params['debug'] = false;
        return $params;
    }

    public function force_file_download_when_masked_link_is_processed($url, $disposition): void
    {
        $file = str_replace(['%3A', '%2F'], [':', '/'], rawurlencode($url));
        if (empty($file)) {
            return;
        }
        $type = wp_check_filetype(basename($url), wp_get_mime_types());
        header('Content-Type: ' . (!empty($type['type']) ? $type['type'] : 'application/octet-stream'));
        header('Content-Disposition: ' . $disposition . '; filename="' . basename($url) . '"');
        readfile($file);
        die;
    }

    public function filter_cdn_anchors_in_content(string $pattern): string
    {
        $cdn_url = str_replace('https:/', '', S3_UPLOADS_BUCKET_URL . '/' . $this->instance_id);

        return '#href=("[^"]+?/wp-content/uploads/[^"]+"|\'[^\']+?/wp-content/uploads/[^\']+\'|"[^"]+?'
            . $cdn_url . '[^"]+"|\'[^\']+?' . $cdn_url . '[^\']+\')#';
    }

    public function filter_download_method($download_id): void
    {
        $is_digital_product_download = $this->digital_products_app_service->find_digital_product_by_offer_id(
            new Product_ID((int)$download_id)
        );

        if (!$is_digital_product_download) {
            return;
        }

        $this->filters->add(Filter_Name::FILE_DOWNLOAD_METHOD, function () {
            return 'redirect';
        }, 99);

        $this->filters->add(Filter_Name::FILE_DOWNLOAD_METHOD_REDIRECT, function () {
            return false;
        });
    }

    private function should_run(): bool
    {
        return defined('S3_UPLOADS_BUCKET') &&
            defined('S3_UPLOADS_REGION') &&
            defined('S3_UPLOADS_KEY') &&
            defined('S3_UPLOADS_SECRET') &&
            defined('S3_UPLOADS_USE_INSTANCE_PROFILE') &&
            defined('S3_UPLOADS_BUCKET_URL');
    }

    private function define(string $name, string $value): void
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }
}