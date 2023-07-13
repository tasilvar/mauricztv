<?php

namespace bpmj\wpidea\admin\settings\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\{Additional_Fields_Collection, Fields_Collection};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Checkbox_Setting_Field,
	Configure_Popup_Setting_Field,
	License_Setting_Field,
	Media_Setting_Field,
	Message,
	Number_Setting_Field,
	Select_Setting_Field,
	Text_Area_Setting_Field,
	Text_Setting_Field,
	Toggle_Setting_Field,
	Wysiwyg_Setting_Field};
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\Caps;
use bpmj\wpidea\events\Event_Name;

class General_Settings_Group extends Abstract_Settings_Group
{
    private const RESTRICTED_TO = '_bpmj_eddpc_restricted_to';
    public const BLOG_NAME = 'blogname';
    public const BLOG_DESCRIPTION = 'blogdescription';
    public const PAGE_ON_FRONT = 'page_on_front';
    public const COMMENTS_NOTIFY = 'comments_notify';
    public const MODERATION_NOTIFY = 'moderation_notify';
    public const COMMENT_MODERATION = 'comment_moderation';
    public const COMMENT_PREVIOUSLY_APPROVED = 'comment_previously_approved';
    public const ADMIN_NOTICE_EMAILS = 'admin_notice_emails';
    public const LICENSE_KEY = 'license_key';
    private const LOGO = 'logo';
    private const FAVICON = 'favicon';
    private const PROFILE_EDITOR_PAGE = 'profile_editor_page';
    private const PAGE_TO_REDIRECT_TO_AFTER_LOGIN = 'page_to_redirect_to_after_login';
    private const CONTACT_PAGE_POPUP = 'contact_page_popup';
    private const RECAPTCHA_SITE_KEY = 'recaptcha_site_key';
    private const RECAPTCHA_SECRET_KEY = 'recaptcha_secret_key';
    private const CONTACT_PAGE = 'contact_page';
    private const COMMENT_MANAGEMENT = 'comment_management';
    private const CONTACT_EMAIL = 'contact_email';
    private const FOOTER = 'footer';
    private const FOOTER_HTML = 'footer_html';
    private const COOKIE_BAR = 'cookie-bar';
    private const COOKIE_BAR_PRIVACY_POLICY = 'cookie-bar-privacy-policy';
    private const COOKIE_BAR_CONTENT = 'cookie-bar-content';
    private const COOKIE_BAR_BUTTON_TEXT = 'cookie-bar-button-text';
    private const NEW_SALE_NOTIFICATIONS = 'new_sale_notifications';
    private const ADMIN_NOTICE_POLICY = 'bpmj_eddcm_admin_notice_policy';
    private const LOGO_ALLOWED_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif'];
    private const FAVICON_ALLOWED_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'ico'];
    private const DELIVERY_PRICE = 'delivery_price';
    private const DELIVERY_POPUP = 'delivery_popup';
    private const DELIVERY_PROVIDER = 'delivery_provider';
    private const DEFAULT_DELIVERY_PRICE = 0;
    private const MIN_DELIVERY_PRICE = 0;
    private const MAX_DELIVERY_PRICE = 9999;

    private Subscription $subscription;

    public function __construct(
        Subscription $subscription
    )
    {
        $this->subscription = $subscription;
    }

    public function get_name(): string
    {
        return 'general';
    }

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translator->translate('settings.sections.general.fieldset.service'),
            (new Fields_Collection())
                ->add($this->get_blog_name_field())
                ->add($this->get_blog_description_field()),
            $this->active_for_current_user([
                Caps::ROLE_LMS_ADMIN
            ])
        );

        $should_fieldset_be_visible = !$this->subscription->is_go();
        $this->add_fieldset(
            $this->translator->translate('settings.sections.general.fieldset.license'),
            (new Fields_Collection())
                ->add($this->get_license_key_field()),
            $should_fieldset_be_visible
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.general.fieldset.branding'),
            (new Fields_Collection())
                ->add($this->get_logo_field())
                ->add($this->get_favicon_field())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.general.fieldset.functional_pages'),
            (new Fields_Collection())
                ->add($this->get_page_on_front_field())
                ->add($this->get_my_account_field())
                ->add($this->get_after_logging_in_field())
                ->add($this->get_contact_page_configuration_popup())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.general.fieldset.comment_management'),
            (new Fields_Collection())
                ->add($this->get_comment_management_field()),
            $this->active_for_current_user([
                Caps::ROLE_LMS_ADMIN,
                Caps::ROLE_LMS_SUPPORT
            ])
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.general.fieldset.email'),
            (new Fields_Collection())
                ->add($this->get_contact_email_field())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.general.fieldset.footer'),
            (new Fields_Collection())
                ->add($this->get_footer_content_field())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.general.fieldset.cookie_bar'),
            (new Fields_Collection())
                ->add($this->get_cookie_bar_field())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.general.fieldset.new_sale_notifications'),
            (new Fields_Collection())
                ->add($this->get_new_sale_notifications_field())
        );

        if ($this->app_settings->get(Settings_Const::PHYSICAL_PRODUCTS_ENABLED)) {
            $this->add_fieldset(
                $this->translator->translate('settings.sections.general.fieldset.delivery'),
                (new Fields_Collection())
                    ->add($this->get_delivery_field())
            );
        }
    }

    private function get_blog_name_field(): Text_Setting_Field
    {
        return new Text_Setting_Field(
            self::BLOG_NAME,
            $this->translator->translate('settings.sections.general.blog_name'),
            null,
            $this->translator->translate('settings.sections.general.blog_name.tooltip')
        );
    }

    private function get_blog_description_field(): Text_Setting_Field
    {
        return new Text_Setting_Field(
            self::BLOG_DESCRIPTION,
            $this->translator->translate('settings.sections.general.blog_description'),
            null,
            $this->translator->translate('settings.sections.general.blog_description.tooltip')
        );
    }

    private function get_license_key_field(): License_Setting_Field
    {
        $field = new License_Setting_Field(
            self::LICENSE_KEY,
            $this->translator->translate('settings.sections.general.license_key')
        );
        $field->set_sanitize_callback(function ($value) {
            global $courses_manager_settings;
            $license = trim($value);
            if (!empty($courses_manager_settings['license_key']) && $courses_manager_settings['license_key'] === $license) {
                return $license;
            }
            $license_status = bpmj_eddcm_check_license($license, BPMJ_EDDCM_NAME, 'bpmj_eddcm_license_status');
            if ('valid' === $license_status) {
                delete_option('wpidea_package');
                update_option('bmpj_wpidea_vkey', $license);
                do_action(Event_Name::NEW_VALID_LICENSE_HAS_BEEN_ENTERED, $license);
            }
            if (!empty($license) && false === $license_status) {
                update_option('bpmj_wpidea_license_connection_error', true);
            } else {
                delete_option('bpmj_wpidea_license_connection_error');
            }
            return $license;
        });
        return $field;
    }

    private function get_logo_field(): Media_Setting_Field
    {
        $field = new Media_Setting_Field(
            self::LOGO,
            $this->translator->translate('settings.sections.general.logo'),
            null,
            $this->translator->translate('settings.sections.general.logo.tooltip')
        );
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();
            if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                $results->add_error_message('settings.field.validation.invalid_url');
            }

            if (!empty($value) && !in_array(pathinfo($value, PATHINFO_EXTENSION), General_Settings_Group::LOGO_ALLOWED_EXTENSIONS)) {
                $results->add_error_message('settings.field.validation.invalid_extension');
            }

            return $results;
        });
        return $field;
    }

    private function get_favicon_field(): Media_Setting_Field
    {
        $field = new Media_Setting_Field(
            self::FAVICON,
            $this->translator->translate('settings.sections.general.favicon'),
            null,
            $this->translator->translate('settings.sections.general.favicon.tooltip')
        );
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();
            if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                $results->add_error_message('settings.field.validation.invalid_url');
            }

            if (!empty($value) && !in_array(pathinfo($value, PATHINFO_EXTENSION), General_Settings_Group::FAVICON_ALLOWED_EXTENSIONS)) {
                $results->add_error_message('settings.field.validation.invalid_extension');
            }

            return $results;
        });
        return $field;
    }

    private function get_page_on_front_field(): Select_Setting_Field
    {
        $field = new Select_Setting_Field(
            self::PAGE_ON_FRONT,
            $this->translator->translate('settings.sections.general.page_on_front'),
            null,
            $this->translator->translate('settings.sections.general.page_on_front.tooltip'),
            null,
            $this->get_all_pages()
        );

        $field->change_visibility(
            $this->active_for_current_user([
                Caps::ROLE_LMS_ADMIN
            ])
        );

        return $field;
    }

    private function get_my_account_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::PROFILE_EDITOR_PAGE,
            $this->translator->translate('settings.sections.general.my_account'),
            null,
            $this->translator->translate('settings.sections.general.my_account.tooltip'),
            null,
            $this->get_all_pages()
        );
    }

    private function get_after_logging_in_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::PAGE_TO_REDIRECT_TO_AFTER_LOGIN,
            $this->translator->translate('settings.sections.general.after_logging_in'),
            null,
            $this->translator->translate('settings.sections.general.after_logging_in.tooltip'),
            null,
            $this->get_all_pages()
        );
    }

    private function get_contact_page_configuration_popup(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::CONTACT_PAGE_POPUP,
            $this->translator->translate('settings.sections.general.contact_page'),
            null,
            $this->translator->translate('settings.sections.general.contact_page.tooltip'),
            (new Additional_Fields_Collection())
                ->add(new Message($this->translator->translate('settings.sections.general.recaptcha.popup.additional_information')))
                ->add($this->get_site_key_field())
                ->add($this->get_secret_key_field())
                ->add(new Message($this->translator->translate('settings.sections.general.recaptcha.popup.contact_page.additional_information')))
                ->add($this->get_contact_page_field())
        ))->set_popup($this->settings_popup, $this->translator->translate('settings.sections.general.contact_page.popup.title'));
    }

    private function get_site_key_field(): Text_Setting_Field
    {
        $field = new Text_Setting_Field(
            self::RECAPTCHA_SITE_KEY,
            $this->translator->translate('settings.sections.general.recaptcha.site_key')
        );

        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if (empty($value)) {
                $results->add_error_message('settings.sections.general.recaptcha.site_key.empty');
            }

            return $results;
        });
        return $field;

    }

    private function get_secret_key_field(): Text_Setting_Field
    {
        $field = new Text_Setting_Field(
            self::RECAPTCHA_SECRET_KEY,
            $this->translator->translate('settings.sections.general.recaptcha.secret_key')
        );

        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if (empty($value)) {
                $results->add_error_message('settings.sections.general.recaptcha.secret_key.empty');
            }

            return $results;
        });
        return $field;
    }

    private function get_contact_page_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::CONTACT_PAGE,
            $this->translator->translate('settings.sections.general.contact_page'),
            null,
            $this->translator->translate('settings.sections.general.contact_page.tooltip'),
            null,
            $this->get_all_pages()
        );
    }

    private function get_comment_management_field(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::COMMENT_MANAGEMENT,
            $this->translator->translate('settings.sections.general.comment_management'),
            null,
            $this->translator->translate('settings.sections.general.comment_management.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_notify_of_new_comments_field())
                ->add($this->get_notify_of_new_comments_pending_moderation_field())
                ->add($this->get_moderation_of_comments_field())
                ->add($this->get_allow_comments_from_trusted_authors_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.general.comment_management.popup.title'));
    }

    private function get_notify_of_new_comments_field(): Checkbox_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::COMMENTS_NOTIFY,
            $this->translator->translate('settings.sections.general.comments_notify'),
            null,
            $this->translator->translate('settings.sections.general.comments_notify.tooltip')
        );
    }

    private function get_notify_of_new_comments_pending_moderation_field(): Checkbox_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::MODERATION_NOTIFY,
            $this->translator->translate('settings.sections.general.moderation_notify'),
            null,
            $this->translator->translate('settings.sections.general.moderation_notify.tooltip')
        );
    }

    private function get_moderation_of_comments_field(): Checkbox_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::COMMENT_MODERATION,
            $this->translator->translate('settings.sections.general.comment_moderation'),
            null,
            $this->translator->translate('settings.sections.general.comment_moderation.tooltip')
        );
    }

    private function get_allow_comments_from_trusted_authors_field(): Checkbox_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::COMMENT_PREVIOUSLY_APPROVED,
            $this->translator->translate('settings.sections.general.comment_previously_approved'),
            null,
            $this->translator->translate('settings.sections.general.comment_previously_approved.tooltip')
        );
    }

    private function get_contact_email_field(): Text_Setting_Field
    {
        $field = new Text_Setting_Field(
            self::CONTACT_EMAIL,
            $this->translator->translate('settings.sections.general.contact_email'),
            $this->translator->translate('settings.sections.general.contact_email.desc'),
            $this->translator->translate('settings.sections.general.contact_email.tooltip')
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $results->add_error_message('settings.field.validation.invalid_email');
            }
            return $results;
        });
        return $field;
    }

    private function get_footer_content_field(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::FOOTER,
            $this->translator->translate('settings.sections.general.footer'),
            null,
            $this->translator->translate('settings.sections.general.footer.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_footer_content_additional_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.general.footer.popup.title'));
    }

    private function get_footer_content_additional_field(): Wysiwyg_Setting_Field
    {
        return new Wysiwyg_Setting_Field(
            self::FOOTER_HTML,
            $this->translator->translate('settings.sections.general.footer.popup.footer_html'),
            null,
            $this->translator->translate('settings.sections.general.footer.popup.footer_html.tooltip')

        );
    }

    private function get_cookie_bar_field(): Toggle_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::COOKIE_BAR,
            $this->translator->translate('settings.sections.general.cookie_bar'),
            null,
            $this->translator->translate('settings.sections.general.cookie_bar.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_privacy_policy_additional_field())
                ->add($this->get_cookie_bar_content_additional_field())
                ->add($this->get_button_cookie_bar_additional_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.general.cookie_bar.popup.title'));
    }


    private function get_privacy_policy_additional_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::COOKIE_BAR_PRIVACY_POLICY,
            $this->translator->translate('settings.sections.general.cookie_bar.popup.privacy_policy'),
            $this->translator->translate('settings.sections.general.cookie_bar.popup.privacy_policy.desc'),
            $this->translator->translate('settings.sections.general.cookie_bar.popup.privacy_policy.tooltip'),
            null,
            $this->get_all_pages()
        );
    }

    private function get_cookie_bar_content_additional_field(): Wysiwyg_Setting_Field
    {
        $field = new Wysiwyg_Setting_Field(
            self::COOKIE_BAR_CONTENT,
            $this->translator->translate('settings.sections.general.cookie_bar.popup.content'),
            null,
            $this->translator->translate('settings.sections.general.cookie_bar.popup.content.tooltip'),
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();
            if (empty($value)) {
                $results->add_error_message('settings.field.validation.cant_be_empty');
            }
            return $results;
        });
        return $field;
    }

    private function get_button_cookie_bar_additional_field(): Text_Setting_Field
    {
        $field = new Text_Setting_Field(
            self::COOKIE_BAR_BUTTON_TEXT,
            $this->translator->translate('settings.sections.general.cookie_bar.popup.button_text'),
            null,
            $this->translator->translate('settings.sections.general.cookie_bar.popup.button_text.tooltip')
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();
            if (empty($value)) {
                $results->add_error_message('settings.field.validation.cant_be_empty');
            }
            return $results;
        });
        return $field;
    }

    private function get_new_sale_notifications_field(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::NEW_SALE_NOTIFICATIONS,
            $this->translator->translate('settings.sections.general.new_sale_notifications'),
            null,
            $this->translator->translate('settings.sections.general.new_sale_notifications.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_admin_notice_policy_additional_field())
                ->add($this->get_email_addresses_sale_notifications_additional_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.general.new_sale_notifications.popup.title'));
    }

    private function get_admin_notice_policy_additional_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::ADMIN_NOTICE_POLICY,
            $this->translator->translate('settings.sections.general.new_sale_notifications.popup.admin_notice_policy'),
            null,
            $this->translator->translate('settings.sections.general.new_sale_notifications.popup.admin_notice_policy.tooltip'),
            null,
            $this->get_admin_notice_policy_options()
        );
    }

    private function get_email_addresses_sale_notifications_additional_field(): Text_Area_Setting_Field
    {
        $field = new Text_Area_Setting_Field(
            self::ADMIN_NOTICE_EMAILS,
            $this->translator->translate('settings.sections.general.new_sale_notifications.popup.admin_notice_emails'),
            $this->translator->translate('settings.sections.general.new_sale_notifications.popup.admin_notice_emails.desc'),
            $this->translator->translate('settings.sections.general.new_sale_notifications.popup.admin_notice_emails.tooltip')
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {

            $emails = array_map('trim', explode("\n", $value));

            $return_error = false;
            foreach($emails as $email){
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $return_error = true;
                }
            }
            $results = new Setting_Field_Validation_Result();

            if($return_error){
                $results->add_error_message('settings.field.validation.invalid_email');
            }

            return $results;
        });
        return $field;
    }

    private function get_all_pages(): array
    {
        $all_pages = get_pages([
            'hierarchical' => false,
            'post_type' => 'page'
        ]);

        $all_pages_new = [$this->translator->translate('settings.field.select.choose')];

        foreach ($all_pages as $page) {

            if ('publish' !== $page->post_status || $this->is_course_page($page->ID)) {
                continue;
            }
            $all_pages_new[$page->ID] = $page->post_title;
        }

        return $all_pages_new;
    }

    private function is_course_page(int $post_id): bool
    {
        return !empty(get_post_meta($post_id, self::RESTRICTED_TO, true));
    }

    private function get_admin_notice_policy_options(): array
    {
        return [
            'disabled' => $this->translator->translate('settings.sections.general.option.admin_notice_policy.disabled'),
            'comments' => $this->translator->translate('settings.sections.general.option.admin_notice_policy.comments'),
            'all' => $this->translator->translate('settings.sections.general.option.admin_notice_policy.all'),
        ];
    }

    private function get_delivery_field(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::DELIVERY_POPUP,
            $this->translator->translate('settings.sections.general.delivery_price'),
            null,
            $this->translator->translate('settings.sections.general.delivery_price.tooltip'),
            (new Additional_Fields_Collection())
            ->add($this->get_delivery_price_additional_field())
            ->add($this->get_delivery_provider_additional_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.general.delivery_price.popup.title'));
    }

    private function get_delivery_price_additional_field(): Number_Setting_Field
    {
        $field = new Number_Setting_Field(
            self::DELIVERY_PRICE,
            $this->translator->translate('settings.sections.general.delivery_price.popup.delivery_price'),
            $this->translator->translate('settings.sections.general.delivery_price.popup.delivery_price.desc'),
            $this->translator->translate('settings.sections.general.delivery_price.popup.delivery_price.tooltip'),
            null,
            self::DEFAULT_DELIVERY_PRICE
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();
            if (!is_numeric($value) || (float)$value < General_Settings_Group::MIN_DELIVERY_PRICE || (float)$value > General_Settings_Group::MAX_DELIVERY_PRICE) {
                $results->add_error_message('settings.sections.general.delivery_price.popup.delivery_price.validation');
            }
            return $results;
        });
        return $field;
    }

    private function get_delivery_provider_additional_field()
    {
        return new Text_Setting_Field(
            self::DELIVERY_PROVIDER,
            $this->translator->translate('settings.sections.general.delivery_price.popup.delivery_provider'),
            $this->translator->translate('settings.sections.general.delivery_price.popup.delivery_provider.desc'),
            $this->translator->translate('settings.sections.general.delivery_price.popup.delivery_provider.tooltip'),
        );
    }
}