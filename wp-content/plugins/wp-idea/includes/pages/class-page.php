<?php
namespace bpmj\wpidea\pages;

use bpmj\wpidea\Post_Meta;
use bpmj\wpidea\templates_system\admin\modules\Interface_Templates_System_Modules_Factory;
use bpmj\wpidea\templates_system\templates\scarlet\Experimental_Cart_Template;
use bpmj\wpidea\templates_system\templates\Template_Metaboxes_Handler;
use bpmj\wpidea\View;

class Page
{
    public const COURSES = 'page_courses';
    public const CART = 'page_cart';
    public const CART_EXPERIMENTAL = 'page_cart_experimental';
    public const COURSE_OFFER = 'course_offer_page';
    public const COURSE_PANEL = 'course_panel_page';
    public const COURSE_MODULE = 'course_module_page';
    public const COURSE_LESSON = 'course_lesson_page';
    public const USER_ACCOUNT = 'user_account_page';
    public const QUIZ = 'quiz_page';
    public const CATEGORY = 'category_page';
    public const TAG = 'tag_page';
    public const SEARCH_RESULTS = 'search_results_page';

    protected $template;

    public function __construct(
        Interface_Templates_System_Modules_Factory $templates_system_modules_factory
    ) {
        $page_renderer = $templates_system_modules_factory->get_page_renderer();

        $this->template = $page_renderer->get_current_page_template();
    }

    public function render_template(): void
    {
        if(!$this->has_template()) {
            echo $this->get_main_view();

            return;
        }

        $this->template->before_render();

        $content = $this->apply_filters($this->template->render());
        if ($this->template->is_full_page_template()){
            echo $this->get_experimental_cart_view($content);
            exit;
        }

        echo $this->get_main_view($content);
    }

    private function apply_filters(string $content): string {
        global $wp_embed;

        $content = $wp_embed->autoembed( $content );

        return $content;
    }

    public function has_template(): bool
    {
        return !empty($this->template);
    }

    public function disable_wpi_css_for_current_template(): bool
    {
        if (!$this->has_template()) {
            return false;
        }
        
        $disable_wpi_css = Post_Meta::get($this->template->get_id(), Template_Metaboxes_Handler::META_DISABLE_WPI_CSS);

        return !empty($disable_wpi_css);
    }

    private function get_main_view(string $content = ''): string
    {
        return View::get('views/main', [
            'content' => $content
        ]);
    }

    private function get_experimental_cart_view(string $content = ''): string
    {
        return View::get('views/cart-experimental', [
            'content' => $content
        ]);
    }
}