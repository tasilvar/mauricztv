<?php

namespace bpmj\wpidea\admin\pages\digital_product_editor\core\fields;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use JsonException;

class Files_Field extends Abstract_Setting_Field
{
    private string $save_data_endpoint;

    public function __construct(string $name, string $save_data_endpoint)
    {
        parent::__construct($name);

        $this->save_data_endpoint = $save_data_endpoint;
    }

    /**
     * @throws JsonException
     */
    public function render_to_string(): string
    {
        $json_config = json_encode([
            'save_data_endpoint' => $this->save_data_endpoint,
            'field_name' => $this->get_name(),
            'files' => $this->get_value(),
            'translations' => $this->get_translations(),
        ], JSON_THROW_ON_ERROR);

        return "<div id='product-files-container'></div><script>
            if(typeof tableConfig === 'undefined') {
                let tableConfig;
            }
            
            tableConfig = JSON.parse('" . $json_config . "');
        </script>";
    }

    private function get_translations(): array
    {
        return [
            'priority' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.column.priority'),
            'file_name' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.column.file_name'),
            'file_url' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.column.file_url'),
            'browse_media' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.button.browse_media'),
            'add_file' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.button.add_file'),
            'save' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.button.save'),
            'saving' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.message.saving'),
            'cancel' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.button.cancel'),
            'reset_changes' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.button.reset_changes'),
            'you_have_unsaved_changes' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.message.you_have_unsaved_changes'),
            'be_careful' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.message.be_careful'),
            'active_files' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.message.active_files'),
            'no_active_files' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.message.no_active_files'),
            'save_success' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.message.save_success'),
            'save_error' => Translator_Static_Helper::translate('digital_product_editor.sections.files.table.message.save_error'),
        ];
    }
}