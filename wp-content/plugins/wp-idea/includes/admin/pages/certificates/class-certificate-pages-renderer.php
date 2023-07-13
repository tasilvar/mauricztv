<?php

namespace bpmj\wpidea\admin\pages\certificates;

use bpmj\wpidea\admin\helpers\utils\Snackbar;
use bpmj\wpidea\admin\Notices;
use bpmj\wpidea\certificates\Certificate_ID;
use bpmj\wpidea\certificates\Certificate_Template;
use bpmj\wpidea\certificates\Certificate_Template_Actions;
use bpmj\wpidea\certificates\Interface_Certificate_Repository;
use bpmj\wpidea\controllers\admin\Admin_Certificate_Templates_Controller;
use bpmj\wpidea\exceptions\Certificate_Not_Found_Exception;
use bpmj\wpidea\learning\course\Interface_Readable_Author_Repository;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_User_Repository;
use bpmj\wpidea\View;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\wolverine\product\Repository as Product_Repository;

class Certificate_Pages_Renderer
{
    private Interface_User_Repository $user_repository;

    private Interface_Certificate_Repository $certificate_repository;

    private Interface_Readable_Course_Repository $course_repository;

    private Product_Repository $product_repository;

    private Interface_Readable_Author_Repository $author_repository;

    private Certificate_Template $certificate_template;

    private Interface_Url_Generator $url_generator;

    private Certificate_Table_Builder $certificate_table_builder;

    private Interface_View_Provider $view_provider;

    private Interface_Translator $translator;
    private Interface_Packages_API $packages_api;

    public function __construct(
        Interface_User_Repository $user_repository,
        Interface_Certificate_Repository $certificate_repository,
        Interface_Readable_Course_Repository $course_repository,
        Product_Repository $product_repository,
        Interface_Readable_Author_Repository $author_repository,
        Certificate_Template $certificate_template,
        Interface_Url_Generator $url_generator,
        Certificate_Table_Builder $certificate_table_builder,
        Interface_View_Provider $interface_view_provider,
        Interface_Translator $translator,
        Interface_Packages_API $packages_api
    ) {
        $this->user_repository = $user_repository;
        $this->certificate_repository = $certificate_repository;
        $this->course_repository = $course_repository;
        $this->product_repository = $product_repository;
        $this->author_repository = $author_repository;
        $this->certificate_template = $certificate_template;
        $this->url_generator = $url_generator;
        $this->certificate_table_builder = $certificate_table_builder;
        $this->view_provider = $interface_view_provider;
        $this->translator = $translator;
        $this->packages_api = $packages_api;
    }

    public function render_table(): void
    {
        echo $this->view_provider->get_admin('/pages/certificates/index', [
            'table' => $this->certificate_table_builder->get_table(),
            'page_title' => $this->translator->translate('certificates.page_title'),
            'no_access_to_feature' => $this->maybe_get_no_access_to_feature_message()
        ]);
    }

    private function maybe_get_no_access_to_feature_message(): ?string
    {
        if($this->packages_api->has_access_to_feature(Packages::FEAT_CERTIFICATES)) {
            return null;
        }

        return $this->packages_api->render_no_access_to_feature_info(
            Packages::FEAT_CERTIFICATES,
            $this->translator->translate('settings.sections.certificate.new_certificate.notice')
        );
    }

    public function generate_certificate_template()
    {
        if (!Certificate_Template_Actions::check_certificates_permissions()) {
            return false;
        }

        if (!Certificate_Template::check_nonce_in_get_action()) {
            return false;
        }

        $id = $_GET['id'];
        $redirect_url = admin_url(Certificate_Template::LIST_URL);

        if (is_null($id)) {
            WPI()->snackbar->display_message_on_next_request(__('Certificate not found!', BPMJ_EDDCM_DOMAIN),
                Snackbar::TYPE_ERROR);
            wp_redirect($redirect_url);
            exit;
        }

        $post_id = WPI()->certificates->get_certificate_post_id($id);
        if (is_null($post_id)) {
            throw new Certificate_Not_Found_Exception($this->translator);
        }

        $certificate = $this->certificate_repository->find_by_id(new Certificate_ID($post_id));
        $course = $this->course_repository->find_by_certificate_id($certificate->get_id());
        $certificate_template_id = $course->get_certificate_template_id();
        $certificate_template = $this->certificate_template->get_by_id($certificate_template_id);

        if (!$certificate_template) {
            WPI()->snackbar->display_message_on_next_request(__('Certificate not found!', BPMJ_EDDCM_DOMAIN),
                Snackbar::TYPE_ERROR);
            wp_redirect($redirect_url);
            exit;
        }

        $certificate_template->set_certificate_repository($this->certificate_repository);
        $certificate_template->set_course_repository($this->course_repository);
        $certificate_template->set_user_repository($this->user_repository);
        $certificate_template->set_product_repository($this->product_repository);
        $certificate_template->set_author_repository($this->author_repository);
        $certificate_template->set_params_by_user_certificate_id((int)$id);
        $certificate_template->replace_params();

        echo View::get_admin('/certificate-template/generate-certificate-template', [
            'certificate' => $certificate_template,
        ]);

    }

    /**
     * Add new certificate
     */
    public function add_certificate_template()
    {
        if (!Certificate_Template_Actions::check_certificates_permissions()) {
            WPI()->snackbar->display_message_on_next_request(__('No permission!', BPMJ_EDDCM_DOMAIN),
                Snackbar::TYPE_ERROR);
            wp_redirect(admin_url(Certificate_Template::LIST_URL));
            exit;
        }

        if (!Certificate_Template::check_nonce_in_get_action()) {
            WPI()->snackbar->display_message_on_next_request(__('Bad nonce!', BPMJ_EDDCM_DOMAIN), Snackbar::TYPE_ERROR);
            wp_redirect(admin_url(Certificate_Template::LIST_URL));
            exit;
        }

        echo View::get_admin('/certificate-template/builder-certificate-template', [
            'url' => $this->url_generator->generate(
                Admin_Certificate_Templates_Controller::class,
                'add'
            ),
            'certificate_template_action' => 'add',
            'certificate' => null
        ]);
    }

    public function enable_new_certificate_template()
    {
        if (!Certificate_Template_Actions::check_certificates_permissions()) {
            return false;
        }

        if (!Certificate_Template::check_nonce_in_get_action()) {
            return false;
        }

        Certificate_Template::enable_new_version_of_certificate_templates();
        $redirect_url = admin_url(Certificate_Template::LIST_URL);
        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Add new certificate
     */
    public function edit_certificate_template()
    {
        if (!Certificate_Template_Actions::check_certificates_permissions()) {
            return false;
        }

        if (!Certificate_Template::check_nonce_in_get_action()) {
            return false;
        }

        $id = $_GET['id'];
        $redirect_url = admin_url(Certificate_Template::LIST_URL);


        if (is_null($id)) {
            WPI()->snackbar->display_message_on_next_request(__('Certificate not found!', BPMJ_EDDCM_DOMAIN),
                Snackbar::TYPE_ERROR);
            wp_redirect($redirect_url);
            exit;
        }

        $certificate = new Certificate_Template();
        $certificate = $certificate->find_by_id($id);

        if (!$certificate) {
            WPI()->snackbar->display_message_on_next_request(__('Certificate not found!', BPMJ_EDDCM_DOMAIN),
                Snackbar::TYPE_ERROR);
            wp_redirect($redirect_url);
            exit;
        }

        add_action('wp_ajax_edit_certificate_template', 'ajax_edit_certificate_template');

        echo View::get_admin('/certificate-template/builder-certificate-template', [
            'url' => $this->url_generator->generate(
                Admin_Certificate_Templates_Controller::class,
                'edit'
            ),
            'certificate_template_action' => 'edit',
            'certificate' => $certificate,
        ]);
    }

    public function preview_certificate_template()
    {
        if (!Certificate_Template_Actions::check_certificates_permissions()) {
            return false;
        }

        if (!Certificate_Template::check_nonce_in_get_action()) {
            return false;
        }

        $id = $_GET['id'];
        $redirect_url = admin_url(Certificate_Template::LIST_URL);

        if (is_null($id)) {
            WPI()->notices->add_dismissible_notice(__('Certificate not found!'), Notices::TYPE_ERROR);
            wp_redirect($redirect_url);
            exit;
        }

        $certificate = new Certificate_Template();
        $certificate = $certificate->find_by_id($id);
        $certificate->generate_default_params();
        $certificate->replace_params();

        if (!$certificate) {
            WPI()->snackbar->display_message_on_next_request(__('Certificate not found!', BPMJ_EDDCM_DOMAIN),
                Snackbar::TYPE_ERROR);
            wp_redirect($redirect_url);
            exit;
        }

        echo View::get_admin('/certificate-template/preview-certificate-template', [
            'certificate' => $certificate,
        ]);
    }
}
