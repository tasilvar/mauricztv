<?php

namespace bpmj\wpidea\modules\app_view\ams\api\controllers;

class AMS_Rest_Routes_Controller
{
    public const URL_NAMESPACE = 'wc/v3';
    private const URL_ROUTE_AMS_MENU = '/ams-menu';
    private const URL_ROUTE_AMS_MENU_NAMES = '/ams-menu-names';
    private const URL_ROUTE_AMS_LOGIN = '/ams-login';
    private const URL_ROUTE_AMS_VERIFY_USER = '/ams-verify-user';
    public const URL_ROUTE_AMS_VERIFY_APPLICATION_PASSWORD = '/ams-verify-application-password';
    public const URL_ROUTE_AMS_WP_GET_USER_AUTH_COOKIES = '/ams-wp-get-user-auth-cookies';
    private const URL_ROUTE_AMS_SEND_PASSWORD_RESET_LINK = '/ams-send-password-reset-link';

    public function init(): void
    {
        $this->register_routes();
    }

    private function register_routes(): void
    {
        add_action('rest_api_init', function () {
            register_rest_route(
                self::URL_NAMESPACE,
                self::URL_ROUTE_AMS_MENU,
                array(
                    'methods' => 'GET',
                    'callback' => array($this, 'ams_get_menu_items'),
                    'permission_callback' => '__return_true',
                )
            );
            register_rest_route(
                self::URL_NAMESPACE,
                self::URL_ROUTE_AMS_MENU_NAMES,
                array(
                    'methods' => 'GET',
                    'callback' => array($this, 'ams_get_menu_names'),
                    'permission_callback' => '__return_true',
                )
            );
            register_rest_route(
                self::URL_NAMESPACE,
                self::URL_ROUTE_AMS_LOGIN,
                array(
                    'methods' => 'POST',
                    'callback' => array($this, 'ams_ls_login'),
                    'permission_callback' => '__return_true',
                )
            );
            register_rest_route(
                self::URL_NAMESPACE,
                self::URL_ROUTE_AMS_VERIFY_USER,
                array(
                    'methods' => 'POST',
                    'callback' => array($this, 'ams_ls_verify_user'),
                    'permission_callback' => '__return_true',
                )
            );

            register_rest_route(self::URL_NAMESPACE, self::URL_ROUTE_AMS_VERIFY_APPLICATION_PASSWORD, array(
                'methods' => 'GET',
                'callback' => array($this, 'ams_ls_verify_application_password'),
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
            ));

            register_rest_route(self::URL_NAMESPACE, self::URL_ROUTE_AMS_WP_GET_USER_AUTH_COOKIES, array(
                'methods' => 'POST',
                'callback' => array($this, 'ams_ls_wp_get_user_auth_cookies'),
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
                'args' => array(
                    'user_id' => array(
                        'required' => true,
                        'type' => 'integer',
                        'description' => 'User ID',
                    )
                ),
            ));

            register_rest_route(
                self::URL_NAMESPACE,
                self::URL_ROUTE_AMS_SEND_PASSWORD_RESET_LINK,
                array(
                    'methods' => 'POST',
                    'callback' => array($this, 'ams_ls_send_password_reset_link'),
                    'permission_callback' => '__return_true',
                )
            );
        });
    }

    public function ams_get_menu_items(object $request)
    {
        $menu_name = 'primary-menu'; // primary-menu, top

        if (isset($request['menu_name'])) {
            $menu_name = $request['menu_name'];
        }
        $nav_menu_items = wp_get_nav_menu_items($menu_name);  //slug,id
        return (rest_ensure_response($nav_menu_items));
    }

    public function ams_get_menu_names()
    {
        $nav_menu_locations = wp_get_nav_menus();
        $result = [];
        foreach ((array)$nav_menu_locations as $item) {
            $result[$item->slug] = $item->term_id;
        }
        return (rest_ensure_response($result));
    }

    public function ams_ls_login(\WP_REST_Request $request)
    {
        $req = $request->get_json_params();

        $validate = $this->ams_basic_validate($req, array('username', 'password'));
        if ($validate != true) {
            return $validate;
        }
        $wp_version = get_bloginfo('version');
        $user = wp_authenticate(sanitize_text_field($req['username']), sanitize_text_field($req['password']));  // htmlspecialchars

        if (isset($user->errors)) {
            $error_message = strip_tags($this->ams_convert_error_to_string($user->errors));
            $error = new \WP_Error();
            $error->add('message', __($error_message . ''));
            return $error;
        } elseif (isset($user->data)) {
            $user->data->user_pass = '';
            $user->data->user_activation_key = '';
            $user->data->id = $user->ID; //integer
            $user->data->first_name = get_user_meta($user->ID, 'first_name', true);
            $user->data->last_name = get_user_meta($user->ID, 'last_name', true);
            $user->data->roles = $user->roles;
            $user->data->wp_version = $wp_version;
            return rest_ensure_response($user->data);
        } else {
            return new \WP_Error('ams_error', 'Something went wrong. Please contact support.', array('status' => 500));
        }
    }

    public function ams_ls_verify_user(\WP_REST_Request $request)
    {
        $req = $request->get_json_params();

        $validate = $this->ams_basic_validate($req, array('username'));
        if ($validate != true) {
            return $validate;
        }

        $is_email = is_email($req['username']);
        if (!$is_email) {
            $user = get_user_by('login', $req['username']); // | ID | slug | email | login.
        } else {
            $user = get_user_by('email', $req['username']); // | ID | slug | email | login.
            if (isset($user->errors)) {
                $user = get_user_by('login', $req['username']); // | ID | slug | email | login.
            }
        }

        if (isset($user->errors)) {
            $error_message = strip_tags($this->ams_convert_error_to_string($user->errors));
            $error = new \WP_Error();
            $error->add('message', __($error_message . ''));
            return $error;
        } elseif (isset($user->data)) {
            $user->data->user_pass = '';
            $user->data->user_activation_key = '';
            $user->data->id = $user->ID; //integer
            $user->data->first_name = get_user_meta($user->ID, 'first_name', true);
            $user->data->last_name = get_user_meta($user->ID, 'last_name', true);
            $user->data->roles = $user->roles;
            return rest_ensure_response($user->data);
        } else {
            return rest_ensure_response(array()); // User not found.
        }

    }

    public function ams_ls_verify_application_password(\WP_REST_Request $request)
    {
        $user = get_user_by('ID', apply_filters('determine_current_user', false)); // | User by ID
        if (isset($user->errors)) {
            $error_message = strip_tags($this->ams_convert_error_to_string($user->errors));
            $error = new \WP_Error();
            $error->add('message', __($error_message . ''));
            return $error;
        } elseif (isset($user->data)) {
            $user->data->user_pass = '';
            $user->data->user_activation_key = '';
            $user->data->id = $user->ID; //integer
            $user->data->first_name = get_user_meta($user->ID, 'first_name', true);
            $user->data->last_name = get_user_meta($user->ID, 'last_name', true);
            $user->data->roles = $user->roles;

            return rest_ensure_response($user->data);
        } else {
            return new \WP_Error('ams_error', 'Something went wrong. Please contact support.', array('status' => 500));
        }
    }

    public function ams_ls_wp_get_user_auth_cookies(\WP_REST_Request $request)
    {
        $user_id = sanitize_text_field($request->get_param('user_id'));
        $user = get_user_by('ID', $user_id); // | ID | slug | email | login.
        if (isset($user->errors)) {
            $error_message = strip_tags($this->ams_convert_error_to_string($user->errors));
            $error = new \WP_Error();
            $error->add('message', __($error_message . ''));
            return $error;
        } elseif (isset($user->data)) {
            $user->data->user_pass = '';
            $user->data->user_activation_key = '';
            $user->data->id = $user->ID; //integer
            $user->data->first_name = get_user_meta($user->ID, 'first_name', true);
            $user->data->last_name = get_user_meta($user->ID, 'last_name', true);
            $user->data->roles = $user->roles;

            $expiration = time() + apply_filters('auth_cookie_expiration', 14 * DAY_IN_SECONDS, $user->ID, true);
            $site_url = get_site_url();//get_site_option('site_url');
            if ($site_url) {
                $cookie_hash = md5($site_url);
            } else {
                $cookie_hash = '';
            }
            $user->data->expiration = $expiration;
            //$user->data->expire = $expiration + ( 12 * HOUR_IN_SECONDS );
            $user->data->cookie_hash = $cookie_hash;
            $user->data->wordpress_logged_in_ = wp_generate_auth_cookie($user->ID, $expiration, 'logged_in');
            $user->data->wordpress_ = wp_generate_auth_cookie($user->ID, $expiration, 'secure_auth');

            return rest_ensure_response($user->data);
        } else {
            return new \WP_Error('ams_error', 'Something went wrong. Please contact support.', array('status' => 500));
        }
    }

    public function ams_ls_send_password_reset_link(\WP_REST_Request $request)
    {
        $req = $request->get_json_params();
        $validate = $this->ams_basic_validate($req, array('email'));
        if ($validate != true) {
            return $validate;
        }
        $email = sanitize_email($req['email']);
        $user = get_user_by('email', $email);
        if (!$user) {
            $error = new \WP_Error();
            $error->add('message', __('The email address appears to be incorrect. Please try again.'));
            return $error;
        }
        $firstname = $user->first_name;
        $email = $user->user_email;
        $user_login = $user->user_login;
        $retrieve_password = retrieve_password($user_login);

        if (isset($retrieve_password->errors)) {
            $error_message = strip_tags($this->ams_convert_error_to_string($retrieve_password->errors));
            $error = new \WP_Error();
            $error->add('message', __($error_message . ''));
            return $error;
        }

        return (rest_ensure_response(array('message' => 'Reset Password link sent successfully!')));
    }

    private function ams_convert_error_to_string($er)
    {
        $string = ' ';
        foreach ($er as $key => $value) {

            $string = $string . '' . $key . ':';
            foreach ($value as $newkey => $newvalue) {

                $string = $string . '' . $newvalue . ' ';
            }
        }
        $string = str_replace('Lost your password?', '', $string);
        $string = str_replace('Error:', '', $string);
        $string = str_replace('[message]', '', $string);
        return ($string);
    }

    private function ams_basic_validate($request, $keys)
    {
        foreach ($keys as $key => $value) {

            if (!isset($request[$value])) {
                status_header(400);
                echo(json_encode(
                    array(
                        'message' => 'There is a problem with your input!',
                        'error' => $value . ': Field is required!',
                    ),
                    JSON_UNESCAPED_UNICODE
                ));
                die();
            }
            if (empty($request[$value])) {
                status_header(400);
                echo(json_encode(
                    array(
                        'message' => 'There is a problem with your input!',
                        'error' => $value . ': Can not be empty!',
                    ),
                    JSON_UNESCAPED_UNICODE
                ));
                die();
            }
        }
        return true;
    }
}