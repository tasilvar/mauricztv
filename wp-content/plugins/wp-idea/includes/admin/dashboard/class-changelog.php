<?php
namespace bpmj\wpidea\admin\dashboard;

class Changelog
{
    const AJAX_SECURITY_TOKEN_NAME = 'bpmj_changelog';

    const AJAX_ACTION_NAME = 'bpmj_get_changelog';

    const CHANGELOG_TRANSIENT = 'bpmj_wpi_changelog';

    public function __construct() {
        $this->pass_data_to_script();
        
        add_action('wp_ajax_' . self::AJAX_ACTION_NAME, [$this, 'ajax_get_changelog']);
    }

    public function ajax_get_changelog()
    {
        if (!check_ajax_referer(self::AJAX_SECURITY_TOKEN_NAME, 'security', false)) {
            wp_send_json_error('Invalid security token');
        }

        $changelog = $this->get_changelog();

        if(empty($changelog)) wp_send_json_error(__('No data', BPMJ_EDDCM_DOMAIN));

        wp_send_json_success($changelog);
    }

    protected function pass_data_to_script()
    {
        add_action( 'admin_enqueue_scripts', function(){
            wp_localize_script('bpmj-eddpc-admin-script', 'bpmj_changelog', [
                'security' => wp_create_nonce(self::AJAX_SECURITY_TOKEN_NAME)
            ]);
        });
    }

    protected function get_changelog()
    {
        $changelog = $this->get_changelog_data();
        
        if($changelog) $changelog = substr($changelog, 0, strpos($changelog, '</ul>') + 5); // remove everything after first <ul> to get rid of the older logs

        return $changelog ?? null;
    }

	protected function get_changelog_data()
	{
        $changelog_cache = get_site_transient(self::CHANGELOG_TRANSIENT);
        
        if(empty($changelog_cache)){
            $changelog = $this->request_changelog_from_api();

            if(empty($changelog)) return null;

            set_site_transient(self::CHANGELOG_TRANSIENT, $changelog, 60*60*24);

            return $changelog;
        }

        return $changelog_cache;
    }
    
    protected function request_changelog_from_api()
    {
		$api_params = array(
			'edd_action'	 => 'get_version',
			'item_id'	 => BPMJ_EDDCM_ID,
			'slug'		 => 'wp-idea',
			'url'		 => home_url()
		);

		$request = wp_remote_post( BPMJ_UPSELL_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
        
		if ( !is_wp_error( $request ) ) {
			$version_info = json_decode( wp_remote_retrieve_body( $request ) );
		}

		if ( !empty( $version_info ) && isset( $version_info->sections ) ) {
			$version_info->sections = maybe_unserialize( $version_info->sections );
		} else {
			$version_info = false;
		}

		if ( !empty( $version_info ) && isset( $version_info->sections[ 'changelog' ] ) ) {
			return $version_info->sections[ 'changelog' ];
        }
        
        return null;
    }
}