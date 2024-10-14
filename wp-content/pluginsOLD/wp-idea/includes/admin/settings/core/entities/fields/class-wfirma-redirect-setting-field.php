<?php
namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;

class WFirma_Redirect_Setting_Field extends Abstract_Setting_Field
{
    private string $data = '';

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        ?Additional_Fields_Collection $additional_fields = null,
        $value = null
    )
    {
        parent::__construct($name, $label, $description, $tooltip);
        $this->name = $name;
        $this->label = $label;
        $this->description = $description;
        $this->tooltip = $tooltip;
        $this->additional_fields = $additional_fields;
        $this->value = $value;
    }
    
    public function render_to_string(): string
    {
        if(!$this->is_visible()){
            return '';
        }
        
        return $this->get_field_wrapper_start()
            . $this->get_link_item()
            . $this->render_js_script()
            . $this->get_field_wrapper_end();
    }

    public function set_data(string $data): self
    {
        $this->data = $data;

        return $this;
    }

    private function get_link_item(): string
    {
        return "<a id='" . $this->name . "' data-data='{$this->data}' class='wpi-button wpi-button--clean configuration-button'>" . $this->value . '</a>';
    }
    
    private function render_js_script(): string
    {
        return "
        <script>
           jQuery( document ).ready( function ( $ ) {


                const showHideWfirmaAdditionalFields = () => {
                    const basicHiddenFieldsName = [
                        'wfirma_wf_oauth2_client_id',
                        'wfirma_wf_oauth2_client_secret',
                        'wfirma_wf_oauth2_button_redir',
                        'wfirma_wf_oauth2_authorization_code'
                    ];
                    const oauth2HiddenFieldsName = [
                        'wfirma_wf_login',
                        'wfirma_wf_pass'
                    ];
        
        			var auth_type = null;
        			if($('select[name=\"wfirma_auth_type\"]')) {
        				auth_type = $('select[name=\"wfirma_auth_type\"]').val();
        			}
        			if(!auth_type) {
        				auth_type = 'oauth2';
        			}
        
                    if ('oauth2' === auth_type) {
                        oauth2HiddenFieldsName.map((fieldName) => {
                            $('.single-field-wrapper[data-related-field=\"' + fieldName + '\"]').hide();
                        });
                        basicHiddenFieldsName.map((fieldName) => {
                            $('.single-field-wrapper[data-related-field=\"' + fieldName + '\"]').show();
                        });
                        $('.single-field-wrapper[data-related-field=\"wfirma_wf_oauth2_client_secret\"]').next().show();
                        $('.single-field-wrapper[data-related-field=\"wfirma_wf_pass\"]').next().show();
                    } else {
                        basicHiddenFieldsName.map((fieldName) => {
                            $('.single-field-wrapper[data-related-field=\"' + fieldName + '\"]').hide();
                        });
                        oauth2HiddenFieldsName.map((fieldName) => {
                            $('.single-field-wrapper[data-related-field=\"' + fieldName + '\"]').show();
                        });
                        $('.single-field-wrapper[data-related-field=\"wfirma_wf_oauth2_client_secret\"]').next().hide();
                        $('.single-field-wrapper[data-related-field=\"wfirma_wf_pass\"]').next().hide();
                    }
        		}

                $('select[name=\"wfirma_auth_type\"]').on('change', function () {
			        showHideWfirmaAdditionalFields();
                });
                showHideWfirmaAdditionalFields();


           });
        </script>";
    }
}
