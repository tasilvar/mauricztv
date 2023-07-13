<?php

namespace bpmj\wpidea;

class Packages
{
    public const FEAT_SUBSCRIPTIONS = 'subscriptions';
    public const FEAT_COURSE_ACCESS_START = 'access start';
    public const FEAT_COURSE_ACCESS_TIME = 'access time';
    public const FEAT_MAILERS = 'mailers';
    public const FEAT_COURSE_CLONING = 'course cloning';
    public const FEAT_COURSE_BUNDLING = 'course bundling';
    public const FEAT_PROGRESS_TRACKING = 'progress tracking';
    public const FEAT_DELAYED_ACCESS = 'delayed access to content';
    public const FEAT_RECURRING_PAYMENTS = 'recurring payments';
    public const FEAT_VARIABLE_PRICES = 'variable_prices';
    public const FEAT_BUY_AS_GIFT = 'buy_as_gift';
    public const FEAT_TESTS = 'tests';
    public const FEAT_CERTIFICATES = 'certificates';
    public const FEAT_SALE_PRICE_DATES = 'sale_price_dates';
    public const FEAT_DISCOUNT_CODE_GENERATOR = 'discount_code_generator';
    public const FEAT_VAT_MOSS = 'vat_moss';
    public const FEAT_API_V2 = 'api_v2';
    public const FEAT_USER_NOTICES = 'user_notices';
    public const FEAT_WEBHOOKS = 'webhooks';
    public const FEAT_GUS_API = 'gus_api';
    public const FEAT_INCREASING_SALES = 'increasing_sales';
    public const FEAT_PARTNER_PROGRAM = 'partner_program';
    public const FEAT_ACTIVE_SESSIONS_LIMITER = 'active_sessions_limiter';
    public const FEAT_PAYMENT_REMINDERS = 'payment_reminders';
    public const FEAT_AFTER_PURCHASE_REDIRECTIONS = 'after_purchase_redirections';
    public const FEAT_CUSTOM_PURCHASE_LINKS = 'custom_purchase_links';
    public const FEAT_PHYSICAL_PRODUCTS = 'physical_products';
    public const FEAT_OPINIONS = 'opinions';
    public const FEAT_PRODUCTS_CUSTOM_SORTING_ORDER = 'products_custom_sorting_order';
    public const FEAT_AVAILABLE_QUANTITIES = 'available_quantities';

	public const FEAT_PRIVATE_NOTES = 'private_notes';

    public const REQUIRED_PACKAGE_PRO = 'pro';
    public const REQUIRED_PACKAGE_PLUS_OR_HIGHER = 'plus+';

    public const PLAN_PRO = 'pro';
    public const PLAN_PLUS = 'plus';


    /**
     * @var Packages
     */
    protected static $instance;

    /**
     * @var array
     */
    protected static $package_priority_order = array(
        'none' => 0,
        'start' => 1,
        'plus' => 2,
        'pro' => 3,
        'shop' => 4,
    );

    /**
     * @var array
     */
    protected static array $feature_to_package_map = [
        self::FEAT_SUBSCRIPTIONS => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_COURSE_ACCESS_START => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_COURSE_ACCESS_TIME => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_MAILERS => 'start+',
        self::FEAT_COURSE_CLONING => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER,
        self::FEAT_COURSE_BUNDLING => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER,
        self::FEAT_PROGRESS_TRACKING => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER,
        self::FEAT_DELAYED_ACCESS => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER,
        self::FEAT_RECURRING_PAYMENTS => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_VARIABLE_PRICES => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_BUY_AS_GIFT => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_TESTS => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_CERTIFICATES => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_SALE_PRICE_DATES => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER,
        self::FEAT_DISCOUNT_CODE_GENERATOR => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER,
        self::FEAT_VAT_MOSS => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_API_V2 => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER,
        self::FEAT_USER_NOTICES => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER,
        self::FEAT_WEBHOOKS => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_GUS_API => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER,
        self::FEAT_INCREASING_SALES => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_PARTNER_PROGRAM => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_ACTIVE_SESSIONS_LIMITER => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER,
        self::FEAT_PAYMENT_REMINDERS => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_AFTER_PURCHASE_REDIRECTIONS => self::REQUIRED_PACKAGE_PRO,
        self::FEAT_PHYSICAL_PRODUCTS => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER,
        self::FEAT_OPINIONS => self::REQUIRED_PACKAGE_PRO,
	    self::FEAT_PRODUCTS_CUSTOM_SORTING_ORDER => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER,
        self::FEAT_AVAILABLE_QUANTITIES => 'plus+',
        self::FEAT_CUSTOM_PURCHASE_LINKS => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER,
	    self::FEAT_PRIVATE_NOTES => self::REQUIRED_PACKAGE_PLUS_OR_HIGHER
    ];

    /**
     * @var string
     */
    public $package;

    /**
     * bpmj\wpidea\BPMJ_EDDCM_Packages constructor
     */
    public function __construct()
    {
        $package = defined('BPMJ_EDDCM_FORCE_PACKAGE') && BPMJ_EDDCM_FORCE_PACKAGE ? BPMJ_EDDCM_FORCE_PACKAGE : get_option('wpidea_package', '');
        if (empty($package)) {
            $res = $this->get_package();
            if (!empty($res)) {
                $package = $res;
                update_option('wpidea_package', $package);
            }
        }

        $this->package_priority = $this->get_package_priority($package);
        $this->package = $package;

        $this->add_hooks_and_filters();
    }

    /**
     * @return Packages
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public function get_package()
    {
        global $wpidea_settings;

        if (empty($wpidea_settings['license_key'])) {
            return '';
        }

        $package = get_option('wpidea_package', '');
        if(!empty($package)) {
            return $package;
        }

        $license_key = trim($wpidea_settings['license_key']);

        $license_info = $this->get_license_info($license_key);
        if ('true' == $license_info->success) {
            if ($license_info->payment_id < 9470) {
                return 'pro';
            }

            $packageString = $this->convert_price_id_to_package_name($license_info->price_id);
            if ($packageString) {
                return $packageString;
            }

            return 'start';
        }

        return '';
    }

    public function get_license_info($license_key)
    {

        $license_data = array();

        if (!(empty($license_key))) {

            $action = 'check_license';

            $api_params = array(
                'edd_action' => $action,
                'license' => $license_key,
                'item_id' => BPMJ_EDDCM_ID,
            );

            $response = wp_remote_get(add_query_arg($api_params, BPMJ_UPSELL_STORE_URL), array(
                'timeout' => 15,
                'sslverify' => false,
            ));
            if (!is_wp_error($response)) {
                $license_data = json_decode(wp_remote_retrieve_body($response));
            }
        }

        return $license_data;
    }

    /**
     * @param string $package
     *
     * @return bool
     */
    private function check_is_valid_package($package)
    {
        if (!isset(self::$package_priority_order[$package])) {
            _doing_it_wrong(__FUNCTION__, sprintf('This package is not supported (%s)', $package), '4.7.2');

            return false;
        }

        return true;
    }

    /**
     * @param string $feature
     *
     * @return bool
     */
    private function check_is_valid_feature($feature)
    {
        if (!isset(self::$feature_to_package_map[$feature])) {
            _doing_it_wrong(__FUNCTION__, sprintf('You must add the feature to the map before using it in checks (%s)', $feature), '4.7.2');

            return false;
        }

        return true;
    }

    /**
     * @param string $package
     *
     * @return int
     */
    private function get_package_priority($package)
    {
        if (empty($package)) {
            $package = 'none';
        }
        return (int)self::$package_priority_order[$package];
    }

    /**
     * @return bool
     */
    public function has_start()
    {
        return 'start' === $this->package;
    }

    /**
     * @return bool
     */
    public function has_plus()
    {
        return 'plus' === $this->package;
    }

    /**
     * @return bool
     */
    public function has_pro()
    {
        return 'pro' === $this->package;
    }

    /**
     * @return bool
     */
    public function has_shop()
    {
        return 'shop' === $this->package;
    }

    /**
     * @param string $package
     * @param bool $include
     *
     * @return bool
     */
    public function has_higher_than($package, $include = false)
    {
        $other_package_priority = $this->get_package_priority($package);

        return $include ? $this->package_priority >= $other_package_priority : $this->package_priority > $other_package_priority;
    }

    /**
     * @param bool $include
     *
     * @return bool
     */
    public function has_higher_than_start($include = false)
    {
        return $this->has_higher_than('start', $include);
    }

    /**
     * @param bool $include
     *
     * @return bool
     */
    public function has_higher_than_plus($include = false)
    {
        return $this->has_higher_than('plus', $include);
    }

    /**
     * @param bool $include
     *
     * @return bool
     */
    public function has_higher_than_pro($include = false)
    {
        return $this->has_higher_than('pro', $include);
    }

    /**
     * @param string $feature
     *
     * @return bool
     */
    public function has_access_to_feature($feature)
    {
        if (!$this->check_is_valid_feature($feature)) {
            return false;
        }

        $feature_spec = self::$feature_to_package_map[$feature];
        if (is_array($feature_spec)) {
            return in_array($this->package, $feature_spec);
        } else if ('+' === substr($feature_spec, -1)) {
            $feature_spec_at_least = substr($feature_spec, 0, strlen($feature_spec) - 1);
            if (!$this->check_is_valid_package($feature_spec_at_least)) {
                return false;
            }

            return $this->has_higher_than($feature_spec_at_least, true);
        } else if ('>' === substr($feature_spec, 0, 1)) {
            $feature_spec_lower_bound = substr($feature_spec, 1);
            if (!$this->check_is_valid_package($feature_spec_lower_bound)) {
                return false;
            }

            return $this->has_higher_than($feature_spec_lower_bound);
        } else if (!$this->check_is_valid_package($feature_spec)) {
            return false;
        }

        return $this->package === $feature_spec;
    }

    /**
     * @param $feature
     *
     * @return bool
     */
    public function no_access_to_feature($feature)
    {
        return !$this->has_access_to_feature($feature);
    }

    /**
     * @param string $feature
     *
     * @return string
     */
    public function required_package_label($feature)
    {
        if (!$this->check_is_valid_feature($feature)) {
            return '';
        }

        $feature_spec = self::$feature_to_package_map[$feature];
        $package_name_wrap_start = '<span class="package-name">';
        $package_name_wrap_end = '</span>';

        if (is_array($feature_spec)) {
            return sprintf(_x('one of: %s', 'package level', BPMJ_EDDCM_DOMAIN), $package_name_wrap_start . implode(', ', array_intersect(array_keys(self::$package_priority_order), $feature_spec)) . $package_name_wrap_end
            );
        } else if ('+' === substr($feature_spec, -1)) {
            $feature_spec_at_least = substr($feature_spec, 0, strlen($feature_spec) - 1);
            if (!$this->check_is_valid_package($feature_spec_at_least)) {
                return '';
            }

            return sprintf(_x('%s or higher', 'package level', BPMJ_EDDCM_DOMAIN), $package_name_wrap_start . $feature_spec_at_least . $package_name_wrap_end
            );
        } else if ('>' === substr($feature_spec, 0, 1)) {
            $feature_spec_lower_bound = substr($feature_spec, 1);
            if (!$this->check_is_valid_package($feature_spec_lower_bound)) {
                return '';
            }

            return sprintf(_x('higher than %s', 'package level', BPMJ_EDDCM_DOMAIN), $package_name_wrap_start . $feature_spec_lower_bound . $package_name_wrap_end
            );
        } else if (!$this->check_is_valid_package($feature_spec)) {
            return '';
        }

        return $package_name_wrap_start . $feature_spec . $package_name_wrap_end;
    }

    /**
     * @param string $feature
     * @param string $message
     *
     * @return string
     */
    public function feature_not_available_message($feature, $message)
    {
        return sprintf($message, $this->required_package_label($feature));
    }

    /**
     * Adds hooks and filters that disable various features
     */
    private function add_hooks_and_filters()
    {
        if ($this->no_access_to_feature(Packages::FEAT_SUBSCRIPTIONS)) {
            // This virtually disables renewal e-mails
            add_filter('pre_option_bmpj_eddpc_renewal', array($this, 'filter_return_empty_array'));
        }
    }

    /**
     * @return array
     */
    public function filter_return_empty_array()
    {
        return array();
    }

    /**
     * @return string
     */
    public function get_renewal_url()
    {
        global $wpidea_settings;

        if (empty($wpidea_settings['license_key'])) {
            return 'https://publigo.pl';
        }
        $license_key = trim($wpidea_settings['license_key']);

        return 'https://upsell.pl/zamowienie/?edd_license_key=' . $license_key . '&download_id=6245&utm_source=wpidea&utm_medium=software&utm_campaign=renew';
    }

    public function convert_price_id_to_package_name($priceId)
    {
        $priority_to_package = array_flip(self::$package_priority_order);
        if (isset($priority_to_package[(int)$priceId])) {
            return $priority_to_package[(int)$priceId];
        }

        return null;
    }

    public function get_required_package(string $feature): ?string
    {
        return self::$feature_to_package_map[$feature] ?? null;
    }
}
