<?php
// Exit if accessed directly

use bpmj\wpidea\admin\settings\Settings_API;
use bpmj\wpidea\admin\settings\Core_Settings;
use bpmj\wpidea\assets\Assets;

if ( !defined( 'ABSPATH' ) )
	exit;

require_once 'class-colors-base-customizer.php';
require_once 'class-colors-manager.php';

class BPMJ_WPIDEA_Colors_Customizer extends BPMJ_WPIDEA_Base_Colors_Customizer {

    public $template = 'scarlet';

    /**
     * @var Core_Settings
     */
    private $core_settings;

    public function __construct(Core_Settings $core_settings)
    {
        parent::__construct();

        $this->core_settings = $core_settings;
    }

    /**
     * Include dependencies and run customizer settings setup
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        require_once 'class-colors-custom-control-select2.php';

        add_action( 'customize_register', array( $this, 'register_customizer_settings' ) );

        add_action( 'customize_save_after',  array( $this, 'modify_css' ) );
    }

    /**
     * Register customizer settings and run custom function on each preview refresh
     *
     * @param WP_Customize_Manager $wp_customize
     * @return void
     */
    public function register_customizer_settings( $wp_customize )
    {
        $this->get_settings_api();

        add_action( 'wp_head', array( $this, 'display_customized_colors' ) );

        $this->register_section_and_fields( $wp_customize );
    }

    /**
     * Get WP Idea settings API
     *
     * @return Settings_API
     */
    public function get_settings_api()
    {
        return $this->settings = $this->core_settings->get_layout_template_settings_api( $this->template );
    }

    /**
     * Register customizer sections and fields
     *
     * @param WP_Customize_Manager $wp_customize
     * @return void
     */
    public function register_section_and_fields( $wp_customize )
    {
        // section for colors
        $wp_customize->add_section( 'bpmj_eddcm_colors_settings', array(
            'title' => __( 'WP Idea colors settings', BPMJ_EDDCM_DOMAIN )
        ));

        $this->render_presets_settings( $wp_customize );

        global $bpmj_eddcm_colors_settings;

        foreach ( $bpmj_eddcm_colors_settings as $color ) {

            $value_from_settings = $this->settings->get_detached_option_value( $color['name'] );
            $color_setting_name = $this->get_color_setting_name($color['name']);

            // settings
            if( \strpos($color['name'], '_inverted') !== false ) continue;

            $wp_customize->add_setting(
                $color_setting_name, array (
                    'default' => $value_from_settings ?: $color[ 'default' ],
                    'type' => 'option',
                    'transport' => 'postMessage'
                )
            );

            // controls
            $wp_customize->add_control( new WP_Customize_Color_Control(
                $wp_customize,
                $color_setting_name,
                array (
                    'label' => $color[ 'label' ],
                    'section' => 'bpmj_eddcm_colors_settings',
                    'settings' => $color_setting_name
                )
            ));
        }
    }

    /**
     * Render presets choosing field
     *
     * @param WP_Customize_Manager $wp_customize
     * @return void
     */
    public function render_presets_settings( $wp_customize)
    {
        $presets = include __DIR__ . '/../color-presets.php';

        $wp_customize->add_setting( $this->get_color_setting_name('preset'),
            array(
                'default' => 'default',
                'transport' => 'postMessage',
            )
        );
        $wp_customize->add_control( new BPMJ_WPIDEA_Dropdown_Select2_Custom_Control( $wp_customize, $this->get_color_setting_name('preset'),
            array(
                'label' => __( 'Preset', BPMJ_EDDCM_DOMAIN ),
                'description' => esc_html__( 'You can select one of the preinstalled colors presets.', BPMJ_EDDCM_DOMAIN ),
                'section' => 'bpmj_eddcm_colors_settings',
                'input_attrs' => array(
                   'multiselect' => false,
                ),
                'choices' => $presets,
                'settings' => $this->get_color_setting_name('preset')
            )
        ) );
    }

    /**
     * Display customized colors on preview refresh (only triggered when setting with transport mode set to 'refresh' is changed)
     *
     * @return void
     */
    public function display_customized_colors()
    {
        global $bpmj_eddcm_colors_settings;

        echo "<style>html{";

            foreach ( $bpmj_eddcm_colors_settings as $color ) {

                if ( \strpos($color['name'], '_inverted') !== false ) {
                    $key_not_inverted = str_replace('_inverted', '', $color['name']);
                    $color_setting_name = $this->get_color_setting_name($key_not_inverted);
                    $color_before_invertion = get_option( $color_setting_name, false );
                    $color_hex = $color_before_invertion ? BPMJ_WPIDEA_Colors_Manager::getInverted( $color_before_invertion ) : $value_from_settings;
                } else {
                    $value_from_settings = $this->settings->get_detached_option_value( $color['name'] );
                    $color_setting_name = $this->get_color_setting_name($color['name']);
                    $color_hex = get_option( $color_setting_name, false ) ?: $value_from_settings;
                }

                // echo css variable
                echo '--' . str_replace( '_', '-', $color['name']) . ':' . $color_hex . ';';
            }

        echo "} .modul_lekcja {
            height: auto;
        }</style>";
    }

    /**
     * Modify output CSS file.
     * It does not affect customizer preview, only live website styles
     *
     * @param WP_Customize_Manager $wp_customize
     * @return void
     */
    public function modify_css( $wp_customize )
    {
        $assets = new Assets( BPMJ_EDDCM_TEMPLATES_DIR . 'scarlet' );

        $assets_src_dir  = $assets->get_source_dir() . '/css/partials';
        $assets_dest_dir = $assets->get_absolute_dir() . '/css';

        $regexes          = array();
        $replacements     = array();
        global $bpmj_eddcm_colors_settings;

        foreach ( $bpmj_eddcm_colors_settings as $color ) {
            $color_setting_name = $this->get_color_setting_name($color['name']);

            $color_subpattern = '(\/\*\s*' . preg_quote( $color[ 'name' ] ) . '\s*\*\/)';
            // We search for things like color: #ff0000 /* text_color */;
            $regexes[] = '/(?:#[a-fA-F0-9]{3,6}|"[a-zA-Z0-9\s]+")\s*' . $color_subpattern . '|' . $color_subpattern . '\s*(?:#[a-fA-F0-9]{3,6}|"[a-zA-Z0-9\s]+")/';

            if ( \strpos($color['name'], '_inverted') !== false ) {
				$key_not_inverted = str_replace('_inverted', '', $color['name']);
                $color_setting_name = $this->get_color_setting_name($key_not_inverted);
				$color_before_invertion = $wp_customize->get_setting( $color_setting_name )->value();
				$replacements[] =  ( $color_before_invertion ? BPMJ_WPIDEA_Colors_Manager::getInverted( $color_before_invertion ) : 'none' ) . ' $1';
			} else {
                $replacements[] = ( $wp_customize->get_setting( $color_setting_name )->value() ?: 'none' ) . ' $1';
            }
        }

        $css_file       = $assets_dest_dir . '/colors.css';
        $css_source_file= $assets_src_dir . '/colors.css';
        $css_source     = file_get_contents( $css_source_file );
        $replaced_css   = preg_replace( $regexes, $replacements, $css_source );
        $css_source_new = preg_replace( '/^@charset.+?$/m', '', $replaced_css );
		$css_string     = "/* " . basename( $css_file ) . " */\n" . $css_source_new;

        file_put_contents( $css_file, $css_string );


        WPI()->templates->reload_template_config();
        WPI()->templates->minify_css();
    }

    /**
     * Fill CSS file with default colors
     *
     * @see global $bpmj_eddcm_colors_settings
     * @see bpmj_eddcm_reload_layout_template_settings()
     *
     * @return void
     */
    public static function regenerate_css( $layout_template_settings )
    {
        $customizer_settings = get_option( 'bpmj_eddcm_scarlet_colors_settings', null );

        $assets = new Assets( BPMJ_EDDCM_TEMPLATES_DIR . 'scarlet' );

        $assets_src_dir  = $assets->get_source_dir() . '/css/partials';
        $assets_dest_dir = $assets->get_absolute_dir() . '/css';
        $regexes          = array();
        $replacements     = array();
        global $bpmj_eddcm_colors_settings;

        foreach ( $bpmj_eddcm_colors_settings as $color ) {
            $color_subpattern = '(\/\*\s*' . preg_quote( $color[ 'name' ] ) . '\s*\*\/)';
            // We search for things like color: #ff0000 /* text_color */;
            $regexes[] = '/(?:#[a-fA-F0-9]{3,6}|"[a-zA-Z0-9\s]+")\s*' . $color_subpattern . '|' . $color_subpattern . '\s*(?:#[a-fA-F0-9]{3,6}|"[a-zA-Z0-9\s]+")/';

            $replacements[] = self::get_color_setting( $color, $customizer_settings, $layout_template_settings ) . ' $1';
        }

        $css_file       = $assets_dest_dir . '/colors.css';
        $css_source_file= $assets_src_dir . '/colors.css';
        $css_source     = file_get_contents( $css_source_file );
        $replaced_css   = preg_replace( $regexes, $replacements, $css_source );
        $css_source_new = preg_replace( '/^@charset.+?$/m', '', $replaced_css );
		$css_string     = "/* " . basename( $css_file ) . " */\n" . $css_source_new;

        file_put_contents( $css_file, $css_string );
    }

    /**
     * Find color setting value, first search in customizer settings, then in layout settings, then look for default value
     *
     * @param array $color
     * @param array $customizer_settings
     * @param array $layout_template_settings
     * @return void
     */
    public static function get_color_setting($color, $customizer_settings, $layout_template_settings)
    {
        $color_value = null;
        $color_name = $color['name'];
        $color_default_value = $color['default'];

        $color_value = !empty($customizer_settings[ 'color_' . $color_name ]) ? $customizer_settings[ 'color_' . $color_name ] : null;
        if( !$color_value ) $color_value = !empty($layout_template_settings[ $color_name ]) ? $layout_template_settings[ $color_name ] : null;
        if( !$color_value ) $color_value = $color_default_value ?: 'none';

        return $color_value;
    }
}
