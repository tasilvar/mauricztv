<?php
namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Base_Controller;
use bpmj\wpidea\Courses;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\exceptions\No_Required_Variables_Exception;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Redirect_Controller extends Base_Controller
{
    private Courses $courses;
    private Interface_Url_Generator $url_generator;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Courses $courses,
        Interface_Url_Generator $url_generator
    ) {
        $this->courses = $courses;
        $this->url_generator = $url_generator;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'caps' => [Caps::CAP_MANAGE_PRODUCTS],
            'allowed_methods' => [Request_Method::GET]
        ];
    }

    public function redirect_to_edit_product_description_action(Current_Request $current_request): void
    {
        $id = $current_request->get_query_arg('course_id');

        if (!$id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $product_id = $this->courses->get_product_by_course((int)$id);

        $this->redirector->redirect(
            $this->url_generator->generate_admin_page_url('post.php', [
                'post' => $product_id,
                'action' => 'edit',
                'edit_description' => 1
            ])
        );
    }
}
