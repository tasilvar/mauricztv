<?php

namespace bpmj\wpidea;

use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\settings\LMS_Settings;

class Analytics
{
    public const PIXEL_FB_ID_SETTING_NAME = 'pixel_fb_id';
    public const GA_ID_SETTING_NAME = 'ga_id';
    public const GTM_ID_SETTING_NAME = 'gtm_id';
    public const BEFORE_END_HEAD_SETTING_NAME = 'before_end_head';
    public const AFTER_BEGIN_BODY_SETTING_NAME = 'after_begin_body';
    public const BEFORE_END_BODY_SETTING_NAME = 'before_end_body';

    private Interface_Actions $actions;
    private Interface_Settings $settings;
    private Cart_API $cart_api;

    public function __construct(
            Interface_Actions $actions,
            Interface_Settings $settings,
            Cart_API $cart_api
    ) {
        $this->actions = $actions;
        $this->settings = $settings;
        $this->cart_api = $cart_api;

        $this->add_actions();
    }

    public function add_actions(): void
    {
        $this->actions->add(Action_Name::HEAD, [$this, 'before_head_close_tag_scripts'], 1000);
        $this->actions->add(Action_Name::AFTER_BODY_OPEN_TAG, [$this, 'after_body_open_tag_scripts'], 1000);
        $this->actions->add(Action_Name::PRINT_FOOTER_SCRIPT, [$this, 'before_body_close_tag_scripts']);
    }

    public function before_head_close_tag_scripts(): void
    {
        echo $this->get_head_scripts();
    }

    public function after_body_open_tag_scripts(): void
    {
        echo $this->get_after_body_open_tag_scripts();
    }

    public function before_body_close_tag_scripts(): void
    {
        echo $this->get_before_body_close_tag_scripts();
    }

    private function get_head_scripts(): string
    {
        $string = '';

        $ga_id = $this->settings->get(self::GA_ID_SETTING_NAME);
        if (!empty($ga_id)) {
            $string .= $this->get_ga($ga_id);
        }

        $gtm_id = $this->settings->get(self::GTM_ID_SETTING_NAME);
        if (!empty($gtm_id)) {
            $string .= $this->get_gtm($gtm_id);
        }

        $before_end_head = $this->settings->get(self::BEFORE_END_HEAD_SETTING_NAME);
        if (!empty($before_end_head)) {
            $string .= htmlspecialchars_decode($before_end_head);
        }

        return $string;
    }

    private function get_after_body_open_tag_scripts(): string
    {
        $string = '';

        $gtm_id = $this->settings->get(self::GTM_ID_SETTING_NAME);
        if (!empty($gtm_id)) {
            $string .= $this->get_gtm_noscript($gtm_id);
        }

        $after_begin_body = $this->settings->get(self::AFTER_BEGIN_BODY_SETTING_NAME);
        if (!empty($after_begin_body)) {
            $string .= htmlspecialchars_decode($after_begin_body);
        }

        return $string;
    }

    private function get_before_body_close_tag_scripts(): string
    {
        $string = '';

        $before_end_body = $this->settings->get(self::BEFORE_END_BODY_SETTING_NAME);
        if (!empty($before_end_body)) {
            $string .= htmlspecialchars_decode($before_end_body);
        }

        return $string;
    }

    private function get_ga($id): string
    {
        ob_start();
        ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php
        echo $id; ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());
            gtag('config', '<?php echo $id; ?>');
        </script>
        <?php
        return ob_get_clean();
    }

    private function get_payment_id_on_success_page(): ?int
    {
        global $edd_receipt_args;

        $session = edd_get_purchase_session();
        if (isset($_GET['payment_key'])) {
            $payment_key = urldecode($_GET['payment_key']);
        } else {
            if ($session) {
                $payment_key = $session['purchase_key'];
            } elseif ($edd_receipt_args['payment_key']) {
                $payment_key = $edd_receipt_args['payment_key'];
            }
        }

        if (empty($payment_key)) {
            return null;
        }

        return edd_get_purchase_id_by_key($payment_key);
    }

    private function add_data_layer_on_success_page(): void
    {
        if ($this->cart_api->is_success_page()) {
            $payment_id = $this->get_payment_id_on_success_page();

            if ($payment_id) {
                $payment = edd_get_payment($payment_id);
                View_Hooks::run(View_Hooks::RENDER_HEAD_ELEMENTS_IN_PURCHASE, $payment);
            }
        }

        if ($this->cart_api->is_checkout()) {
            View_Hooks::run(View_Hooks::RENDER_HEAD_ELEMENTS_IN_CHECKOUT, edd_get_cart_contents());
        }
    }

    private function get_gtm($id): string
    {
        ob_start();
        $this->add_data_layer_on_success_page();
        ?>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','<?php echo $id; ?>');</script>
        <!-- End Google Tag Manager -->
        <?php
        return ob_get_clean();
    }

    private function get_gtm_noscript($id): string
    {
        ob_start();
        ?>
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-<?php
            echo $id; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        <?php
        return ob_get_clean();
    }

    public static function is_gtm_enabled(): bool
    {
        if (!empty(LMS_Settings::get_option(self::GTM_ID_SETTING_NAME))) {
            return true;
        }
        return false;
    }
}
