<?php

use bpmj\wpidea\admin\settings\Core_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* @var $this Core_Settings */
$template_settings_api             = $this->get_layout_template_settings_api( $template );
$output_subcolors                  = function ( $main_field, array $sub_fields ) use ( $template_settings_api ) {
	$main_field_value = $template_settings_api->get_detached_option_value( $main_field );
	?>
    <tbody style="display: none;" class="additional-colors-for-<?php echo esc_attr( $main_field ); ?>">
	<?php
	foreach ( $sub_fields as $sub_field ):
		?>
        <tr>
            <th style="padding-left: 40px; color: #808080;"><?php $template_settings_api->show_detached_field_label( $sub_field ); ?></th>
            <td>
				<?php $template_settings_api->show_detached_field( $sub_field ); ?>
                <label style="display: inline-block; line-height: 26px; vertical-align: top;"><input
                            style="vertical-align: middle;" class="slave-setting"
                            data-slave-of="<?php echo esc_attr( $main_field ); ?>"
                            type="checkbox" <?php checked( $template_settings_api->get_detached_option_value( $sub_field ), $main_field_value ); ?> /> <?php _e( 'Use base color', BPMJ_EDDCM_DOMAIN ); ?>
                </label>
            </td>
        </tr>
	<?php
	endforeach;
	?>
    </tbody>
	<?php
};
$wpidea_settings                   = get_option( $this->get_settings_slug() );
$create_show_advanced_options_link = function ( $main_field ) {
	return ' <a style="line-height: 22px; vertical-align: top" href="javascript:;" class="show-additional-colors" data-of="' . esc_attr( $main_field ) . '">' . __( 'Show additional colors', BPMJ_EDDCM_DOMAIN ) . '</a>';
};
?>

<div id="poststuff">
    <div id="post-body" class="columns-2" style="margin-right: 400px;">
        <div id="post-body-content">
            <table class="form-table">
                <tbody>
				<?php $template_settings_api->show_detached_field_row( 'main_font' ); ?>
				<?php $template_settings_api->show_detached_field_row( 'secondary_font' ); ?>
				<?php $template_settings_api->show_detached_field_row( 'login_bg_file' ); ?>
				<?php $template_settings_api->show_detached_field_row( 'bg_file' ); ?>
				<?php $template_settings_api->show_detached_field_row( 'section_bg_file' ); ?>
				<?php $template_settings_api->show_detached_field_row( 'disable_banners' ); ?>
				<?php $template_settings_api->show_detached_field_row( 'css' ); ?>
                </tbody>
            </table>
        </div>
        <br class="clear">
    </div>
</div>
<script type="text/javascript">
	(
		function ( $ ) {
			$( function () {
				var bg_file_keys = [ 'bg_file' ];
				var create_google_font_link_tag = function ( font_id, font_family ) {
					if ( 0 === $( 'link#bpmj-eddcm-google-font-' + font_id ).length ) {
						$( document.head ).append( '<link id="bpmj-eddcm-google-font-' + font_id + '" rel="stylesheet" href="https://fonts.googleapis.com/css?family=' + encodeURIComponent( font_family ) + '"/>' );
					}
				};
				$additional_settings_div = $( '#courses_layout_additional_settings_div' );
				
				$additional_settings_div.find( '.select2.font' ).each( function () {
					var select = $( this ).get( 0 );
					var font_id = select.value;
					var font_family = select.options[ 0 ].label;
					create_google_font_link_tag( font_id, font_family );
					$( this ).after(
						'<input type="hidden" name="' + select.name.replace( /font\]$/, 'font_family]' ) + '" id="' + select.id.replace( /font\]$/, 'font_family]' ) + '" value="' + font_family + '" />'
					);
				} );
				$additional_settings_div.find( '.select2.font' ).select2( {
					ajax: {
						url: bpmj_eddcm.ajax,
						dataType: 'json',
						delay: 250,
						data: function ( params ) {
							return {
								action: 'bpmj_eddcm_search_google_fonts',
								term: params.term
							};
						},
						processResults: function ( data, page ) {
							var font_id, font_family;
							for ( var i = 0, length = data.length; i < length; ++ i ) {
								font_id = data[ i ].id;
								font_family = data[ i ].name;
								create_google_font_link_tag( font_id, font_family );
							}
							return {
								results: data
							};
						},
						minimumInputLength: 2,
						cache: true
					},
					width: "element",
					theme: "bootstrap",
					templateResult: function ( item ) {
						return $( '<span style="' + (
							item.name ? 'font-family: &quot;' + item.name + '&quot;;' : ''
						) + 'font-size: 18px;">' + (
							          item.name || item.text
						          ) + '</span>' );
					},
					templateSelection: function ( item ) {
						var font_family = item.name || item.text;
						return $( '<span style="font-family: &quot;' + font_family + '&quot;; font-size: 18px;">' + font_family + '</span>' );
					}
				} );
				$additional_settings_div.find( '.select2.font' ).on( 'select2:select', function ( e ) {
					var font_family = e.params.data.name;
					var select = e.target;
					document.getElementById( select.id.replace( /font\]$/, 'font_family]' ) ).value = font_family;
				} );
				
				<?php $template_settings_api->script_file(); ?>
			} );
		}( jQuery )
	);
</script>