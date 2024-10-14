<?php

namespace bpmj\wpidea\caps;

use bpmj\wpidea\Caps;
use bpmj\wpidea\caps\Access_Filter_Name;

/**
 *
 * The class responsible for access filters
 *
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
    exit;

class Access_Filters {
    public function __construct(){
        $this->init();
    }

    private function init()
    {        
        add_filter( 'init', array($this, 'prevent_save'), 10, 3 );

        // hide sensitive data
        add_filter( Access_Filter_Name::CUSTOMER_EMAIL, array($this, 'filter_email'), 10, 3 );

        add_filter( Access_Filter_Name::CUSTOMER_NAME, array($this, 'filter_name'), 10, 3 );

        add_filter( Access_Filter_Name::CUSTOMER_FIRST_NAME, array($this, 'filter_first_name'), 10, 3 );

        add_filter( Access_Filter_Name::CUSTOMER_LAST_NAME, array($this, 'filter_last_name'), 10, 3 );

        add_filter( Access_Filter_Name::CUSTOMER_LOGIN, array($this, 'filter_login'), 10, 3 );

        add_filter( Access_Filter_Name::CUSTOMER_ADDRESS, array($this, 'filter_address'), 10, 3 );

        add_filter( Access_Filter_Name::CUSTOMER_IP, array($this, 'filter_ip'), 10, 3 );

        add_filter( Access_Filter_Name::CUSTOMER_PHONE, array($this, 'filter_phone'), 10, 3 );

        add_filter( Access_Filter_Name::CUSTOMER_COUNTRY, array($this, 'filter_country'), 10, 3 );

        add_filter( Access_Filter_Name::CUSTOMER_NIP, array($this, 'filter_nip'), 10, 3 );

        add_filter( Access_Filter_Name::CUSTOMER_COMPANY, array($this, 'filter_company'), 10, 3 );

        add_filter( Access_Filter_Name::DISALLOW_EDIT, array($this, 'filter_edit'), 10, 3 );

        // hide data in user profile (user-edit page)
        add_filter( Access_Filter_Name::EDIT_USER_FIRST_NAME, array($this, 'filter_first_name'), 10, 3 );

        add_filter( Access_Filter_Name::EDIT_USER_LOGIN, array($this, 'filter_login'), 10, 3 );

        add_filter( Access_Filter_Name::EDIT_USER_NICKNAME, array($this, 'filter_nickname'), 10, 3 );

        add_filter( Access_Filter_Name::EDIT_USER_DISPLAY_NAME, array($this, 'filter_display_name'), 10, 3 );

        add_filter( Access_Filter_Name::EDIT_USER_EMAIL, array($this, 'filter_email'), 10, 3 );

        add_filter( Access_Filter_Name::EDIT_USER_LAST_NAME, array($this, 'filter_last_name'), 10, 3 );

        add_filter( Access_Filter_Name::EDD_GET_PAYMENT_NOTE, array($this, 'filter_payment_note') );

        add_action('plugins_loaded', function(){
            add_filter( Access_Filter_Name::OPTION_SHOW_AVATARS, array($this, 'filter_show_avatars') );
        });
    }

    /**
     * Return true if user cannot see sensitive data.
     * We use email address instead of ID because the ID stored in EDD payments is unreliable
     *
     * @param string $user_email
     * @return boolean
     */
    public static function cannot_see_sensitive_data($user_email = null)
    {
        /**
         * Temporarily disabled (so for now user cannot see even his own data).
         * Enabling this feature will require improving filters so that they always pass an email as an argument
         */
        // if( !empty($user_email) ){
        //     $current_user = wp_get_current_user();
        //     $email_belongs_to_current_user = !empty( $current_user->user_email ) && $current_user->user_email == $user_email;
            
        //     if( $email_belongs_to_current_user ){
        //         return false; //current user can see his own data
        //     }
        // }
        return !current_user_can( Caps::CAP_VIEW_SENSITIVE_DATA );
    }
    
    public function prevent_save()
    {
        if( self::cannot_see_sensitive_data() ){
            if(!empty($_POST['from']) && $_POST['from'] === 'profile' && !empty($_POST['user_id'])){
                /** 
                 * For now user cannot save even his own data
                 * @see cannot_see_sensitive_data()
                 */
                // if( $_POST['user_id'] == get_current_user_id() ) return;

                foreach ($this->disable_inputs as $input_name) {
                    unset($_POST[$input_name]);
                }
            }
        }
    }

    public function filter_show_avatars()
    {
        if( self::cannot_see_sensitive_data() ){
            return false;
        }

        return true;
    }

    public function filter_email($value, $customer_id = null, $customer_email = null)
    {
        $args = array(
            'value' => $value,
            'customer_id' => $customer_id,
            'customer_email' => $customer_email,
            'prefix' => 'email_'
        );

        return $this->get_placeholder( $args );
    }

    public function filter_name($value, $customer_id = null, $customer_email = null)
    {
        $args = array(
            'value' => $value,
            'customer_id' => $customer_id,
            'customer_email' => $customer_email,
            'prefix' => 'username_'
        );

        return $this->get_placeholder( $args );
    }

    public function filter_login($value, $customer_id = null, $customer_email = null)
    { 
        $args = array(
            'value' => $value,
            'customer_id' => $customer_id,
            'customer_email' => $customer_email,
            'prefix' => 'login_'
        );

        return $this->get_placeholder( $args );
    }

    public function filter_nickname($value, $customer_id = null, $customer_email = null)
    {
        $args = array(
            'value' => $value,
            'customer_id' => $customer_id,
            'customer_email' => $customer_email,
            'prefix' => 'nickname_'
        );

        return $this->get_placeholder( $args );
    }

    public function filter_display_name($value, $customer_id = null, $customer_email = null)
    {
        $args = array(
            'value' => $value,
            'customer_id' => $customer_id,
            'customer_email' => $customer_email,
            'prefix' => 'DisplayName_'
        );

        return $this->get_placeholder( $args );
    }

    public function filter_last_name($value, $customer_id = null, $customer_email = null)
    {
        $args = array(
            'value' => $value,
            'customer_id' => $customer_id,
            'customer_email' => $customer_email,
            'prefix' => 'LastName_'
        );

        return $this->get_placeholder( $args );
    }

    public function filter_first_name($value, $customer_id = null, $customer_email = null)
    {
        $args = array(
            'value' => $value,
            'customer_id' => $customer_id,
            'customer_email' => $customer_email,
            'prefix' => 'FirstName_'
        );

        return $this->get_placeholder( $args );
    }

    public function filter_ip($value, $customer_id = null, $customer_email = null)
    {
        $args = array(
            'value' => $value,
            'customer_id' => $customer_id,
            'customer_email' => $customer_email,
            'prefix' => 'ip_'
        );

        return $this->get_placeholder( $args );
    }

    public function filter_address($value, $customer_id = null, $customer_email = null)
    {
        if( self::cannot_see_sensitive_data( $customer_email ) ){
            if(is_array($value)){
                foreach ($value as $i => $address_line) {
                    $hidden = __( 'Hidden', BPMJ_EDDCM_DOMAIN );
                    $line = "{$i} ({$hidden})";
                    $value[$i] = "<i>$line</i>";
                }
            }
        }
        return $value;
    }

    public function filter_phone($value, $customer_id = null, $customer_email = null)
    {
        $args = array(
            'value' => $value,
            'customer_id' => $customer_id,
            'customer_email' => $customer_email,
            'prefix' => 'phone_'
        );

        return $this->get_placeholder( $args );
    }

    public function filter_country($value, $customer_id = null, $customer_email = null)
    {
        $args = array(
            'value' => $value,
            'customer_id' => $customer_id,
            'customer_email' => $customer_email,
            'prefix' => 'country_'
        );

        return $this->get_placeholder( $args );
    }

    public function filter_nip($value, $customer_id = null, $customer_email = null)
    {
        $args = array(
            'value' => $value,
            'customer_id' => $customer_id,
            'customer_email' => $customer_email,
            'prefix' => 'nip_'
        );

        return $this->get_placeholder( $args );
    }

    public function filter_company($value, $customer_id = null, $customer_email = null)
    {
        $args = array(
            'value' => $value,
            'customer_id' => $customer_id,
            'customer_email' => $customer_email,
            'prefix' => 'company_'
        );

        return $this->get_placeholder( $args );
    }

    public function filter_edit($disallow = false)
    {
        if( self::cannot_see_sensitive_data() ){
            $disallow = true;
        }
        return $disallow;
    }

    public function filter_payment_note( $note )
    {
        if ( ! $this->cannot_see_sensitive_data() ) return $note;

        if ( $note instanceof \WP_Comment ){
            // hide PayU buyer personal data
            $prefixes = array( 'PayU order: ', 'PayU raw input: ' );

            foreach ($prefixes as $prefix) {
                if( strpos( $note->comment_content, $prefix ) !== false ){
                    $content = $note->comment_content;
                    $content = str_replace( $prefix, '', $content);
                    
                    $decoded = json_decode( $content );
                    if( $decoded ){
                        if( !empty( $decoded->buyer ) ) $decoded->buyer =  __( 'Hidden', BPMJ_EDDCM_DOMAIN );
                        if( !empty( $decoded->order->buyer ) ) $decoded->order->buyer =  __( 'Hidden', BPMJ_EDDCM_DOMAIN );
                        $content = json_encode( $decoded );

                        $note->comment_content = $prefix . ' ' . $content;
                    }
                }
            }
            
            // hide emails
            $note->comment_content = preg_replace( '/[a-zA-Z0-9-_.]+@[a-zA-Z0-9-_.]+/',  'email (' . __( 'Hidden', BPMJ_EDDCM_DOMAIN ) . ')', $note->comment_content );
        }

        return $note;
    }

    private function get_placeholder( array $args )
    {
        $default_args = array(
            'value' => null,
            'customer_id' => null,
            'customer_email' => null,
            'wrap' => false,
            'prefix' => 'user_data_',
        );
        $args = array_merge( $default_args, $args );

        extract( $args );

        $user = get_user_by( 'email', $customer_email );
        $customer_id = $customer_id ?: 0;
        $customer_id = !empty( $user ) ? $user->ID : $customer_id;

        if( self::cannot_see_sensitive_data( $customer_email ) ){
            $value = "{$prefix}{$customer_id}";
        }
        
        return $value;
    }
}