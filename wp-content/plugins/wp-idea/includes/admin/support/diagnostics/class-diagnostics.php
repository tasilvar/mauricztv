<?php

namespace bpmj\wpidea\admin\support\diagnostics;

use bpmj\wpidea\admin\support\diagnostics\items\Allow_Url_Fopen;
use bpmj\wpidea\admin\support\diagnostics\items\Curl_Enabled;
use bpmj\wpidea\admin\support\diagnostics\items\Decimal_Point_Comma;
use bpmj\wpidea\admin\support\diagnostics\items\Hash_Hmac_Enabled;
use bpmj\wpidea\admin\support\diagnostics\items\Max_Input_Vars;
use bpmj\wpidea\admin\support\diagnostics\items\PHP_Session_Cookie_Lifetime;
use bpmj\wpidea\admin\support\diagnostics\items\PHP_Session_Use_Cookies;
use bpmj\wpidea\admin\support\diagnostics\items\PHP_Version;
use bpmj\wpidea\admin\support\diagnostics\items\WPI_Version;
use bpmj\wpidea\admin\support\diagnostics\items\BCMath;
use bpmj\wpidea\admin\support\diagnostics\items\Mbstring;
use bpmj\wpidea\admin\support\diagnostics\items\Abstract_Diagnostics_Item;
use bpmj\wpidea\admin\support\diagnostics\items\Memory_Limit;
use bpmj\wpidea\admin\support\diagnostics\items\Conflicting_Plugins;

/**
 *
 * The class responsible for displaying diagnostic informations
 *
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
    exit;

class Diagnostics {

    private $items = array();

    public function __construct(
            WPI_Version $wpi_version, 
            PHP_Version $php_version,
            Allow_Url_Fopen $allow_url_fopen,
            Curl_Enabled $curl_enabled,
            Max_Input_Vars $max_input_vars,
            BCMath $bcmath,
            Mbstring $mbstring,
            Decimal_Point_Comma $decimal_point_comma,
            Hash_Hmac_Enabled $hash_hmac_enabled,
            PHP_Session_Use_Cookies $hp_session_use_cookies,
            PHP_Session_Cookie_Lifetime $php_session_cookie_lifetime,
            Memory_Limit $memory_limit,
            Conflicting_Plugins $conflicting_plugins
    ) {
        $this->items[] = $wpi_version; 
        $this->items[] = $php_version;
        $this->items[] = $allow_url_fopen;
        $this->items[] = $curl_enabled;
        $this->items[] = $max_input_vars;
        $this->items[] = $bcmath;
        $this->items[] = $mbstring;
        $this->items[] = $decimal_point_comma;
        $this->items[] = $hash_hmac_enabled;
        $this->items[] = $hp_session_use_cookies;
        $this->items[] = $php_session_cookie_lifetime;
        $this->items[] = $memory_limit;
        $this->items[] = $conflicting_plugins;
    }

    /**
     * @return Abstract_Diagnostics_Item[]
     */
    public function get_items()
    {
        return !empty( $this->items ) && is_array( $this->items ) ? $this->items : array();
    }
}
