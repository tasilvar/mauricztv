<?php

namespace bpmj\wpidea\certificates;

use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\admin\Admin_Certificate_Templates_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;

class Certificate_Template_Actions
{
    private Interface_Url_Generator $url_generator;
    private Interface_Translator $translator;

    public function __construct(
        Interface_Url_Generator $url_generator,
        Interface_Translator $translator
    ) {
        $this->url_generator = $url_generator;
        $this->translator = $translator;

        $this->hooks();
    }

    public function hooks()
    {
        add_action('admin_init', [$this, 'script_loader']);

    }

    public static function check_certificates_permissions()
    {
        return is_admin() && (current_user_can(Caps::CAP_MANAGE_SETTINGS) || current_user_can(Caps::CAP_MANAGE_CERTIFICATES));
    }

    public function script_loader()
    {
        if ($this->is_pdf_builder_page()) {
            $this->load_pdf_builder_script();
        }
        if ($this->is_pdf_generator_page()) {
            $this->load_pdf_generator_script();
        }

    }

    private function is_pdf_builder_page()
    {
        $request = new Current_Request();
        if (!$request->query_arg_exists('page')) {
            return false;
        }

        if (in_array($request->get_query_arg('page'), Certificate_Template::PDF_BUILDER_PAGES)) {
            return true;
        }

        return false;
    }

    public function load_pdf_builder_script()
    {
        wp_register_script('pdf_builder', BPMJ_EDDCM_URL . 'assets/js/pdf-builder.js', [
            'jquery',
        ], BPMJ_EDDCM_VERSION);

        $pdf_builder_text = [
            'stretch_100' => __('Stretch 100%', BPMJ_EDDCM_DOMAIN),
            'stretch_50' => __('Stretch 50%', BPMJ_EDDCM_DOMAIN),
            'to_left' => __('To the left', BPMJ_EDDCM_DOMAIN),
            'move_to_left' => __('Move the item to the left', BPMJ_EDDCM_DOMAIN),
            'to_right' => __('To the right', BPMJ_EDDCM_DOMAIN),
            'move_to_right' => __('Move the item to the right', BPMJ_EDDCM_DOMAIN),
            'center_element' => __('Center element', BPMJ_EDDCM_DOMAIN),
            'center' => __('Center', BPMJ_EDDCM_DOMAIN),
            'delete' => __('Delete', BPMJ_EDDCM_DOMAIN),
            'classic' => __('Classic', BPMJ_EDDCM_DOMAIN),
            'golden' => __('Golden', BPMJ_EDDCM_DOMAIN),
            'cert_preview' => __('Certificate preview', BPMJ_EDDCM_DOMAIN),
            'tips' => __('Tips', BPMJ_EDDCM_DOMAIN),
            'hold_shift' => __('Hold down shift to move the element in one axis only', BPMJ_EDDCM_DOMAIN),
            'use_help_Lines' => __('Use the red help lines', BPMJ_EDDCM_DOMAIN),
            'click_to_style' => __('To style an element, click on the content', BPMJ_EDDCM_DOMAIN),
            'draggable_elements' => sprintf(__('Grab %s to move items', BPMJ_EDDCM_DOMAIN),
                '<span class="pb-grab">✥</span>'),
            'use_flags' => sprintf(__('In elements marked as %s the content cannot be edited, it will be dynamically replaced when generating the certificate for the user. However, it can be styled',
                BPMJ_EDDCM_DOMAIN), '<span class="pg-flag">⚑</span>'),
            'see_full_page' => __('Can\'t see the full page? Zoom out the page by clicking CTRL and minus several times.',
                BPMJ_EDDCM_DOMAIN),
            'variables_available' => __('Variables available', BPMJ_EDDCM_DOMAIN),
            'dynamically_generating_info' => __('They will be dynamically replaced when generating the certificate for the user.',
                BPMJ_EDDCM_DOMAIN),
            'custom_fields' => __('Custom fields', BPMJ_EDDCM_DOMAIN),
            'use_own_fields' => __('You can use your own fields to define the appearance of the certificate.',
                BPMJ_EDDCM_DOMAIN),
            'default_templates' => __('Default templates', BPMJ_EDDCM_DOMAIN),
            'choose_available_template' => __('You can choose one of the available templates and then modify it to suit your needs.',
                BPMJ_EDDCM_DOMAIN),
            'choose_template' => __('Choose a template', BPMJ_EDDCM_DOMAIN),
            'collapse' => __('Collapse', BPMJ_EDDCM_DOMAIN),
            'expand' => __('Expand', BPMJ_EDDCM_DOMAIN),
            'change_positions' => __('Change position', BPMJ_EDDCM_DOMAIN),
            'download_sample_pdf' => __('Download a sample PDF', BPMJ_EDDCM_DOMAIN),
            'save' => __('Save', BPMJ_EDDCM_DOMAIN),
            'certificate_number' => $this->translator->translate('certificates.column.certificate_number'),
            'course_name' => __('Name of the course', BPMJ_EDDCM_DOMAIN),
            'sample_course_name' => __('Sample course title', BPMJ_EDDCM_DOMAIN),
            'student_first_name' => __('Student first name', BPMJ_EDDCM_DOMAIN),
            'student_last_name' => __('Student last name', BPMJ_EDDCM_DOMAIN),
            'student_first_name_last_name' => __('First and last name of the student', BPMJ_EDDCM_DOMAIN),
            'certificate_creation_date' => __('Certificate creation date', BPMJ_EDDCM_DOMAIN),
            'course_price' => __('Course price', BPMJ_EDDCM_DOMAIN),
            'coach_name' => __('Coach first and last name', BPMJ_EDDCM_DOMAIN),
            'choose_background' => __('Choose a background', BPMJ_EDDCM_DOMAIN),
            'add_text_field' => __('Add a field with content', BPMJ_EDDCM_DOMAIN),
            'enter_content' => __('Enter the content', BPMJ_EDDCM_DOMAIN),
            'picture' => __('Picture', BPMJ_EDDCM_DOMAIN),
            'clear' => __('Clear', BPMJ_EDDCM_DOMAIN),
            'move' => __('Move', BPMJ_EDDCM_DOMAIN),
            'exit' => __('Exit', BPMJ_EDDCM_DOMAIN),
            'page_component_title_readonly' => __('Variable (the content will be dynamically replaced when generating the certificate)',
                BPMJ_EDDCM_DOMAIN),
            'select_template_question' => __('Are you sure you want to upload the template? All previous changes will be deleted.',
                BPMJ_EDDCM_DOMAIN),
            'clear_page_question' => __('Are you sure you want to clear the page? All previous changes will be deleted.',
                BPMJ_EDDCM_DOMAIN),
            'exit_page_question' => __('Are you sure you want to exit? All previous changes will be deleted.',
                BPMJ_EDDCM_DOMAIN),
            'small_device_info' => __('Your resolution is too low, try it on your computer', BPMJ_EDDCM_DOMAIN),
            'certificate_template_edited' => __('Certificate template edited!', BPMJ_EDDCM_DOMAIN),
            'home_url' => get_home_url(),
            'settings_page_url' => add_query_arg([
                'page' => Admin_Menu_Item_Slug::SETTINGS,
                'autofocus' => 'certificate'
             ], admin_url('admin.php')),
        ];
        wp_localize_script('pdf_builder', 'pdf_builder_text', $pdf_builder_text);
        wp_enqueue_script('pdf_builder');


        wp_register_script('pdf_builder_editor2',
            'https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js');
        wp_enqueue_script('pdf_builder_editor2');
        wp_register_script('pdf_builder_editor', 'https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js');
        wp_enqueue_script('pdf_builder_editor');

        $this->load_pdf_generator_script();
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

    private function is_pdf_generator_page()
    {
        $request = new Current_Request();

        if (!$request->query_arg_exists('page')) {
            return false;
        }

        if (in_array($request->get_query_arg('page'), Certificate_Template::PDF_GENERATOR_PAGES)) {
            return true;
        }

        return false;
    }

    public function get_set_default_url(int $id): string
    {
        return $this->url_generator->generate(Admin_Certificate_Templates_Controller::class, 'set_default', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $id
        ]);
    }
}
