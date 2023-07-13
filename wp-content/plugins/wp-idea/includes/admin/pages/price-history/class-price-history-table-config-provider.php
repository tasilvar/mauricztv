<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\price_history;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\sales\price_history\core\model\value_objects\Type_Of_Price;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;

class Price_History_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_price_history_table';

    private Dynamic_Tables_Module $dynamic_tables_module;
    private Price_History_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private Interface_Product_Repository $product_repository;

    public function __construct(
        Dynamic_Tables_Module $dynamic_tables_module,
        Price_History_Table_Data_Provider $data_provider,
        Interface_Translator $translator,
        Interface_Product_Repository $product_repository
    ) {
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->data_provider = $data_provider;
        $this->translator = $translator;
        $this->product_repository = $product_repository;
    }

    public function get_config(): Dynamic_Table_Config
    {
        return $this->dynamic_tables_module->create_table_config(
            $this->get_table_id(),
            $this->data_provider,
            $this->get_columns_config()
        );
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
                'label' => $this->translator->translate('price_history.column.id'),
                'type' => 'id',
                'prefix' => '#',
                'sortable' => false
            ],
            [
                'property' => 'product_name',
                'type' => 'text',
                'label' => $this->translator->translate('price_history.column.product_name'),
                'sortable' => false,
                'filter' => 'multiselect',
                'filter_options' => $this->get_product_filter_values(),
            ],
            [
                'property' => 'price',
                'type' => 'text',
                'label' => $this->translator->translate('price_history.column.price'),
                'sortable' => false,
                'filter' => 'number_range',
            ],
            [
                'property' => 'type_of_price',
                'type' => 'text',
                'label' => $this->translator->translate('price_history.column.type_of_price'),
                'sortable' => false,
                'filter' => 'multiselect',
                'filter_options' => $this->get_type_of_price_filter_values(),
            ],
            [
                'property' => 'changed_at',
                'type' => 'date',
                'label' => $this->translator->translate('price_history.column.date_of_change'),
                'sortable' => false,
                'filter' => 'date_range',
            ],
        ];
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

    private function get_type_of_price_filter_values(): array
    {
        $options = [];

        foreach (Type_Of_Price::ALL_TYPES as $type) {
            $options[] = [
                'label' => $this->translator->translate('price_history.type_of_price.' . $type),
                'value' => $type,
            ];
        }

        return $options;
    }
}