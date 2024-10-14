<?php

namespace bpmj\wpidea\certificates;

use bpmj\wpidea\certificates\Interface_Certificate_Repository;
use bpmj\wpidea\learning\course\Interface_Readable_Author_Repository;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\user\Interface_User_Repository;
use bpmj\wpidea\wolverine\product\Repository as Product_Repository;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;

class Certificate_Template
{
    const CERTIFICATES_SLUG = 'wpi_certificate_templates';
    const ADD_PAGE = 'wp-idea-add-certificate-template';
    const ADD_URL = 'admin.php?page=' . self::ADD_PAGE;
    const EDIT_PAGE = 'wp-idea-edit-certificate-template';
    const EDIT_URL = 'admin.php?page=' . self::EDIT_PAGE;
    const PREVIEW_PAGE = 'wp-idea-preview-certificate-template';
    const PREVIEW_URL = 'admin.php?page=' . self::PREVIEW_PAGE;
    const GENERATE_PAGE = 'wp-idea-generate-certificate-template';
    const GENERATE_URL = 'admin.php?page=' . self::GENERATE_PAGE;
    const SET_DEFAULT_URL = 'admin.php?set-default-certificate-template';
    const LIST_URL = 'admin.php?page=' . Admin_Menu_Item_Slug::SETTINGS . '&autofocus=certificate';
    const USER_DOWNLOAD_URL = '?download-certificate';
    const ENABLE_NEW_VERSION_CERTIFICATES_ACTION = 'wp-idea-enable-new-certificate-template';
    const ENABLE_NEW_VERSION_CERTIFICATES_URL = 'admin.php?page=' . self::ENABLE_NEW_VERSION_CERTIFICATES_ACTION;

    const PDF_BUILDER_PAGES = [
        self::ADD_PAGE,
        self::EDIT_PAGE
    ];

    const PDF_GENERATOR_PAGES = [
        self::PREVIEW_PAGE,
        self::EDIT_PAGE,
        self::GENERATE_PAGE
    ];

    const TEMPLATES_OPTION_SLUG = 'templates';
    const LAST_ID_OPTION_SLUG = 'last_id';

    const SETTINGS_DISABLE_NEW_VERSION = 'disable_new_version_certificates';

    const CERTIFICATE_NONCE = 'certificate_nonce';
    const PARAM_NAME_NONCE = 'certificate-nonce';

    private $id;
    private $name;
    private $page;
    private $page_replacement;
    private $params = [];
    private $is_default = false;
    private $course_id = null;
    private $option;

    private Interface_Certificate_Repository $certificate_repository;
    private Interface_Readable_Course_Repository $course_repository;
    private Product_Repository $product_repository;
    private Interface_User_Repository $user_repository;
    private Interface_Readable_Author_Repository $author_repository;

    public function __construct()
    {
        $this->load_certificate_template_option();
    }

    private function load_certificate_template_option()
    {
        $option = get_option(self::CERTIFICATES_SLUG);
        if (!$option) {
            $this->create_option();
            $this->load_certificate_template_option();
        }
        $this->option = $option;
    }

    private function create_option(): bool
    {
        $option = [
            self::TEMPLATES_OPTION_SLUG => [],
            self::LAST_ID_OPTION_SLUG => 0
        ];
        return $this->update_option($option);
    }

    private function update_option($option): bool
    {
        $this->option = $option;
        return update_option(self::CERTIFICATES_SLUG, $option);
    }

    public static function get_add_url(): string
    {
        return admin_url(self::ADD_URL) . '&' . self::PARAM_NAME_NONCE . '=' . self::generate_nonce();
    }

    public static function generate_nonce(): string
    {
        return wp_create_nonce(self::CERTIFICATE_NONCE);
    }

    public static function get_enable_new_version_url(): string
    {
        return admin_url(self::ENABLE_NEW_VERSION_CERTIFICATES_URL) . '&' . self::PARAM_NAME_NONCE . '=' . self::generate_nonce();
    }

    public static function check_nonce_in_get_action(): bool
    {
        return self::check_nonce($_GET);
    }

    public static function check_nonce($arr): bool
    {
        if (!isset($arr[self::PARAM_NAME_NONCE])) {
            return false;
        }

        if (!wp_verify_nonce($arr[self::PARAM_NAME_NONCE], self::CERTIFICATE_NONCE)) {
            return false;
        }

        return true;
    }

    public static function check_nonce_in_post_action(): bool
    {
        return self::check_nonce($_POST);
    }

    public static function check_if_new_version_of_certificate_templates_is_enabled()
    {
        $settings = get_option('wp_idea');
        if (isset($settings[self::SETTINGS_DISABLE_NEW_VERSION]) && $settings[self::SETTINGS_DISABLE_NEW_VERSION] == 'on') {
            return false;
        }
        return true;
    }

    public static function enable_new_version_of_certificate_templates()
    {
        $settings = get_option('wp_idea');
        $settings[self::SETTINGS_DISABLE_NEW_VERSION] = 'off';
        update_option('wp_idea', $settings);
    }

    public function get_download_link_by_certificate_id($id)
    {
        $url = admin_url(self::GENERATE_URL);

        $args = [
            'id' => $id,
            self::PARAM_NAME_NONCE => self::generate_nonce()
        ];
        $url = add_query_arg($args, $url);

        return esc_url($url);
    }

    public function generate_default_params()
    {
        $this->params = [
            'certificate_number' => '1423 / 2021',
            'course_name' => 'Przykładowy tytuł kursu',
            'student_name' => 'Jan Nowak',
            'student_first_name' => 'Janusz',
            'student_last_name' => 'Nowakowski',
            'certificate_date' => '21.10.2020',
            'course_price' => '19,99',
            'coach_name' => 'Jastrzębski Piotr'
        ];
    }

    public function replace_params()
    {
        $content = $this->get_page();

        foreach ($this->params as $name => $value) {
            $content = str_replace("{" . $name . "}", $value, $content);
        }
        
        $content = apply_filters('certificate_replace_params', $content);

        $this->page_replacement = $content;
    }

    public function get_page()
    {
        return stripslashes(htmlspecialchars_decode($this->page));
    }

    public function set_page($page): void
    {
        $this->page = htmlspecialchars($page);
    }

    public function get_params(): ?array
    {
        return $this->params;
    }

    public function set_params($params)
    {
        $this->params = $params;
    }

    public function set_params_by_user_certificate_id($id)
    {
        $certificate = $this->certificate_repository->find_by_id(new Certificate_ID($id));
        $course = $this->course_repository->find_by_certificate_id($certificate->get_id());
        $product_id = $course->get_product_id();
        $product = $this->product_repository->find($product_id->to_int());
        $author = $this->author_repository->find($course->get_author_id());
        $user = $this->user_repository->find_by_id($certificate->get_user_id());

        $this->set_params([
            'certificate_number' => $certificate->get_certificate_number() ? $certificate->get_certificate_number()->get_value() : '',
            'course_name' => $course->get_title(),
            'student_name' => $user->full_name() ? $user->full_name()->get_full_name() : $user->get_login(),
            'student_first_name' => $user->get_first_name() ?? '',
            'student_last_name' => $user->get_last_name() ?? '',
            'certificate_date' => date_format($certificate->get_created(), 'd.m.Y'),
            'course_price' => number_format_i18n($product->getPrice(), 2),
            'coach_name' => $author->get_name()
        ]);
    }

    public function delete()
    {
        $certificates = $this->get_certificates();
        foreach ($certificates as $key => $certificate) {
            if ($this->id == $certificate['id']) {
                unset($certificates[$key]);
            }
        }
        $this->update_certificates_option($certificates);
    }

    public function get_certificates()
    {
        return $this->option[self::TEMPLATES_OPTION_SLUG];
    }

    private function update_certificates_option($certificates): bool
    {
        $option = $this->option;
        $option[self::TEMPLATES_OPTION_SLUG] = $certificates;
        return $this->update_option($option);
    }

    public function was_installed(): bool
    {
        return $this->get_last_id() > 0;
    }

    private function get_last_id(): int
    {
        return $this->option[self::LAST_ID_OPTION_SLUG] ?? 0;
    }

    public function find_by_id($id): ?self
    {
        foreach ($this->get_certificates() as $certificate) {
            if ($id == $certificate['id']) {
                return $this->load_from_array($certificate);
            }
        }
        return null;
    }

    public function load_from_array($certificate_array): ?self
    {
        $certificate_model = new self();
        $certificate_model->set_id($certificate_array['id']);
        $certificate_model->set_page($certificate_array['page']);
        $certificate_model->set_course_id($certificate_array['course_id']);
        $certificate_model->set_is_default($certificate_array['is_default']);
        $certificate_model->set_name($certificate_array['name']);
        return $certificate_model;
    }

    public function get_edit_url(): string
    {
        return admin_url(self::EDIT_URL . '&id=' . $this->id) . '&' . self::PARAM_NAME_NONCE . '=' . self::generate_nonce();
    }

    public function get_preview_url(): string
    {
        return admin_url(self::PREVIEW_URL . '&id=' . $this->id) . '&' . self::PARAM_NAME_NONCE . '=' . self::generate_nonce();

    }

    public function reset()
    {
        $this->update_option([]);
    }

    public function get_page_replacement()
    {
        return $this->page_replacement;
    }

    public function get_count_certificates()
    {
        return empty($this->get_certificates()) ? 0 : count($this->get_certificates());
    }

    public function set_default_last()
    {
        $last_certificate_templates = $this->get_last();

        if (!is_null($last_certificate_templates)) {
            $last_certificate_templates->set_default();
        }
    }

    public function get_last(): ?Certificate_Template
    {
        $certificate_templates = new Certificate_Template();
        $certificate_templates = $certificate_templates->get_certificates();
        $last_certificate_templates = null;
        foreach ($certificate_templates as $certificate_template) {
            $last_certificate_templates = $certificate_template;
        }

        return $this->load_from_array($last_certificate_templates);
    }

    public function set_default()
    {
        $certificate_templates = new Certificate_Template();
        foreach ($certificate_templates->get_certificates() as $certificate) {
            $certificate_template_model = $certificate_templates->load_from_array($certificate);
            if ($certificate_template_model->id == $this->id) {
                $certificate_template_model->set_is_default(true);
            } else {
                $certificate_template_model->set_is_default(false);
            }
            $certificate_template_model->save();

        }
    }

    public function save(): bool
    {
        if ($this->id) {
            return $this->update();
        }

        return $this->create();
    }

    private function update()
    {
        $certificates = $this->get_certificates();
        foreach ($certificates as $key => $certificate) {
            if ($this->id == $certificate['id']) {
                $certificates[$key] = $this->get_certificate_template_in_array();
            }
        }
        return $this->update_certificates_option($certificates);
    }

    private function get_certificate_template_in_array()
    {
        return [
            'id' => $this->get_id(),
            'page' => $this->get_page(),
            'name' => $this->get_name(),
            'is_default' => $this->get_is_default(),
            'course_id' => $this->get_course_id()
        ];
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id): void
    {
        $this->id = $id;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function set_name($name): void
    {
        $this->name = $name;
    }

    public function get_is_default()
    {
        return $this->is_default;
    }

    public function set_is_default($is_default): void
    {
        $this->is_default = $is_default;
    }

    public function get_course_id()
    {
        return $this->course_id;
    }

    public function set_course_id($course_id): void
    {
        $this->course_id = $course_id;
    }

    private function create(): bool
    {
        $certificates = $this->get_certificates();

        $id = $this->generate_id();
        $this->set_id($id);

        $certificates[] = $this->get_certificate_template_in_array();
        return $this->update_certificates_option($certificates);
    }

    private function generate_id()
    {
        $last_id = $this->get_last_id() ?? 0;
        $last_id = $last_id + 1;

        $this->update_last_id_option($last_id);
        return $last_id;
    }

    private function update_last_id_option($last_id)
    {
        $option = $this->option;
        $option[self::LAST_ID_OPTION_SLUG] = $last_id;
        $this->update_option($option);
    }

    public function set_certificate_repository(Interface_Certificate_Repository $certificate_repository): void
    {
        $this->certificate_repository = $certificate_repository;
    }

    public function set_course_repository(Interface_Readable_Course_Repository $course_repository): void
    {
        $this->course_repository = $course_repository;
    }

    public function set_user_repository(Interface_User_Repository $user_repository): void
    {
        $this->user_repository = $user_repository;
    }

    public function set_product_repository(Product_Repository $product_repository): void
    {
        $this->product_repository = $product_repository;
    }

    public function set_author_repository(Interface_Readable_Author_Repository $author_repository): void
    {
        $this->author_repository = $author_repository;
    }

    public function check_if_name_exists_except_id(string $name, ?int $id = null): bool
    {
        $certificates = $this->find_all();

        /** @var Certificate_Template $certificate */
        foreach ($certificates as $certificate) {

            if ($name == $certificate->get_name() && $id != $certificate->get_id()) {
                return true;
            }
        }

        return false;
    }

    public function find_all(): array
    {
        $models_certificates = [];
        foreach ($this->get_certificates() as $certificate) {
            $models_certificates[] = $this->load_from_array($certificate);
        }
        return $models_certificates;
    }

    public function get_by_id($certificate_template_id): ?self
    {
        if (!$certificate_template_id) {
            return $this->get_default();
        }

        foreach ($this->get_certificates() as $certificate) {
            if ($certificate['id'] == $certificate_template_id->to_int()) {
                return $this->load_from_array($certificate);
            }
        }

        return $this->get_default();
    }

    public function get_default(): ?self
    {
        foreach ($this->get_certificates() as $certificate) {
            if ($certificate['is_default']) {
                return $this->load_from_array($certificate);
            }
        }
        return null;
    }

    public function load_pdf_generator_script()
    {
        wp_register_script('pdf_generator', BPMJ_EDDCM_URL . 'assets/js/pdf-generator.js', [
            'jquery',
        ], BPMJ_EDDCM_VERSION);
        wp_enqueue_script('pdf_generator');

        wp_register_script('pdf_builder_html2pdf',
            'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js');
        wp_enqueue_script('pdf_builder_html2pdf');
    }

}
