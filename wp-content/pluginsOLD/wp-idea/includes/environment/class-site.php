<?php
namespace bpmj\wpidea\environment;

class Site implements Interface_Site
{
    public function get_base_url(): string
    {
        return get_site_url();
    }

    public function get_admin_url(): string
    {
        return admin_url();
    }

    public function get_ajax_url(): string
    {
        return admin_url( 'admin-ajax.php' );
    }

    public function get_name(): string
    {
        return get_bloginfo('name');
    }
}
