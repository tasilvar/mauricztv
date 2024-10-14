<?php
namespace bpmj\wpidea\pages\renderers;

use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\pages\Page;
use bpmj\wpidea\Post_Meta;
use bpmj\wpidea\templates_system\Experimental_Cart_View_Handler;
use bpmj\wpidea\templates_system\groups\settings\Template_Group_Settings;
use bpmj\wpidea\templates_system\groups\Template_Groups_Repository;
use bpmj\wpidea\templates_system\templates\Template;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\translator\Interface_Translator;

class New_Templates_Page_Renderer implements Interface_Page_Renderer
{
    private $active_template_group;

    private $lms_settings;

    private $post_meta;
    private Interface_Translator $translator;

    private Experimental_Cart_View_Handler $experimental_cart_view_handler;

    public function __construct(
        Template_Groups_Repository $template_groups_repository,
        LMS_Settings $lms_settings,
        Post_Meta $post_meta,
        Experimental_Cart_View_Handler $experimental_cart_view_handler,
        Interface_Translator $translator
    ) {
        $this->lms_settings = $lms_settings;
        $this->post_meta = $post_meta;
        $this->experimental_cart_view_handler = $experimental_cart_view_handler;

        $this->active_template_group = $template_groups_repository->find_active();
        $this->translator = $translator;
    }

    public function get_current_page_template(): ?Template
    {
        if($this->active_template_group === null) {
            return null;
        }

        return $this->get_current_template();
    }

    protected function get_current_template(): ?Template
    {
        if ( $this->is_search_page() ) {
            return $this->get_template_for_page(Page::SEARCH_RESULTS);
        }

        if ( $this->is_courses_page() ) {
            return $this->prepare_courses_page_template();
        }

        if ( $this->is_course_offer_page() ) {
            return $this->get_template_for_page(Page::COURSE_OFFER);
        }

        if( $this->is_cart_page() ) {
            if ($this->experimental_cart_view_handler->should_use_experimental_view()) {
                return $this->get_template_for_page(Page::CART_EXPERIMENTAL);
            }
            return $this->get_template_for_page(Page::CART);
        }

        if( $this->is_course_module_page() ) {
            return $this->get_template_for_page(Page::COURSE_MODULE);
        }

        if( $this->is_course_panel_page() ) {
            return $this->get_template_for_page(Page::COURSE_PANEL);
        }

        if( $this->is_user_account_page() ) {
            return $this->get_template_for_page(Page::USER_ACCOUNT);
        }

        if( $this->is_lesson_page() ) {
            return $this->get_template_for_page(Page::COURSE_LESSON);
        }

        if ( $this->is_quiz_page() ) {
            return $this->get_template_for_page(Page::QUIZ);
        }

        if ( $this->is_category_page() ) {
            return $this->get_template_for_page(Page::CATEGORY);
        }

        if ( $this->is_tag_page() ) {
            return $this->get_template_for_page(Page::TAG);
        }


        return null;
    }

    private function is_courses_page(): bool
    {
        $active_group = $this->active_template_group;
        $current_page_id = get_the_ID();

        if( $current_page_id === false) {
            return false;
        }

        $courses_page_id = $active_group->get_option(Template_Group_Settings::OPTION_COURSES_PAGE);
        if(empty($courses_page_id) || !is_numeric($courses_page_id)) {
            return false;
        }

        if ($current_page_id === (int)$courses_page_id){
            return true;
        }

        return false;
    }

    private function prepare_courses_page_template(): ?Template
    {
        add_filter('bpmj_cm_get_body_class', function(){ return 'courses'; });

        return $this->get_template_for_page(Page::COURSES);
    }

    private function is_course_offer_page(): bool
    {
        if(is_archive()){
            return false;
        }

        return edd_get_download() !== null;
    }

    private function is_cart_page(): bool
    {
        return edd_is_checkout();
    }

    private function is_course_module_page(): bool
    {
        return 'full' === $this->post_meta->get_meta(get_the_ID(), 'mode');
    }

    private function is_course_panel_page(): bool
    {
        if(is_archive()) {
            return false;
        }

        return 'home' === Post_Meta::get(get_the_ID(), 'mode');
    }

    private function is_user_account_page(): bool
    {
        $current_page_id = get_the_ID();
        $legacy_option = (int)$this->lms_settings->get('profile_editor_page');

        if($current_page_id === $legacy_option) {
            return true;
        }

        $user_account_page_id_option = $this->active_template_group->get_option(Template_Group_Settings::OPTION_USER_ACCOUNT_PAGE);
        if(empty($user_account_page_id_option) || !is_numeric($user_account_page_id_option)) {
            return false;
        }

        if ($current_page_id === (int)$user_account_page_id_option){
            return true;
        }

        return false;
    }

    private function is_lesson_page(): bool
    {
        return 'lesson' === $this->post_meta->get_meta(get_the_ID(), 'mode');
    }

    private function is_quiz_page(): bool
    {
        return 'test' === $this->post_meta->get_meta(get_the_ID(), 'mode');
    }

    private function get_template(string $template_class): ?Template
    {
        $group_id = $this->active_template_group->get_id();

        /** @var Template $template_class */
        $template = $template_class::find_active_one_in_group($group_id);

        if(!$template) {
            return null;
        }

        $template->set_translator($this->translator);

        return $template;
    }

    private function get_template_for_page(string $page): ?Template
    {
        $template_class = $this->active_template_group->get_template_class_for_page($page);

        if($template_class === null) {
            return null;
        }
        return $this->get_template($template_class);
    }

    private function is_category_page(): bool
    {
        return is_tax('download_category');
    }

    private function is_tag_page(): bool
    {
        return is_tax('download_tag');
    }

    private function is_search_page(): bool
    {
        return is_search();
    }

}