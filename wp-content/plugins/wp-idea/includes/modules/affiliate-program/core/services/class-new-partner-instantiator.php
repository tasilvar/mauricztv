<?php

namespace bpmj\wpidea\modules\affiliate_program\core\services;

use bpmj\wpidea\Caps;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Partner_Repository;
use bpmj\wpidea\user\User_ID;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\admin\helpers\utils\Snackbar;
use bpmj\wpidea\translator\Interface_Translator;

class New_Partner_Instantiator
{
    private const USER_CREATED_HOOK = 'user_register';
    private const SET_USER_ROLE_HOOK = 'set_user_role';
    private const PARTNER_GET_PARAMETER = 'partner';
    private const REDIRECT_FILTER = 'wp_redirect';

    private Current_Request $current_request;
    private Interface_Actions $actions;
    private Interface_Partner_Repository $partner_repository;
    private Interface_Url_Generator $url_generator;
    private Interface_Filters $filters;
    private Snackbar $snackbar;
    private Interface_Translator $translator;

    public function __construct(
        Current_Request $current_request,
        Interface_Actions $actions,
        Interface_Partner_Repository $partner_repository,
        Interface_Url_Generator $url_generator,
        Interface_Filters $filters,
        Snackbar $snackbar,
        Interface_Translator $translator
    ) {
        $this->current_request = $current_request;
        $this->actions = $actions;
        $this->partner_repository = $partner_repository;
        $this->url_generator = $url_generator;
        $this->filters = $filters;
        $this->snackbar = $snackbar;
        $this->translator = $translator;
    }

    public function init(): void
    {
        $this->actions->add(self::USER_CREATED_HOOK, [$this, 'user_register_with_partner_role']);
        $this->actions->add(self::USER_CREATED_HOOK, [$this, 'redirect_to_partners_page']);
        $this->actions->add(self::SET_USER_ROLE_HOOK, [$this, 'maybe_create_new_partner'], 10, 2);
    }

    public function user_register_with_partner_role(int $user_id): void
    {
        if ($this->current_request->query_arg_exists(self::PARTNER_GET_PARAMETER)) {
            $wp_user_object = new \WP_User($user_id);
            $wp_user_object->set_role(Caps::ROLE_LMS_PARTNER);
        }
    }


    public function redirect_to_partners_page(int $user_id): void
    {
        if (!$this->current_request->query_arg_exists(self::PARTNER_GET_PARAMETER)) {
            return;
        }

        $this->filters->add(self::REDIRECT_FILTER, function($location) {
            return $this->url_generator->generate_admin_page_url('admin.php', [
                'page' => Admin_Menu_Item_Slug::AFFILIATE_PROGRAM_PARTNERS
            ]);
        });
        
        $this->snackbar->display_message_on_next_request(
            $this->translator->translate('affiliate_program.actions.add_partner.success')
        );
    }

    public function maybe_create_new_partner(int $user_id, string $role): void
    {
        if ($role !== Caps::ROLE_LMS_PARTNER) {
            return;
        }

        $partner = $this->partner_repository->find_by_user_id(new User_ID($user_id));
        if ($partner) {
            return;
        }

        $partner = $this->partner_repository->create_partner_model_from_user($user_id);
        $this->partner_repository->create($partner);
    }
}