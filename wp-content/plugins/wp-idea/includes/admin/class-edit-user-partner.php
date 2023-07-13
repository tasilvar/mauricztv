<?php

namespace bpmj\wpidea\admin;

use bpmj\wpidea\Caps;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Partner_Repository;
use bpmj\wpidea\modules\affiliate_program\core\services\Affiliate_Link_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_User_Permissions_Service;
use bpmj\wpidea\user\Interface_User_Repository;
use bpmj\wpidea\user\User_ID;
use bpmj\wpidea\user\User_Role_Factory;
use bpmj\wpidea\view\Interface_View_Provider;

class Edit_User_Partner implements Interface_Initiable
{
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private User_Role_Factory $user_role_factory;
    private Interface_User_Permissions_Service $user_permissions_service;
    private Interface_User_Repository $user_repository;
    private Affiliate_Link_Generator $affiliate_link_generator;
    private Interface_Actions $actions;
    private Current_Request $current_request;
    private Interface_Partner_Repository $partner_repository;

    function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        User_Role_Factory $user_role_factory,
        Interface_User_Permissions_Service $user_permissions_service,
        Interface_User_Repository $user_repository,
        Affiliate_Link_Generator $affiliate_link_generator,
        Interface_Actions $actions,
        Current_Request $current_request,
        Interface_Partner_Repository $partner_repository
    ) {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->user_role_factory = $user_role_factory;
        $this->user_permissions_service = $user_permissions_service;
        $this->user_repository = $user_repository;
        $this->affiliate_link_generator = $affiliate_link_generator;
        $this->actions = $actions;
        $this->current_request = $current_request;
        $this->partner_repository = $partner_repository;
    }


    public function init(): void
    {
        $this->actions->add(Action_Name::SHOW_USER_PROFILE, [$this, 'html_display_participant_affiliate_program']);
        $this->actions->add(Action_Name::EDIT_USER_PROFILE, [$this, 'html_display_participant_affiliate_program']);
    }

    /**
     * @throws \bpmj\wpidea\data_types\exceptions\Invalid_Url_Exception
     */
    public function html_display_participant_affiliate_program(): void
    {
        $user_id = $this->current_request->get_query_arg('user_id');

        if (!$user_id) {
            return;
        }

        $user = $this->user_repository->find_by_id(new User_ID($user_id));
        $role = $this->user_role_factory->create_from_name(Caps::ROLE_LMS_PARTNER);

        if (!$this->user_permissions_service->has_role($user, $role)) {
            return;
        }

        $partner = $this->partner_repository->find_by_user_id(new User_ID($user_id));

        if (!$partner) {
            return;
        }

        $link = $this->affiliate_link_generator->get_partner_affiliate_link($partner);

        $affiliates = [
            [
                'partner_id' => $partner->get_affiliate_id()->as_string(),
                'link' => $link->get_value()
            ]
        ];

        echo $this->view_provider->get_admin('/participant-affiliate-program', [
            'affiliates' => $affiliates,
            'translator' => $this->translator
        ]);
    }

}
