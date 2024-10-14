<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\Caps;
use bpmj\wpidea\modules\affiliate_program\api\Affiliate_Program_API;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_Info;
use bpmj\wpidea\modules\app_view\api\App_View_API_Static_Helper;
use bpmj\wpidea\modules\opinions\api\Opinions_API;
use bpmj\wpidea\modules\opinions\core\collections\Product_To_Rate_Collection;
use bpmj\wpidea\sales\product\api\Product_API;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\settings\Interface_Settings_Aware;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\translator\Interface_Translator_Aware;
use bpmj\wpidea\user\api\User_API;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\view\Interface_View_Provider_Aware;

class User_Account_Form_Block extends Block implements Interface_View_Provider_Aware, Interface_Translator_Aware, Interface_Settings_Aware
{
    public const BLOCK_NAME = 'wpi/user-account-form';

    private ?Interface_View_Provider $view_provider = null;
    private ?Interface_Translator $translator = null;
    private ?Interface_Settings $settings = null;

    public function __construct()
    {
        parent::__construct();

        $this->title = __('User Account Form', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        if (!$this->view_provider || !$this->translator || !$this->settings) {
            return '';
        }

        $app_view = App_View_API_Static_Helper::is_active();

        if($app_view) {
            return $this->view_provider->get($this->get_template_path_base() . '/user-account/app-view-content', [
                'translator' => $this->translator
            ]);
        }

        $partner_info = $this->get_affiliate_program_api()->get_partner_info();

        return $this->view_provider->get($this->get_template_path_base() . '/user-account/content', [
            'translator' => $this->translator,
            'is_logged_in' => is_user_logged_in(),
            'settings' => $this->settings,
            'partner_info' => $this->get_affiliate_program_api()->get_partner_info(),
            'partner_commissions' => $this->get_affiliate_program_api()->get_partner_commissions(),
            'disabled_partner_tab' => $this->disabled_partner_tab(),
            'external_landing_links' => $this->external_landing_links_to_array($partner_info),
            'opinions_enabled' => $this->get_opinions_api()->is_enabled(),
            'products_user_can_rate' => $this->get_products_user_can_rate(),
            'user_name' => $this->get_user_name(),
            'opinions_rules_url' => $this->settings->get(Settings_Const::OPINIONS_RULES),
        ]);
    }

    public function set_view_provider(Interface_View_Provider $view_provider): void
    {
        $this->view_provider = $view_provider;
    }

    public function set_translator(Interface_Translator $translator): void
    {
        $this->translator = $translator;
    }

    public function set_settings(Interface_Settings $settings): void
    {
        $this->settings = $settings;
    }

    private function get_affiliate_program_api(): Affiliate_Program_API
    {
        return Affiliate_Program_API::get_instance();
    }

    private function get_user_api(): User_API
    {
        return User_API::get_instance();
    }

    private function get_product_api(): Product_API
    {
        return Product_API::get_instance();
    }

    private function get_opinions_api(): Opinions_API
    {
        return Opinions_API::get_instance();
    }

    private function disabled_partner_tab(): bool
    {
        $partner_info_enabled = $this->settings->get(Settings_Const::PARTNER_PROGRAM);

        if ($this->get_user_api()->current_user_has_role(Caps::ROLE_LMS_PARTNER) && $partner_info_enabled) {
            return false;
        }

        return true;
    }

    private function external_landing_links_to_array(?Partner_Info $partner_info): array
    {
        $external_links = [];

        if(!$partner_info) {
            return $external_links;
        }

        $external_landing_links = $partner_info->get_external_landing_links();

        foreach ($external_landing_links as $external_landing_link) {
            $external_links[] = [
                'id' => $external_landing_link->get_id(),
                'product' => $this->get_product_name_by_id($external_landing_link->get_product_id()),
                'landing_url' => $external_landing_link->get_affiliate_url()
            ];
        }

        return $external_links;
    }

    private function get_product_name_by_id(int $id): string
    {
        $product_api = $this->get_product_api();

        $product = $product_api->find($id);

        if (!$product) {
            return '';
        }

        return $product->get_name();
    }

    private function get_products_user_can_rate(): Product_To_Rate_Collection
    {
		$current_user_id = $this->get_user_api()->get_current_user_id();

        if(!$current_user_id) {
			return Product_To_Rate_Collection::create();
        }

        return $this->get_opinions_api()->get_products_user_can_rate($current_user_id->to_int());
    }

    private function get_user_name(): string
    {
        $user = $this->get_user_api()->get_current_user();

        if (!$user) {
            return '';
        }

	    $first_name = $user->get_first_name();

	    return !empty($first_name) ? $first_name : $this->translator->translate('blocks.opinions.empty.user');
    }
}
