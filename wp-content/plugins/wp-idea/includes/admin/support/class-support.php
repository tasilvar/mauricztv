<?php

namespace bpmj\wpidea\admin\support;

use bpmj\wpidea\admin\support\diagnostics\Diagnostics;
use bpmj\wpidea\admin\support\diagnostics\Diagnostics_Data;
use bpmj\wpidea\environment\Interface_Site;
use bpmj\wpidea\Software_Variant;
use bpmj\wpidea\translator\Interface_Translator;
use PhpParser\Builder\Interface_;

class Support {

	public const URL_SLUG = 'wp-idea-support';
	
	private Diagnostics $diagnostics;
	private Diagnostics_Data $diagnostics_data;
	private Rules $rules;
    private Interface_Translator $translator;
    private Interface_Site $site;

    public function __construct(
			Diagnostics $diagnostics, 
			Diagnostics_Data $diagnostics_data, 
			Rules $rules,
            Interface_Translator $translator,
            Interface_Site $site
		) {
			$this->diagnostics = $diagnostics;
			$this->diagnostics_data = $diagnostics_data;
			$this->rules = $rules;
            $this->translator = $translator;
            $this->site = $site;
	
			// disable expiration notice (support page has its own expiration message)
			$this->disable_license_expiration_notice_on_support_page();
	}

	/**
	 * Return true if user has access to all of the support features
	 *
	 * @return bool
	 */
	public static function current_user_has_active_support()
	{
		$license_status = get_option('bpmj_eddcm_license_status');

		return 'valid' === $license_status ? true : false;
	}
	
	private function disable_license_expiration_notice_on_support_page()
	{
		if( $this->is_support_page() ){
			add_filter( 'bpmj_show_license_expired_notice', function(){ return false; } );
		};
	}

	private function is_support_page()
	{
		$is_admin = is_admin();
		$is_support = !empty( $_GET[ 'page' ] ) && $_GET[ 'page' ] === self::URL_SLUG;

		return $is_admin && $is_support;
	}

    public function get_rules(): Rules
    {
        return $this->rules;
    }

    public function get_diagnostics(): Diagnostics
    {
        return $this->diagnostics;
    }

    public function get_diagnostics_data(): Diagnostics_Data
    {
        return $this->diagnostics_data;
    }

    public function get_site_url(): string
    {
        return $this->site->get_base_url();
    }
}
