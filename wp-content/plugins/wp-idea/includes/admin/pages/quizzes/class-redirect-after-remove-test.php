<?php

namespace bpmj\wpidea\admin\pages\quizzes;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\routing\Interface_Url_Generator;

class Redirect_After_Remove_Test implements Interface_Initiable
{
    private const ACTION_NAME = 'load-edit.php';

    private Interface_Actions $actions;

    private Interface_Url_Generator $url_generator;

    public function __construct(
        Interface_Actions $actions,
        Interface_Url_Generator $url_generator
    ) {
        $this->actions = $actions;
        $this->url_generator = $url_generator;
    }

    public function init(): void
    {
        $url = $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::QUIZZES]
        );

        $this->actions->add(
            self::ACTION_NAME,
            function () use ($url) {
                if (isset($_REQUEST['trashed']) && $this->deleted_page_is_test($_REQUEST['ids'])) {
                    wp_redirect($url);
                    exit;
                }
            }
        );
    }

    private function deleted_page_is_test(int $post_id): bool
    {
        return metadata_exists('post', $post_id, 'test_questions');
    }
}
