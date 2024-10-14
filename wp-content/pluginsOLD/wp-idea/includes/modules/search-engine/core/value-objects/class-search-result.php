<?php

namespace bpmj\wpidea\modules\search_engine\core\value_objects;

use bpmj\wpidea\data_types\Url;

class Search_Result
{
    private string $title;
    private Url $url;

    public function __construct(string $title, Url $url)
    {
        $this->title = $title;
        $this->url = $url;
    }

    public function get_title(): string
    {
        return $this->title;
    }

    public function get_url(): Url
    {
        return $this->url;
    }
}