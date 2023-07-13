<?php
namespace bpmj\wpidea\controllers;

use bpmj\wpidea\Caps;
use bpmj\wpidea\certificates\Certificate_ID;
use bpmj\wpidea\certificates\Certificate_Template;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\exceptions\Certificate_Not_Found_Exception;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\learning\course\Interface_Readable_Author_Repository;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\user\Interface_User_Permissions_Service;
use bpmj\wpidea\user\User_Capability_Factory;
use bpmj\wpidea\certificates\Interface_Certificate_Repository;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\user\Interface_User_Repository;
use bpmj\wpidea\wolverine\product\Repository as Product_Repository;
use http\Exception;

class Certificate_Controller extends Base_Controller
{

    private Interface_Certificate_Repository $certificate_repository;
    private Interface_Readable_Course_Repository $course_repository;
    private Product_Repository $product_repository;
    private Interface_Readable_Author_Repository $author_repository;
    private Certificate_Template $certificate_template;
    private Interface_User_Repository $user_repository;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Certificate_Repository $certificate_repository,
        Interface_Readable_Course_Repository $course_repository,
        Product_Repository $product_repository,
        Interface_Readable_Author_Repository $author_repository,
        Certificate_Template $certificate_template,
        Interface_User_Repository $user_repository
    )
    {
        $this->certificate_repository = $certificate_repository;
        $this->course_repository = $course_repository;
        $this->product_repository = $product_repository;
        $this->author_repository = $author_repository;
        $this->certificate_template = $certificate_template;
        $this->user_repository = $user_repository;
        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT_SUBSCRIBER,
            'allowed_methods' => [Request_Method::GET],
            'rules' => [
                'download_action' => [
                    'caps'  => [Caps::CAP_VIEW_SENSITIVE_DATA],
                ]
            ]
        ];
    }

    public function download_action(Current_Request $current_request): string
    {
        $post_id = WPI()->certificates->get_certificate_post_id($current_request->get_query_arg('id'));
        if(is_null($post_id)){
            throw new Certificate_Not_Found_Exception($this->translator);
        }

        $certificate = $this->certificate_repository->find_by_id(new Certificate_ID($post_id));
        $course = $this->course_repository->find_by_certificate_id($certificate->get_id());
        $certificate_template_id = $course->get_certificate_template_id();
        $certificate_template = $this->certificate_template->get_by_id($certificate_template_id);

        $certificate_template->load_pdf_generator_script();
        $certificate_template->set_certificate_repository($this->certificate_repository);
        $certificate_template->set_course_repository($this->course_repository);
        $certificate_template->set_user_repository($this->user_repository);
        $certificate_template->set_product_repository($this->product_repository);
        $certificate_template->set_author_repository($this->author_repository);
        $certificate_template->set_params_by_user_certificate_id($post_id);
        $certificate_template->replace_params();

        return $this->view('/certificate/download', [
            'certificate' => $certificate_template
        ]);
    }

    public function download_old_action(Current_Request $current_request): void
    {
        $post_id = WPI()->certificates->get_certificate_post_id($current_request->get_query_arg('certificate'));

        if (empty( $post_id ) ) {
            throw new Certificate_Not_Found_Exception($this->translator);
        }
        $pdf_content = WPI()->certificates->get_pdf_content( $post_id );
        $orientation = WPI()->certificates->get_pdf_orientation();

        try {
            $pdf = bpmj_eddcm_render_pdf($pdf_content, $orientation);
            $pdf->stream();
        } catch ( \Exception $e ) {
            throw new Certificate_Not_Found_Exception($this->translator);
        }
    }

}
