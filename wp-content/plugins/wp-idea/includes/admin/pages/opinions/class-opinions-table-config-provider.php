<?php

namespace bpmj\wpidea\admin\pages\opinions;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Status;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;

class Opinions_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_opinions_table';
    private Dynamic_Tables_Module $dynamic_tables_module;
    private Opinions_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
	private Interface_Product_Repository $product_repository;

	public function __construct(
        Dynamic_Tables_Module $dynamic_tables_module,
        Opinions_Table_Data_Provider $data_provider,
        Interface_Translator $translator,
	    Interface_Product_Repository $product_repository
    )
    {
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->data_provider = $data_provider;
        $this->translator = $translator;
	    $this->product_repository = $product_repository;
    }

    public function get_config(): Dynamic_Table_Config
    {
        $config = $this->dynamic_tables_module->create_table_config(
            $this->get_table_id(),
            $this->data_provider,
            $this->get_columns_config()
        );

        $config->set_row_actions($this->get_row_actions());
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
                'property' => 'product_name',
                'label' => $this->translator->translate('opinions.column.product_name'),
                'type' => 'text',
	            'filter' => 'multiselect',
                'filter_options' => $this->get_product_filter_values()
            ],
            [
                'property' => 'user_name',
                'label' => $this->translator->translate('opinions.column.user_name'),
                'type' => 'text',
                'filter' => 'text'
            ],
            [
                'property' => 'user_email',
                'label' => $this->translator->translate('opinions.column.user_email'),
                'type' => 'text',
                'filter' => 'text'
            ],
            [
                'property' => 'opinion_rating',
                'label' => $this->translator->translate('opinions.column.opinion_rating'),
                'type' => 'text',
                'filter' => 'multiselect',
                'filter_options' => $this->get_rating_filter_options()
            ],
            [
                'property' => 'opinion_content',
                'label' => $this->translator->translate('opinions.column.opinion_content'),
                'type' => 'longtext',
                'filter' => 'text',
                'max_length' => 70,
            ],
            [
                'property' => 'date_of_issue',
                'label' => $this->translator->translate('opinions.column.date_of_issue'),
                'type' => 'date',
                'filter' => 'date_range',
            ],
            [
                'property' => 'status',
                'label' => $this->translator->translate('opinions.column.status'),
                'type' => 'status',
                'use_json_property_as_label' => 'status_label',
                'filter' => 'multiselect',
                'filter_options' => $this->get_status_filter_options()
            ],
        ];
    }

	private function get_status_filter_options(): array
	{
		$options = [];

		foreach (Opinion_Status::ALL_STATUSES as $status) {
			$options[] = [
				'value' => $status,
				'label' => $this->translator->translate("opinions.status.{$status}"),
			];
		}

		return $options;
	}
	private function get_rating_filter_options(): array
	{
		$options = [];

		foreach ([1,2,3,4,5] as $value) {
			$label = $value . '⭐';

			$options[] = [
				'value' => $value,
				'label' => $label,
			];
		}

		return $options;
	}

	private function get_product_filter_values(): array
	{
		$options = $this->product_repository->find_all()->to_array();
		foreach ($options as $key => $option) {
			$encoded_name = htmlspecialchars($option['name'], ENT_QUOTES);

			unset($options[$key]['name']);

			$options[$key]['label'] = $encoded_name;
			$options[$key]['value'] = $option['id'];
		}

		return $options;
	}

    private function get_row_actions(): array
    {
        return [
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('opinions.action.accept_opinion'),
                'class' => 'accept-opinion',
                'use_json_property_as_url' => 'accept_opinion',
                'status_name' => 'status',
                'disable_when_status_value_is_one_of' => [Opinion_Status::ACCEPTED],
            ],
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('opinions.action.discard_opinion'),
                'class' => 'discard-opinion',
                'use_json_property_as_url' => 'discard_opinion',
                'status_name' => 'status',
                'disable_when_status_value_is_one_of' => [Opinion_Status::DISCARDED],
            ],
        ];
    }
}