<?php

use bpmj\wpidea\admin\settings\Core_Settings;
use bpmj\wpidea\templates_system\admin\modules\settings_handlers\Interface_Templates_Settings_Handler;
use bpmj\wpidea\templates_system\admin\modules\Interface_Templates_System_Modules_Factory;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/** @var Interface_Templates_Settings_Handler $templates_settings_handler */
$templates_settings_handler = WPI()->container->get(Interface_Templates_System_Modules_Factory::class)->get_settings_handler();

/* @var $this Core_Settings */
$template_settings_api	 = $this->get_layout_template_settings_api( $template );
$output_subcolors	 = function ($main_field, array $sub_fields) use ($template_settings_api) {
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
					<label style="display: inline-block; line-height: 26px; vertical-align: top;"><input style="vertical-align: middle;" class="slave-setting" data-slave-of="<?php echo esc_attr( $main_field ); ?>" type="checkbox" <?php checked( $template_settings_api->get_detached_option_value( $sub_field ), $main_field_value ); ?> /> <?php _e( 'Use base color', BPMJ_EDDCM_DOMAIN ); ?></label>
				</td>
			</tr>
			<?php
		endforeach;
		?>
	</tbody>
	<?php
};
$wpidea_settings = get_option( $this->get_settings_slug() );
$create_show_advanced_options_link = function ($main_field) {
	return ' <a style="line-height: 22px; vertical-align: top" href="javascript:;" class="show-additional-colors" data-of="' . esc_attr( $main_field ) . '">' . __( 'Show additional colors', BPMJ_EDDCM_DOMAIN ) . '</a>';
};
?>

<div id="poststuff">
	<div id="post-body" class="columns-2" style="margin-right: 400px;">
		<div id="post-body-content">
			<table class="form-table">
                <tbody style="<?= $templates_settings_handler->legacy_templates_settings_in_use() ? '' : 'display: none' ?>">
                        <?php $template_settings_api->show_detached_field_row( 'main_font' ); ?>
                        <?php $template_settings_api->show_detached_field_row( 'secondary_font' ); ?>
                        <?php $template_settings_api->show_detached_field_row( 'bg_file' ); ?>
                        <?php $template_settings_api->show_detached_field_row( 'section_bg_file' ); ?>
                        <?php $template_settings_api->show_detached_field_row( 'login_bg_file' ); ?>
                </tbody>
                <tbody>
                    <?php $template_settings_api->show_detached_field_row( 'color_preset' ); ?>
                    <?php $template_settings_api->show_detached_field_row( 'bg_color', array( 'post_field' => $create_show_advanced_options_link( 'bg_color' ) ) ); ?>
                </tbody>
				<?php $output_subcolors( 'bg_color', array( 'highlight_color', 'footer_alt_bg_color', 'order_form_bg_color' ) ); ?>
				<tbody>
					<?php $template_settings_api->show_detached_field_row( 'alternative_bg_color', array( 'post_field' => $create_show_advanced_options_link( 'alternative_bg_color' ) ) ); ?>
				</tbody>
				<?php $output_subcolors( 'alternative_bg_color', array( 'box_bg_color', 'header_bg_color', 'section_bg_color', 'button_bg_color', 'form_button_bg_color', 'thumb_bg_color', 'footer_bg_color', 'input_bg_color' ) ); ?>
				<tbody>
					<?php $template_settings_api->show_detached_field_row( 'primary_color', array( 'post_field' => $create_show_advanced_options_link( 'primary_color' ) ) ); ?>
				</tbody>
				<?php $output_subcolors( 'primary_color', array( 'links_color', 'links_color_alt', 'footer_links_color', 'footer_links_hover_color' ) ); ?>
				<tbody>
					<?php $template_settings_api->show_detached_field_row( 'text_color', array( 'post_field' => $create_show_advanced_options_link( 'text_color' ) ) ); ?>
				</tbody>
				<?php $output_subcolors( 'text_color', array( 'section_text_color', 'footer_alt_text_color', 'box_text_color', 'input_text_color', 'order_form_text_color', 'placeholder_text_color', 'contrast_text_color', 'footer_text_color', 'order_form_button_text_color' ) ); ?>
				<tbody>
					<?php $template_settings_api->show_detached_field_row( 'border_color', array( 'post_field' => $create_show_advanced_options_link( 'border_color' ) ) ); ?>
				</tbody>
				<?php $output_subcolors( 'border_color', array( 'menu_icon_color', 'box_border_color', 'button_border_color', 'form_button_border_color', 'order_form_button_border_color', 'thumb_border_color', 'input_border_color' ) ); ?>

                <?php if($templates_settings_handler->legacy_templates_settings_in_use()): ?>
                <tbody>
                    <?php $template_settings_api->show_detached_field_row( 'css' ); ?>
                </tbody>
                <?php endif; ?>
            </table>
		</div>
		<div id="postbox-container-1" class="postbox-container" style="margin-right: -400px; width: 380px;">
			<div class="postbox" id="courses_layout_<?php echo $template; ?>_preview">
				<h2><span><?php _e( 'Preview', BPMJ_EDDCM_DOMAIN ) ?></span></h2>
				<div class="inside _bg-bg_color _links-links_color _text-text_color _font-main_font" style="padding-top: 12px; border-top: 1px solid #c0c0c0;">
					<div class="_head _border-bottom-border_color _text-text_color" style="height: 40px; border-bottom-style: solid; border-bottom-width: 1px; padding-bottom: 12px; text-align: center;">
						<?php
						$logo = isset( $wpidea_settings['logo'] ) ? $wpidea_settings['logo'] : null;
						if ( $logo ):
							?>
							<img src="<?php echo $logo; ?>" alt="" style="max-height: 40px;" />
						<?php else: ?>
							<svg xmlns="http://www.w3.org/2000/svg"
							     viewBox="0 0 500 97"
							     style="height: 40px;">
								<style>
									/* <![CDATA[ */
									#text, #square-brackets {
										stroke: none;
										stroke-width: 0;
									}
									#text { fill: #1c1d21 /* text_color */; }
									#square-brackets { fill: #92cdcf /* primary_color */; }
									/* ]]> */
								</style>
								<path class="_text"
								      d="M 331.00,10.00
								      C 331.00,10.00 331.00,84.00 331.00,84.00
								      331.00,84.00 315.00,84.00 315.00,84.00
								      315.00,84.00 315.00,78.00 315.00,78.00
								      312.46,80.26 310.34,82.30 307.00,83.35
								      304.67,84.08 301.46,84.03 299.00,84.00
								      273.12,83.69 270.65,41.09 290.00,31.92
								      293.97,30.03 297.69,29.90 302.00,30.02
								      308.41,30.19 310.34,31.84 315.00,36.00
								      315.00,36.00 315.00,10.00 315.00,10.00
								      315.00,10.00 331.00,10.00 331.00,10.00 Z
								      M 73.00,13.00
								      C 80.37,13.14 79.70,14.30 82.63,26.00
								      82.63,26.00 88.35,50.00 88.35,50.00
								      88.35,50.00 91.00,62.00 91.00,62.00
								      91.00,62.00 100.14,27.00 100.14,27.00
								      100.14,27.00 104.56,15.02 104.56,15.02
								      104.56,15.02 117.00,14.00 117.00,14.00
								      117.00,14.00 130.00,63.00 130.00,63.00
								      130.26,51.22 138.34,26.21 141.00,13.00
								      141.00,13.00 158.00,13.00 158.00,13.00
								      158.00,13.00 153.37,33.00 153.37,33.00
								      153.37,33.00 140.00,84.00 140.00,84.00
								      140.00,84.00 121.00,84.00 121.00,84.00
								      121.00,84.00 110.00,44.00 110.00,44.00
								      110.00,44.00 99.00,84.00 99.00,84.00
								      99.00,84.00 80.00,84.00 80.00,84.00
								      80.00,84.00 66.63,33.00 66.63,33.00
								      66.63,33.00 62.00,13.00 62.00,13.00
								      62.00,13.00 73.00,13.00 73.00,13.00 Z
								      M 207.98,18.39
								      C 219.41,27.60 219.31,48.12 206.96,56.45
								      198.66,62.05 192.38,61.00 183.00,61.00
								      183.00,61.00 183.00,84.00 183.00,84.00
								      183.00,84.00 166.00,84.00 166.00,84.00
								      166.00,84.00 166.00,13.00 166.00,13.00
								      178.16,13.00 198.37,10.63 207.98,18.39 Z
								      M 267.00,13.00
								      C 267.00,13.00 267.00,84.00 267.00,84.00
								      267.00,84.00 250.00,84.00 250.00,84.00
								      250.00,84.00 250.00,13.00 250.00,13.00
								      250.00,13.00 267.00,13.00 267.00,13.00 Z
								      M 194.89,45.30
								      C 200.21,41.34 200.44,32.60 194.89,28.74
								      191.70,26.52 186.76,27.00 183.00,27.00
								      183.00,27.00 183.00,48.00 183.00,48.00
								      187.20,47.96 191.35,47.94 194.89,45.30 Z
								      M 418.00,29.09
								      C 424.86,29.48 432.19,31.00 436.30,37.02
								      438.93,40.86 438.98,44.55 439.00,49.00
								      439.00,49.00 439.00,84.00 439.00,84.00
								      439.00,84.00 424.00,84.00 424.00,84.00
								      424.00,84.00 423.00,79.00 423.00,79.00
								      418.93,85.65 406.98,85.47 401.04,81.30
								      393.66,76.12 392.71,63.44 399.13,57.21
								      401.40,55.01 403.99,53.76 407.00,52.93
								      411.69,51.64 418.05,52.00 423.00,52.00
								      421.80,37.19 407.29,44.66 399.00,47.00
								      399.00,47.00 399.00,32.00 399.00,32.00
								      405.01,30.65 411.79,28.74 418.00,29.09 Z
								      M 384.00,74.00
								      C 383.98,76.40 384.22,79.43 382.40,81.28
								      378.52,85.22 362.07,84.37 357.00,82.78
								      343.99,78.71 339.21,66.69 340.09,54.00
								      340.52,47.73 342.80,40.85 347.33,36.33
								      355.32,28.34 372.55,27.39 380.90,35.21
								      388.87,42.69 387.79,53.29 387.00,63.00
								      387.00,63.00 357.00,63.00 357.00,63.00
								      364.32,74.92 373.44,71.91 384.00,67.00
								      384.00,67.00 384.00,74.00 384.00,74.00 Z
								      M 369.70,44.43
								      C 363.71,38.91 357.33,45.75 356.00,52.00
								      356.00,52.00 373.00,52.00 373.00,52.00
								      372.41,49.25 371.88,46.44 369.70,44.43 Z
								      M 306.00,70.35
								      C 318.01,68.20 319.59,43.44 303.00,43.48
								      287.87,47.85 292.96,72.69 306.00,70.35 Z
								      M 418.00,62.11
								      C 416.95,62.42 416.05,62.64 415.11,63.17
								      405.30,68.72 419.57,77.49 422.79,66.98
								      423.02,65.41 422.96,63.62 422.79,62.11
								      422.79,62.11 418.00,62.11 418.00,62.11 Z" ></path>
								<path class="_square-brackets"
								      d="M 30.00,3.00
								      C 30.00,3.00 30.00,17.00 30.00,17.00
								      30.00,17.00 15.00,17.00 15.00,17.00
								      15.00,17.00 15.00,83.00 15.00,83.00
								      15.00,83.00 30.00,83.00 30.00,83.00
								      30.00,83.00 30.00,97.00 30.00,97.00
								      30.00,97.00 0.00,97.00 0.00,97.00
								      0.00,97.00 0.00,3.00 0.00,3.00
								      0.00,3.00 30.00,3.00 30.00,3.00 Z
								      M 500.00,3.00
								      C 500.00,3.00 500.00,97.00 500.00,97.00
								      500.00,97.00 469.00,97.00 469.00,97.00
								      469.00,97.00 469.00,83.00 469.00,83.00
								      469.00,83.00 484.00,83.00 484.00,83.00
								      484.00,83.00 484.00,17.00 484.00,17.00
								      484.00,17.00 469.00,17.00 469.00,17.00
								      469.00,17.00 469.00,3.00 469.00,3.00
								      469.00,3.00 500.00,3.00 500.00,3.00 Z" ></path>
                            </svg>
							      <?php endif; ?>
					</div>
					<div class="_bg-alternative_bg_color _border-bottom-border_color _text-section_text_color _links-links_color_alt" style="padding: 12px; text-align: center;">
						<h2 class="_font-secondary_font">Phasellus efficitur</h2>
						<p>Lacus non feugiat ullamcorper. <a href="javascript:">Quisque</a> ornare molestie magna. Integer <a href="javascript:">rhoncus elit</a> non est sollicitudin, ac tristique ante congue. Integer mollis, lacus a&nbsp;feugiat sagittis.</p>
					</div>
					<div style="padding: 12px; text-align: center;">
						Lorem ipsum dolor sit amet, <a href="javascript:">consectetur adipiscing elit</a>. Maecenas et libero eget elit congue rutrum et ut arcu.
						<div class="_bg-box_bg_color _border-bottom-box_border_color _text-box_text_color" style="padding: 12px; text-align: center; margin-top: 12px; border-bottom-width: 5px;">
							Lacus non feugiat ullamcorper. Quisque ornare molestie magna. Integer rhoncus elit non est sollicitudin, ac tristique ante congue. Integer mollis, lacus a feugiat sagittis.
						</div>
					</div>
					<footer class="_bg-footer_bg_color _text-footer_text_color _links-footer_links_color" style="padding: 12px; text-align: center;">
						Copyright &copy; <a href="javascript:">WP Idea</a>
					</footer>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>
<script type="text/javascript">
	( function ( $ ) {
		$( function () {
			var suppress_cascade_color_change = false;
			var main_colors = [ 'bg_color', 'alternative_bg_color', 'primary_color', 'text_color', 'border_color' ];
			var bg_file_keys = [ 'bg_file', 'section_bg_file' ];
			var create_google_font_link_tag = function (font_id, font_family) {
				if ( 0 === $( 'link#bpmj-eddcm-google-font-' + font_id ).length ) {
					$( document.head ).append( '<link id="bpmj-eddcm-google-font-' + font_id + '" rel="stylesheet" href="https://fonts.googleapis.com/css?family=' + encodeURIComponent( font_family ) + '"></link>' );
				}
			};
			$additional_settings_div = $( '#courses_layout_additional_settings_div');
			$additional_settings_div.find('.wp-color-picker-field' ).wpColorPicker( {
				change: function ( ev, ui ) {
					var field = ev.target.id.replace( '<?php echo $template_settings_api->get_name(); ?>[', '' ).replace( ']', '' );
					var new_color = ui.color.toString();
					if ( !suppress_cascade_color_change ) {
						suppress_cascade_color_change = true;
						$( '#courses_layout_additional_settings_div').find('.additional-colors-for-' + field + ' .slave-setting:checked' )
							.closest( 'td' )
							.find( 'input.wp-color-picker-field' )
							.wpColorPicker( 'color', new_color )
							;
						suppress_cascade_color_change = false;
						setTimeout( create_preview_styles, 200 );
					}
					var $checkbox = $( ev.target ).closest( 'td' ).find( 'input.slave-setting' );
					if ( $checkbox.length === 1 ) {
						var main_field = $checkbox.data( 'slaveOf' );
						var main_color = document.getElementById( '<?php echo $template_settings_api->get_name(); ?>[' + main_field + ']' ).value;
						$checkbox.get( 0 ).checked = new_color === main_color;
					}
				}
			} );
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
				create_preview_styles();
			} );
			$additional_settings_div.find('.select2.color-preset' ).select2( { templateResult: function ( option ) {
					var preset = $( option.element ).data() || { };
					var spans = [ ];
					for ( var i = 0, length = main_colors.length; i < length; ++i ) {
						var color_key = main_colors[i];
						if ( preset[color_key] ) {
							spans.push( '<span style="display: inline-block; width: 12px; height: 12px; background: ' + preset[color_key] + '"></span>' );
						}
					}
					return $( '<span>' + spans.join( '' ) + ' ' + option.text + '</span>' );
				}
			} );
			$additional_settings_div.find('.select2.color-preset' ).on( 'select2:select', function ( e ) {
				var option = e.params && e.params.data && e.params.data.element;
				var i, length;
				if ( !option instanceof HTMLOptionElement ) {
					return;
				}
				var preset = $( option ).data() || { };
				suppress_cascade_color_change = true;
				var fields = Object.keys( preset );

				for ( i = 0, length = bg_file_keys.length; i < length; ++i ) {
					document.getElementById( '<?php echo $template_settings_api->get_name(); ?>[' + bg_file_keys[i] + ']' ).value = preset[bg_file_keys[i]] || '';
				}

				// we need to ensure that primary colors come first
				fields.sort( function ( a, b ) {
					if ( $.inArray( a, main_colors ) ) {
						return 1;
					} else if ( $.inArray( b, main_colors ) ) {
						return -1;
					}
					return 0;
				} );
				for ( i = 0, length = fields.length; i < length; ++i ) {
					var field_key = fields[i];
					if ( field_key.indexOf( 'color' ) !== -1 ) {
						$( '#courses_layout_additional_settings_div input[name="<?php echo $template_settings_api->get_name(); ?>[' + field_key + ']"]' ).wpColorPicker( 'color', preset[field_key] || 'none' );
					}
				}
				suppress_cascade_color_change = false;
				create_preview_styles();
			} );
			$additional_settings_div.find('.show-additional-colors' ).click( function () {
				var main_field = $( this ).data( 'of' );
				var $sub_fields_tbody = $( '#courses_layout_additional_settings_div').find('.additional-colors-for-' + main_field );
				if ( $sub_fields_tbody.is( ':visible' ) ) {
					$sub_fields_tbody.hide();
					$( this ).text( '<?php esc_html_e( 'Show additional colors', BPMJ_EDDCM_DOMAIN ); ?>' );
				} else {
					$sub_fields_tbody.show();
					$( this ).text( '<?php esc_html_e( 'Hide additional colors', BPMJ_EDDCM_DOMAIN ); ?>' );
				}
			} );
			$additional_settings_div.find('.slave-setting' ).click( function ( ev ) {
				var $checkbox = $( ev.target );
				var $picker = $checkbox
					.closest( 'td' )
					.find( 'input.wp-color-picker-field' );
				if ( $checkbox.is( ':checked' ) ) {
					var color = $( '#courses_layout_additional_settings_div input[name="<?php echo $template_settings_api->get_name(); ?>[' + $checkbox.data( 'slaveOf' ) + ']"]' ).val();
					$checkbox.data( 'previousColor', $picker.val() );
					$picker.wpColorPicker( 'color', color );
				} else {
					if ( $checkbox.data( 'previousColor' ) ) {
						$picker.wpColorPicker( 'color', $checkbox.data( 'previousColor' ) );
					}
				}
			} );
			var create_preview_styles = function () {
				var styles_id = 'courses_layout_<?php echo $template; ?>_preview_styles';
				var $styles = $( '#' + styles_id );
				var preview_div_id = '#courses_layout_<?php echo $template; ?>_preview';
				if ( $styles.length === 0 ) {
					$( document.head ).append( '<style type="text/css" id="' + styles_id + '" />' );
					$styles = $( $styles.selector );
				}
				var styles_array = [];
				var colors = {};
				$( '#courses_layout_additional_settings_div' ).find( '.wp-color-picker-field' ).each( function () {
					var color_key = $( this ).attr( 'id' ).replace( '<?php echo $template_settings_api->get_name(); ?>[', '' ).replace( ']', '' );
					colors[ color_key ] = $( this ).val();
				} );
				styles_array.push( preview_div_id + ' * { transition: all 1s; }\n' );
				styles_array.push( preview_div_id + ' svg { max-height: 100%; max-width: 100%; }\n' );
				styles_array.push( preview_div_id + ' svg ._text { fill: ' + colors.text_color + '; }\n' );
				styles_array.push( preview_div_id + ' svg ._square-brackets { fill: ' + colors.primary_color + '; }\n' );
				for ( var color_key in colors ) {
					styles_array.push( preview_div_id + ' ._border-bottom-' + color_key + ' { border-bottom: 1px solid ' + colors[ color_key ] + ' !important; }\n'
					                   + preview_div_id + ' ._bg-' + color_key + ' { background: ' + colors[ color_key ] + ' !important; }\n'
					                   + preview_div_id + ' ._text-' + color_key + ' { color: ' + colors[ color_key ] + ' !important; }\n'
					                   + preview_div_id + ' ._links-' + color_key + ' a { color: ' + colors[ color_key ] + ' !important; }\n' );
				}
				$.each( [ 'main_font', 'secondary_font' ], function ( index, font_field ) {
					var font_family = document.getElementById( '<?php echo $template_settings_api->get_name(); ?>[' + font_field + '_family]' ).value;
					styles_array.push( preview_div_id + ' ._font-' + font_field + ' { font-family: "' + font_family + '"; }\n' );
				} );
				$styles.text( styles_array.join( '\n' ) );
			};
			create_preview_styles();
<?php $template_settings_api->script_file(); ?>
		} );
	}( jQuery ) );
</script>