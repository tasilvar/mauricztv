<?php

namespace bpmj\wpidea\modules\meta_conversion_api\core\services;

class Page_Info_Getter implements Interface_Page_Info_Getter
{
    public function get_current_page_url(): string
    {
        return (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public function get_page_title(): string
    {
        return get_the_title();
    }
}
