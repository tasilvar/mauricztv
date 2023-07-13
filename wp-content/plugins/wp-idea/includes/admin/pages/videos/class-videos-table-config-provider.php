<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\videos;

use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\modules\videos\api\controllers\Video_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\Caps;
use bpmj\wpidea\user\User_Role_Factory;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;

class Videos_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_video_table';

    private Dynamic_Tables_Module $dynamic_tables_module;
    private Videos_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private User_Role_Factory $user_role_factory;

    public function __construct(
        Dynamic_Tables_Module $dynamic_tables_module,
        Videos_Table_Data_Provider $data_provider,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        User_Role_Factory $user_role_factory
    ) {
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->data_provider = $data_provider;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->user_role_factory = $user_role_factory;
    }

    public function get_config(): Dynamic_Table_Config
    {
        $config = $this->dynamic_tables_module->create_table_config(
            $this->get_table_id(),
            $this->data_provider,
            $this->get_columns_config()
        );

        $config->disable_multi_sort();
        $config->disable_export();
        $config->set_bulk_actions($this->get_bulk_actions());
        $config->set_row_actions($this->get_row_actions());
        $config->set_top_panel_buttons($this->get_top_panel_buttons());
        $config->set_required_roles($this->user_role_factory->create_many_from_name(Caps::ROLES_ADMINS_SUPPORT));

        return $config;
    }

    public function get_table_id(): string
    {
        return self::TABLE_ID;
    }

    private function get_columns_config(): array
    {
        return [
            [
                'property' => 'id',
                'label' => $this->translator->translate('videos.column.id'),
                'type' => 'id'
            ],
            [
                'property' => 'title',
                'filter' => 'text',
                'label' => $this->translator->translate('videos.column.name')
            ],
            [
                'property' => 'size',
                'label' => $this->translator->translate('videos.column.file_size'),
                'filter' => 'number_range'
            ],
            [
                'property' => 'length',
                'label' => $this->translator->translate('videos.column.length')
            ],
            [
                'property' => 'created_at',
                'label' => $this->translator->translate('videos.column.date_created'),
                'type' => 'date',
                'filter' => 'date_range'
            ],
        ];
    }

    private function get_row_actions(): array
    {
        return [
            [
                'type' => 'link',
                'label' => $this->translator->translate('videos.column.actions.edit_settings'),
                'class' => 'edit-settings',
                'use_json_property_as_url' => 'edit_settings'
            ],
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('videos.column.actions.delete'),
                'class' => 'delete-video',
                'use_json_property_as_url' => 'delete_video',
                'loading_message' => $this->translator->translate('videos.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('videos.actions.delete.confirm')
            ]
        ];
    }

    private function get_top_panel_buttons(): array
    {
        return [
            [
                'target' => $this->url_generator->generate_admin_page_url('admin.php', [
                    'page' => Admin_Menu_Item_Slug::VIDEO_UPLOADER
                ]),
                'label' => $this->translator->translate('videos.column.actions.add_video'),
                'class' => 'add-video-button'
            ]
        ];
    }

    private function get_bulk_actions(): array
    {
        return [
            [
                'label' => $this->translator->translate('videos.actions.delete.bulk'),
                'class' => 'delete-video',
                'url' => $this->url_generator->generate(Video_Controller::class, 'delete_bulk', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]),
                'loading_message' => $this->translator->translate('videos.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('videos.actions.delete.bulk.confirm')
            ]
        ];
    }
}