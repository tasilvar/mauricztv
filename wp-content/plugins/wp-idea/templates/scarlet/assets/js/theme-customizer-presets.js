jQuery(document).ready(function($){
    var loadPresetButton = $("#customize-control-bpmj_eddcm_scarlet_colors_settings-color_preset .load-preset");
    
	$(loadPresetButton).on("click", function() {
		var select2Val = $(this).parent().find('.customize-control-select2').val();
        $(this).parent().find('.customize-control-dropdown-select2').val(select2Val).trigger('change');
        readPresetColors(select2Val);
    });
    
    function readPresetColors( presetName ){
        let preset = eval('bpmj_eddcm_colors_preset_' + presetName);
        $.each( preset, function( colorName, hex ) {
            jQuery("#customize-control-bpmj_eddcm_scarlet_colors_settings-color_" + colorName).find('input.wp-color-picker').wpColorPicker('color', hex);
        });
    }

    function fixUiStyling(){
        loadPresetButton.css('margin', '5px 0 15px');
        $(".customize-control-select2").css('margin', '0');
    }
    fixUiStyling();
});