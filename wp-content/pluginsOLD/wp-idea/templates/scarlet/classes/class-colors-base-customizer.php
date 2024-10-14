<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

class BPMJ_WPIDEA_Base_Colors_Customizer {

    public $settings_api;

    public $template = 'default';
    
    /**
     * Class constructor
     */
    public function __construct(){
        $this->init();
    }

    /**
     * Include dependencies and run customizer settings setup
     *
     * @return void
     */
    public function init()
    {
        add_action( 'customize_preview_init', array( $this, 'include_customizer_scripts') );
        
		if(is_customize_preview()) {
			add_action( 'wp_print_scripts', array( $this, 'load_colors' ), 20, 1);
			add_action( 'wp_print_scripts', array( $this, 'load_presets_scripts' ), 20, 1); // @todo: zmiana z admin_enqueue_scripts, które powodowało wywalenie notice - pytanie czy tak może być, bo WP nie zaleca takiego podejścia		
		}
    }
	
	function load_colors()
    {   
		global $bpmj_eddcm_colors_settings;
        echo "<script type='text/javascript'>";
        $js_array = json_encode($bpmj_eddcm_colors_settings);
        echo "var bpmj_eddcm_colors_settings = ". $js_array . ";\n";
        echo "</script>";
	}

    /**
     * Include scripts used for live preview and put colors settings array into JS global variable
     *
     * @return void
     */
    function include_customizer_scripts()
    {        
        $path = '/wp-content/plugins/wp-idea/templates/' . $this->template; //should be dynamic
        wp_enqueue_script( 
            $this->template . '-themecustomizer-invert-colors',
            $path . '/assets/js/invert.js',
            array(), '', true
        );
        wp_enqueue_script( 
            $this->template . '-themecustomizer-w3color',
            $path . '/assets/js/w3color.js',
            array(), '', true
        );
        wp_enqueue_script( 
            $this->template . '-themecustomizer',
            $path . '/assets/js/theme-customizer.js',
            array( 'jquery','customize-preview' ), '', true
        );
    }

    /**
     * Load scripts responsible for handling colors preset change in customizer and put arrays with presets colors into JS global variables
     *
     * @return void
     */
    function load_presets_scripts(){
        $presets = include __DIR__ . '/../color-presets.php';
        foreach ($presets as $key => $value) {
            $preset_name = str_replace( '-', '_', $key);
            echo "<script type='text/javascript'>";
            $js_array = array_key_exists('data', $value) ? json_encode($value['data']) : array();
            echo "var bpmj_eddcm_colors_preset_{$preset_name} = {$js_array};\n";
            echo "</script>";
        }
        global $wp_customize;
        if ( isset( $wp_customize ) ) {
            wp_enqueue_script( 'bpmj_eddmc_themecustomizer_presets', BPMJ_EDDCM_URL . 'templates/scarlet/assets/js/theme-customizer-presets.js', array( 'jquery','customize-preview', 'wp-color-picker' ), '', true);
        }
    }

    /**
     * Get color setting name
     *
     * @param string $color_name
     * @return string
     */
    public function get_color_setting_name($color_name){
        return 'bpmj_eddcm_' . $this->template . '_colors_settings[color_' . $color_name . ']';
    }
}