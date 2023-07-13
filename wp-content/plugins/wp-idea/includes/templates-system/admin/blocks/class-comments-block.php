<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\Course_Page;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Packages_API_Static_Helper;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\translator\Interface_Translator_Aware;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\view\Interface_View_Provider_Aware;

class Comments_Block extends Block implements Interface_Translator_Aware, Interface_View_Provider_Aware
{
    public const BLOCK_NAME = 'wpi/comments';

    private Interface_Translator $translator;
    private Interface_View_Provider $view_provider;

    public function __construct()
    {
        parent::__construct();

        $this->title = Packages_API_Static_Helper::has_access_to_feature(Packages::FEAT_PRIVATE_NOTES)
	        ? Translator_Static_Helper::translate('blocks.notes.block_name')
	        : Translator_Static_Helper::translate('blocks.notes.block_name.no_access_to_notes');
    }

    public function get_content_to_render($atts)
    {
        global $post;
        $course_page = new Course_Page($post);
        $is_module = $course_page->is_module();
        $is_lesson = $course_page->is_lesson();

        add_filter('comments_template_query_args', function ($comment_args) {
            $comment_args['is_new_templates_system'] = true;
            return $comment_args;
        });

        return $this->view_provider->get($this->get_template_path_base() . '/comments-and-notes/comments-and-notes', [
            'translator' => $this->translator,
            'display_comments' => comments_open() || get_comments_number(),
       		'display_notes_tab' => $this->display_notes_tab($course_page),
            'lesson_id' => $is_lesson ? $post->ID : null,
            'module_id' => $is_module ? $post->ID : null,
        ]);
    }

    public function set_translator(Interface_Translator $translator): void
    {
        $this->translator = $translator;
    }

    public function set_view_provider(Interface_View_Provider $view_provider): void
    {
        $this->view_provider = $view_provider;
    }

    public function display_notes_tab(Course_Page $course_page): bool
    {
        if (!Packages_API_Static_Helper::has_access_to_feature(Packages::FEAT_PRIVATE_NOTES)) {
            return false;
        }

        if($course_page->is_test()) {
			return false;
        }

        return true;
    }
}
