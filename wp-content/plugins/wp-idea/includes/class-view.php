<?php
namespace bpmj\wpidea;

use bpmj\wpidea\view\Interface_View_Provider;

class View
{
    public function get_view($name, $params = [], ?Interface_View_Provider $view_provider = null)
    {
        return self::include_view(BPMJ_EDDCM_DIR . 'views/', $name, $params, $view_provider);
    }

    public function get_admin_view($name,  $params = [], ?Interface_View_Provider $view_provider = null)
    {
        return self::include_view(BPMJ_EDDCM_DIR . 'includes/admin/views/', $name, $params, $view_provider);
    }

    public static function get($name, $params = [])
    {
        return self::include_view(BPMJ_EDDCM_DIR . 'views/', $name, $params);
    }

    public static function get_admin($name,  $params = [])
    {
        return self::include_view(BPMJ_EDDCM_DIR . 'includes/admin/views/', $name, $params);
    }

    protected static function include_view($root, $name, $params = [], ?Interface_View_Provider $view_provider_instance = null)
    {

        if(strpos($name, '.php') === false) $name .= '.php';

        $path = $name;

        if(strpos($path, '/') === 0){
            $path = $root . $path;
        } else {
            $backtrace_level = $view_provider_instance ? 2 : 1; //may change in case this code is moved to another place or deeper in the callstack
            $path = dirname(debug_backtrace()[$backtrace_level]['file']) . '/' . $path;
        }

        ob_start();
            extract($params);
            $view = $view_provider_instance ?: self::class;
            include( $path );
        $view_content = ob_get_clean();

        return self::parse_view_content($view_content, $params);
    }

    protected static function parse_view_content($view_content, $params)
    {
        $parsed = preg_replace_callback('/(\{.*?\})/', function($matches) use ($params){
            extract($params);

            $var_name = trim($matches[1],"{}");

            return ${$var_name} ?? $matches[1];
        }, $view_content);

        return $parsed;
    }
}
