<?php
namespace bpmj\wpidea\templates_system\templates\scarlet;

use bpmj\wpidea\templates_system\templates\base\Abstract_Tag_Page_Template;
use bpmj\wpidea\templates_system\admin\blocks\{Courses_Block, Archive_Title_Block, Search_Results_Block};
use bpmj\wpidea\View;
use bpmj\wpidea\templates_system\templates\Template;

class Search_Page_Template extends Template
{
    public const SEARCH_PHRASE_PLACEHOLDER = 'any';
    public const SEARCH_PHRASE_QUERY_PARAM_NAME = 's';

    protected $registers_blocks = [
        Search_Results_Block::class
    ];
    
    public function before_render(): void
    {
        add_filter( 'bpmj_eddcm_breadcrumbs_parents_ids', function ( $ids ) {
            return [get_option( 'page_on_front' )];
        });

        add_filter( 'bpmj_eddcm_breadcrumbs_current_element_title', function () {
            return $this->translate('search_results.page_title');
        });

        add_filter( 'bpmj_eddcm_page_title_block_title', function () {
            return $this->translate('search_results.page_title');
        });

        add_filter( 'document_title_parts', function ($tile_parts) {
            if($this->is_search_phrase_entered()) {
                return $tile_parts;
            }

            return array_merge($tile_parts, [
                'title' => $this->translate('search_results.page_title')
            ]);
        });

        parent::before_render();
    }

    public function get_default_name()
    {
        return 'template_name.search_results';
    }

    public function get_default_content()
    {
        return View::get_admin('/gutenberg/templates/scarlet/search-results-page-template');
    }

    private function translate(string $message): string
    {
        $translator = $this->get_translator();

        return $translator ? $translator->translate($message) : $message;
    }

    private function is_search_phrase_entered(): bool
    {
        $phrase = $_GET[self::SEARCH_PHRASE_QUERY_PARAM_NAME] ?? null;

        return !empty($phrase) && ($phrase !== self::SEARCH_PHRASE_PLACEHOLDER);
    }
}