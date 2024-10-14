<?php

namespace bpmj\wpidea\admin\pages\bundle_editor\core\fields;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use JsonException;

class Bundle_Content_Field extends Abstract_Setting_Field
{
    private string $save_data_endpoint;
    private array $select_options;

    public function __construct(string $name, string $save_data_endpoint, array $select_options)
    {
        parent::__construct($name);

        $this->save_data_endpoint = $save_data_endpoint;
        $this->select_options = $select_options;
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
            'select_options' => $this->select_options
        ], JSON_THROW_ON_ERROR);

        return "<div id='bundle-content-container'></div><script>
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
            
        </script>";
    }

    private function get_translations(): array
    {
        return [
            'priority' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.column.priority'),
            'product_name' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.column.product_name'),
            'select_product' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.column.select_product'),
            'add_product' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.button.add_product'),
            'save' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.button.save'),
            'saving' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.message.saving'),
            'cancel' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.button.cancel'),
            'reset_changes' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.button.reset_changes'),
            'you_have_unsaved_changes' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.message.you_have_unsaved_changes'),
            'be_careful' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.message.be_careful'),
            'selected_products' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.message.selected_products'),
            'no_selected_products' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.message.no_selected_products'),
            'save_success' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.message.save_success'),
            'save_error' => Translator_Static_Helper::translate('bundle_editor.sections.bundle_content.message.save_error'),
        ];
    }

    private function parse_field_value(array $value): array
    {
        $parsed = [];

        foreach ($value as $item) {
            $parsed[] = [
                'product' => $item
            ];
        }

        return $parsed;
    }
}