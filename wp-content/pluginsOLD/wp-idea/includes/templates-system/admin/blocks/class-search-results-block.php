<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\modules\search_engine\api\Search_Engine_API_Static_Helper;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider_Aware;
use bpmj\wpidea\translator\Interface_Translator_Aware;
use bpmj\wpidea\templates_system\templates\scarlet\Search_Page_Template;
use bpmj\wpidea\modules\search_engine\core\value_objects\Search_Results_Collection;
use bpmj\wpidea\Current_Request;

class Search_Results_Block extends Block implements Interface_View_Provider_Aware, Interface_Translator_Aware
{
    const BLOCK_NAME = 'wpi/search-results';

    private ?Interface_View_Provider $view_provider = null;
    private ?Interface_Translator $translator = null;

    public function __construct() {
        parent::__construct();

        $this->title = $this->translator ? $this->translator->translate('search_results.block_name') : 'Search results';
    }

    public function get_content_to_render($atts)
    {
        $search_query = $this->get_search_query();
        $results = $this->get_search_results($search_query);

        return $this->view_provider->get($this->get_template_path_base() . '/search-results/search-results', [
            'translator' => $this->translator,
            'query' => $search_query,
            'results' => $results,
            'results_count' => $results->size()
        ]);
    }

    public function set_view_provider(Interface_View_Provider $view_provider): void
    {
        $this->view_provider = $view_provider;
    }

    public function set_translator(Interface_Translator $translator): void
    {
        $this->translator = $translator;
    }

    private function get_search_results(string $search_query): Search_Results_Collection
    {
        $current_user_id = get_current_user_id();
        if(!$current_user_id) {
            $current_user_id = null;
        }
        return Search_Engine_API_Static_Helper::search($search_query, $current_user_id);
    }

    private function get_search_query(): string
    {
        $current_request = Current_Request::create();

        $query = $current_request->get_query_arg(Search_Page_Template::SEARCH_PHRASE_QUERY_PARAM_NAME) ?? '';

        return ($query === Search_Page_Template::SEARCH_PHRASE_PLACEHOLDER) ? '' : $query;
    }
}
