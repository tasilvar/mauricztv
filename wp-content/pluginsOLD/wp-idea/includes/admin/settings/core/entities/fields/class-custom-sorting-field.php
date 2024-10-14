<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\helpers\Product_Sorter_Static_Helper;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use JsonException;

class Custom_Sorting_Field extends Abstract_Setting_Field
{
    private string $save_data_endpoint;
    private array $products;

    public function __construct(string $name, string $save_data_endpoint, array $products)
    {
        parent::__construct($name, $name);

        $this->save_data_endpoint = $save_data_endpoint;
        $this->products           = $products;
    }

	/**
     * @throws JsonException
     */
    public function render_to_string(): string
    {
        $json_config = json_encode([
            'save_data_endpoint' => $this->save_data_endpoint,
            'field_name' => $this->get_name(),
            'contents' => $this->parse_field_value($this->get_value() ?? []),
            'translations' => $this->get_translations(),
            'select_options' => $this->products
        ], JSON_THROW_ON_ERROR);

        return "<div id='custom-sorting-order-container' " . $this->get_depends_on_dataset() . "></div><script>
            function decodeHTMLEntities(text) {
              let textArea = document.createElement('textarea');
              textArea.innerHTML = text;
              return textArea.value;
            }
            
            if (typeof tableConfig === 'undefined') {
                let tableConfig;
            }
            
            tableConfig = JSON.parse('" . $json_config . "');
            for (let i = 0; i < tableConfig.select_options.length; i++){
                tableConfig.select_options[i].name = decodeHTMLEntities(tableConfig.select_options[i].name);
            }   
            
            for (let i = 0; i < tableConfig.contents.length; i++){
                tableConfig.contents[i].product = decodeHTMLEntities(tableConfig.contents[i].product);
            }
            
        </script>";
    }

    private function get_translations(): array
    {
        return [
            'priority' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.column.priority'),
            'product_name' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.column.product_name'),
            'select_product' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.column.select_product'),
            'actions' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.column.actions'),
            'add_product' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.button.add_product'),
            'save' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.button.save'),
            'saving' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.message.saving'),
            'cancel' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.button.cancel'),
            'reset_changes' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.button.reset_changes'),
            'you_have_unsaved_changes' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.message.you_have_unsaved_changes'),
            'be_careful' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.message.be_careful'),
            'selected_products' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.message.selected_products'),
            'no_selected_products' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.message.no_selected_products'),
            'save_success' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.message.save_success'),
            'save_error' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.message.save_error'),
            'product_id' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.message.product_id'),
            'move_up' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.message.move_up'),
            'move_down' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.message.move_down'),
            'move_to_top' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.message.move_to_top'),
            'move_to_bottom' => Translator_Static_Helper::translate('settings.sections.design.custom_order_table.message.move_to_bottom'),
        ];
    }

	private function parse_field_value(array $value): array
	{
        return Product_Sorter_Static_Helper::get_products_by_custom_sorting($value, $this->products);
	}
}