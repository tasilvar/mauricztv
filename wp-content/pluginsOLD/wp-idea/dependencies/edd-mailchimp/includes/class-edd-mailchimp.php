<?php

use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;

/**
 * EDD MailChimp class, extension of the EDD base newsletter classs
 *
 * @copyright   Copyright (c) 2013, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
*/

class EDD_MailChimp extends EDD_Newsletter implements Interface_External_Service_Integration {
use Trait_External_Service_Integration;

    const SERVICE_NAME = 'Mailchimp';
    private const TRANSIENT_LIST = 'edd_mailchimp_list_data';
    private const TRANSIENT_CATEGORIES = 'edd_mailchimp_categories_';
    private const TRANSIENT_GROUPS = 'edd_mailchimp_groups_';

	/**
	 * Sets up the checkout label
	 */
	public function init() {
		global $edd_options;
		if( ! empty( $edd_options['eddmc_label'] ) ) {
			$this->checkout_label = trim( $edd_options['eddmc_label'] );
		} else {
			$this->checkout_label = Translator_Static_Helper::translate('newsletter.sign_up');
		}

		add_filter( 'edd_settings_sections_extensions', array( $this, 'subsection' ), 10, 1 );
		add_filter( 'edd_settings_extensions_sanitize', array( $this, 'save_settings' ) );
        add_action( 'mailchimp_clear_cache', [$this, 'clear_cache'] );

	}

	/**
	 * Retrieves the lists from Mail Chimp
	 */
	public function get_lists() {

		global $edd_options;

		$this->lists[] = '';

		if( ! empty( $edd_options['eddmc_api'] ) ) {

			$list_data = get_transient( self::TRANSIENT_LIST );
			if( false === $list_data ) {

				$api       = new EDD_MailChimp_API( trim( $edd_options['eddmc_api'] ) );
				$list_data = $api->call('lists', 'GET', [ 'count' => 100 ] );
				set_transient( self::TRANSIENT_LIST, $list_data, 24*24*24 );
			}

			if( $list_data->lists && ! isset( $list_data->status ) ) {
				foreach( $list_data->lists as $list ) {

					$this->lists[ $list->id ] = $list->name;
                    $list_groups = $this->get_groupings($list->id);

                    foreach ( $list_groups as $group_key => $group_value ) {
                        $this->lists[ $group_key ] = '- '. $group_value;
                    }

				}
			}
		}

		return (array) $this->lists;
	}

	/**
	* Retrive the list of groupings associated with a list id
	*
	* @param  string $list_id     List id for which groupings should be returned
	* @return array  $groups_data Data about the groups
	*/
	public function get_groupings( $list_id = '' ) {

		global $edd_options;

	    $categories_data = get_transient( self::TRANSIENT_CATEGORIES . $list_id );

	    if( false === $categories_data ) {

			if( ! class_exists( 'EDD_MailChimp_API' ) ) {
				require_once( EDD_MAILCHIMP_PATH . '/includes/MailChimp.class.php' );
			}

			$api           = new EDD_MailChimp_API( trim( $edd_options['eddmc_api'] ) );
			$categories_data = $api->call( 'lists/' . $list_id . '/interest-categories', 'GET' );
			set_transient( self::TRANSIENT_CATEGORIES . $list_id, $categories_data, 24*24*24 );
		}

		if( ! $categories_data || isset( $categories_data->status ) ) {
		    return [];
		}

		$groups_data = [];

	    foreach( $categories_data->categories as $category ) {
	        $list_groups = $this->get_groups($list_id, $category->id, $category->title);
	        
	        foreach ( $list_groups as $group_key => $group_value ) {
	            $groups_data[ $group_key ] = $group_value;
	        }
		}

		return $groups_data;
	}

	private function get_groups( $list_id = '', $category_id = '', $category_name = '' ) {

	    global $edd_options;
	    
        $interests_data = get_transient( self::TRANSIENT_GROUPS . $category_id );
        
        if( false === $interests_data ) {
            
            if( ! class_exists( 'EDD_MailChimp_API' ) ) {
                require_once( EDD_MAILCHIMP_PATH . '/includes/MailChimp.class.php' );
            }
            
            $api           = new EDD_MailChimp_API( trim( $edd_options['eddmc_api'] ) );
            $interests_data = $api->call( 'lists/' . $list_id . '/interest-categories/' . $category_id . '/interests', 'GET' );
            set_transient( self::TRANSIENT_GROUPS . $category_id, $interests_data, 24*24*24 );
        }
        
        if( ! $interests_data || isset( $interests_data->status ) ) {
            return [];
        }

        $group_data = [];
        
        foreach( $interests_data->interests as $group ) {
            $group_id   = $group->id;
            $group_name = $group->name;
            
            $group_data["$list_id|$category_id|$group_id"] = $category_name . ' - ' . $group_name;
        }
            
	    return $group_data;
	}
	
	/**
	 * Register our subsection for EDD 2.5
	 *
	 * @since  2.5.6
	 * @param  array $sections The subsections
	 * @return array           The subsections with MailChimp added
	 */
	function subsection( $sections ) {
		$sections['mailchimp'] = __( 'MailChimp', 'eddmc' );
		return $sections;
	}


	/**
	 * Registers the plugin settings
	 */
	public function settings( $settings ) {

		$eddmc_settings = array(
			array(
				'id'      => 'eddmc_settings',
				'name'    => '<strong>' . __( 'MailChimp Settings', 'eddmc' ) . '</strong>',
				'desc'    => __( 'Configure MailChimp Integration Settings', 'eddmc' ),
				'type'    => 'header'
			),
			array(
				'id'      => 'eddmc_api',
				'name'    => __( 'MailChimp API Key', 'eddmc' ),
				'desc'    => __( 'Enter your MailChimp API key', 'eddmc' ),
				'type'    => 'text',
				'size'    => 'regular'
			),
			array(
				'id'      => 'eddmc_show_checkout_signup',
				'name'    => __( 'Show Signup on Checkout', 'eddmc' ),
				'desc'    => __( 'Allow customers to signup for the list selected below during checkout?', 'eddmc' ),
				'type'    => 'checkbox'
			),
			array(
				'id'      => 'eddmc_list',
				'name'    => __( 'Choose a list', 'edda'),
				'desc'    => __( 'Select the list you wish to subscribe buyers to', 'eddmc' ),
				'type'    => 'select',
				'options' => $this->get_lists()
			),
			array(
				'id'      => 'eddmc_label',
				'name'    => __( 'Checkout Label', 'eddmc' ),
				'desc'    => __( 'This is the text shown next to the signup option', 'eddmc' ),
				'type'    => 'text',
				'size'    => 'regular'
			),
			array(
				'id'      => 'eddmc_double_opt_in',
				'name'    => __( 'Double Opt-In', 'eddmc' ),
				'desc'    => __( 'When checked, users will be sent a confirmation email after signing up, and will only be added once they have confirmed the subscription.', 'eddmc' ),
				'type'    => 'checkbox'
			)
		);

		if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
			$eddmc_settings = array( 'mailchimp' => $eddmc_settings );
		}

		return array_merge( $settings, $eddmc_settings );
	}

	/**
	 * Flush the list transient on save
	 */
	public function save_settings( $input ) {
		if( isset( $input['eddmc_api'] ) ) {
			$this->clear_cache();
		}
		return $input;
	}

	/**
	 * Determines if the checkout signup option should be displayed
	 */
	public function show_checkout_signup() {
		global $edd_options;

		return ! empty( $edd_options['eddmc_show_checkout_signup'] ) && $edd_options[ 'eddmc_show_checkout_signup' ] == '1';
	}

	/**
	 * Subscribe an email to a list
	 */
	public function subscribe_email( $user_info = [], $list_id = false, $opt_in_overridde = false ) {

		global $edd_options;

		// Make sure an API key has been entered
		if( empty( $edd_options['eddmc_api'] ) ) {
			return false;
		}

		// Retrieve the global list ID if none is provided
		if( ! $list_id ) {
			$list_id = ! empty( $edd_options['eddmc_list'] ) ? $edd_options['eddmc_list'] : false;
			if( ! $list_id ) {
				return false;
			}
		}

		if( ! class_exists( 'EDD_MailChimp_API' ) ) {
			require_once( EDD_MAILCHIMP_PATH . '/includes/MailChimp.class.php' );
		}

		$api    = new EDD_MailChimp_API( trim( $edd_options['eddmc_api'] ) );

		$merge_vars = [ 'FNAME' => $user_info['first_name'], 'LNAME' => $user_info['last_name'] ];

		$user = new \stdClass();
		$user->email_address = $user_info['email'];
		$user->status = 'subscribed';
		$user->merge_fields = $merge_vars;

		if( strpos( $list_id, '|' ) != FALSE ) {
			$parts = explode( '|', $list_id );

			$list_id = $parts[0];
			$interest_id  = $parts[2];

			$interests = new \stdClass();
			$interests->$interest_id = true;

			$user->interests = $interests;
		}

		$result = $api->call('lists/' . $list_id, 'POST', json_encode(apply_filters( 'edd_mc_subscribe_vars', [
			'members' => [ $user ],
			'update_existing' => true
		] ) ) );

		if( $result ) {
			return true;
		}

		return false;

	}
    public function check_connection(): bool
    {
        return !empty($this->get_lists());
    }

    public function clear_cache(): void
    {
        $mailchimp_list_data = get_transient(self::TRANSIENT_LIST);
        if (false !== $mailchimp_list_data && ! isset($mailchimp_list_data->status)) {
            
            foreach ($mailchimp_list_data->lists as $list) {
                $mailchimp_categories_data = get_transient(self::TRANSIENT_CATEGORIES . $list->id);
                
                if (false !== $mailchimp_categories_data && !empty($mailchimp_categories_data->categories)) {
                    foreach ($mailchimp_categories_data->categories as $category) {
                        delete_transient(self::TRANSIENT_GROUPS . $category->id);
                    }
                }
                
                delete_transient(self::TRANSIENT_CATEGORIES . $list->id);
            }
            
        }
        delete_transient(self::TRANSIENT_LIST);
    }

}
