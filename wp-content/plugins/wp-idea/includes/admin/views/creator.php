<?php
/** @var bool $invoices_enabled */
?>
<?php

use bpmj\wpidea\admin\Creator;
use bpmj\wpidea\admin\Edit_Course;
use bpmj\wpidea\Packages;
use bpmj\wpidea\Software_Variant;

$status = get_option( 'bpmj_eddcm_license_status' );
?>
<div class="wrap">
	<h2></h2>

	<?php

	$clone_mode = false;
	$product_id = false;

	$no_access_to_cloning            = false;
	$no_access_to_dripping           = WPI()->packages->no_access_to_feature( Packages::FEAT_DELAYED_ACCESS );
	$no_access_to_access_start       = WPI()->packages->no_access_to_feature( Packages::FEAT_COURSE_ACCESS_START );
	$no_access_to_access_time        = WPI()->packages->no_access_to_feature( Packages::FEAT_COURSE_ACCESS_TIME );
	$no_access_to_recurring_payments = WPI()->packages->no_access_to_feature( Packages::FEAT_RECURRING_PAYMENTS );
	$no_access_to_variable_pricing   = WPI()->packages->no_access_to_feature( Packages::FEAT_VARIABLE_PRICES );
	$no_access                       = false;
    $no_access_to_tests       = WPI()->packages->no_access_to_feature( Packages::FEAT_TESTS );
    $banner = null;

	if ( isset( $_GET[ 'cloned_id' ] ) ) {
		$id         = $_GET[ 'cloned_id' ];
		$product_id = get_post_meta( $id, 'product_id', true );
		$p          = get_post( $id );
		$course     = WPI()->courses->create_course_options_array( $id );
		$clone_mode = true;
		if ( WPI()->packages->no_access_to_feature( Packages::FEAT_COURSE_CLONING ) ) {
			$no_access_to_cloning = true;
			$no_access            = true;
		}
        $title   = $p ? $p->post_title . ' ' . __( '[Copy]', BPMJ_EDDCM_DOMAIN ) : '';
        $banner = get_post_meta($id, 'banner', true);
	} else {
		$p     = false;
		$id    = '';
		$title = '';
	}
	
	$integrations = $invoices_enabled || WPI()->diagnostic->mailer_integration() ? true : false;

    $editor = WPI()->container->get(Edit_Course::class);
	?>

	<template id="bpmj_eddcm_new_module_full_template">
		<?php echo Creator::create_module_get_html( 'full' ); ?>
	</template>
	<template id="bpmj_eddcm_new_module_lesson_template">
		<?php echo Creator::create_module_get_html( 'lesson' ); ?>
	</template>
    <template id="bpmj_eddcm_new_module_test_template">
        <?php echo Creator::create_module_get_html( 'test', false, false, '', true, false ); ?>
    </template>
	<template id="bpmj_eddcm_new_lesson_template">
		<?php echo Creator::create_lesson_get_html(); ?>
	</template>

	<div class="edd-courses-manager">

		<div class="row">
			<div class="heading animated fadeInDown">
				<?= __('Create a course', BPMJ_EDDCM_DOMAIN ); ?>
				<span class="settings-page"><?php _e( Software_Variant::get_name(), BPMJ_EDDCM_DOMAIN ); ?></span>
			</div>
		</div>

        <?php if ( 'inactive' !== $status ) : ?>

            <section class="edd-courses-manager-creator-steps <?php if ( $integrations ) {
                echo 'integrations-enabled';
            } ?>">
                <div class="container">
                    <ul class="progressbar">
                        <li class="active" data-step="one"><?php _e( 'General', BPMJ_EDDCM_DOMAIN ); ?></li>
                        <li data-step="two"><?php _e( 'Modules / Lessons', BPMJ_EDDCM_DOMAIN ); ?></li>
                        <li data-step="three"><?php _e( 'Product', BPMJ_EDDCM_DOMAIN ); ?></li>
                        <?php
                        // Show 4'th step if any integration is enabled
                        if ( $integrations ) {
                            echo '<li data-step="four">' . __( 'Integrations', BPMJ_EDDCM_DOMAIN ) . '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </section>

            <form class="edd-courses-manager-creator-steps-form" data-mode='course'>

                <section class="step-one animated fadeInUp">
                    <div class="row">
                        <div class="container">
                            <div class="panel <?php if ( $no_access ): ?>panel-danger<?php endif; ?>">
                                <?php if ( $no_access_to_cloning ): ?>
                                    <div
                                        class="panel-heading"><?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_COURSE_CLONING, __( 'In order to be allowed to clone courses, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?></div>
                                <?php endif; ?>
                                <div class="panel-body">



                                    <input type="hidden" name="banner" value="<?php echo $banner; ?>">
                                    
                                    
                                    <div class="form-group">
                                        <label for="title"><?php _e( 'Title', BPMJ_EDDCM_DOMAIN ); ?>*</label>
                                        <input type="text" name="title" id="title" value="<?php echo esc_attr( $title ) ?>">
                                        <div
                                            class="desc"><?php _e( 'Write here the title for your new course.', BPMJ_EDDCM_DOMAIN ); ?></div>
                                    </div>

									<?php if ( !$p ): ?>
                                        <div class="form-group">
                                            <label for="content"><?php _e( 'Short description', BPMJ_EDDCM_DOMAIN ); ?></label>
                                            <?php
                                            $settings = array(
                                                'media_buttons' => false,
                                                'editor_height' => 200,
                                                'teeny'         => true,
                                                'quicktags'     => false
                                            );
                                            wp_editor( '', 'content', $settings );
                                            ?>
                                            <div
                                                class="desc"><?php _e( 'This text will be shown on Course page. Feel free, and put here whatever you want.', BPMJ_EDDCM_DOMAIN ); ?></div>
                                        </div>
									<?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="step-two" style="display: none;">
                    <div class="row">
                        <div class="container">
                            <div class="panel">
                                <div class="panel-body">

                                    <div class="form-group">
                                        <?php
                                        $create_sample_structure = isset( $course ) && ! empty( $course[ 'create_sample_structure' ] );
                                        ?>
                                        <div class="label">
                                            <?php _e( 'Modules / Lessons', BPMJ_EDDCM_DOMAIN ); ?>
                                            (
                                            <label style="display: inline;">
                                                <input id="create-sample-structure" name="create_sample_structure"
                                                       type="checkbox"
                                                       value="1"
                                                    <?php checked( $create_sample_structure ); ?>/> <?php _e( 'Use sample structure', BPMJ_EDDCM_DOMAIN ); ?>
                                            </label>
                                            )
                                        </div>

                                        <ul id="bpmj_eddcm_modules_list" class="modules" <?php echo $create_sample_structure ? 'style="display:none;"' : '' ?>>
                                            <?php
                                            if ( isset( $course ) && ! $no_access ) {
                                                $modules = (isset($course[ 'module' ]) && is_array( $course[ 'module' ] )) ? $course[ 'module' ] : ( is_array( $course[ 'modules' ] ) ? $course[ 'modules' ] : array() );

                                                $content = array();
                                                foreach ( $modules as $module ) {

                                                    $connected_module_id = isset( $module[ 'id' ] ) ? $module[ 'id' ] : ( isset( $module[ 'created_id' ] ) ? $module[ 'created_id' ] : false );
                                                    $lessons             = isset( $module[ 'module' ] ) ? $module[ 'module' ] : false;
                                                    $title               = isset( $module[ 'title' ] ) ? $module[ 'title' ] : '';
                                                    if ( ! $title ) {
                                                        $title = get_the_title( $connected_module_id );
                                                    }

                                                    $get_module                            = Creator::create_module( $module[ 'mode' ], $connected_module_id, $lessons, $title, false, $clone_mode );
                                                    $content[ $get_module[ 'editor_id' ] ] = $module;

                                                    $content = $content + $get_module[ 'content' ];

                                                    echo $get_module[ 'html' ];
                                                }
                                            }
                                            ?>
                                        </ul>

                                        <div <?php echo $create_sample_structure ? 'style="display:none;"' : '' ?>
                                            class="desc"><?php _e( 'You can move modules or lessons and change their order.', BPMJ_EDDCM_DOMAIN ); ?></div>
                                    </div>

                                    <div class="creator-buttons text-center" <?php echo $create_sample_structure ? 'style="display:none;"' : '' ?>>
                                        <button type="button" class="btn-eddcm btn-eddcm-primary" data-action="add-module"
                                                data-mode="full"><?php _e( 'Add module', BPMJ_EDDCM_DOMAIN ); ?></button>
                                        <button type="button" class="btn-eddcm btn-eddcm-primary" data-action="add-module"
                                                data-mode="lesson"><?php _e( 'Add lesson', BPMJ_EDDCM_DOMAIN ); ?></button>
                                        <button<?php echo $no_access_to_tests ? ' disabled="disabled"' : ''; ?> type="button" class="btn-eddcm btn-eddcm-primary" data-action="add-module"
                                            data-mode="test"><?php _e( 'Add quiz', BPMJ_EDDCM_DOMAIN ); ?></button>
                                        <?php if ( $no_access_to_tests ): ?>
                                            <div class="desc text-danger">
                                                <p>
                                                    <strong><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?>:</strong>
                                                    <?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_TESTS, __( 'In order to be allowed to add quiz, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="step-three" style="display: none;">
                    <div class="row">
                        <div class="container">
                            <div class="panel">
                                <div class="panel-body">
                                    <?php
                                    $variable_pricing = isset( $course ) && isset( $course[ 'variable_pricing' ] ) ? (bool) $course[ 'variable_pricing' ] : false;
                                    if ( $no_access_to_variable_pricing ) {
                                        $variable_pricing = false;
                                    }
                                    ?>
                                    <div class="form-group">
                                        <input type="hidden" name="variable_pricing"
                                               value=""/>
                                        <label>
                                            <input type="checkbox"
                                                   id="eddcm-variable-pricing"
                                                   name="variable_pricing"
                                                   value="1"
                                                <?php checked( $variable_pricing ) ?>
                                                <?php disabled( $no_access_to_variable_pricing ); ?>/>
                                            <?php _e( 'Enable variable prices for this course', BPMJ_EDDCM_DOMAIN ); ?>
                                        </label>
                                        <?php if ( $no_access_to_variable_pricing ): ?>
                                            <div class="desc text-danger">
                                                <strong><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?>:</strong>
                                                <?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_VARIABLE_PRICES, __( 'In order to be allowed to set variable prices for a course, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div id="single-price" style="<?php echo $variable_pricing ? 'display:none;' : '' ?>">

                                        <div class="form-group">
                                            <label for="price"><?php _e( 'Price', BPMJ_EDDCM_DOMAIN ); ?></label>
                                            <input type="number" step="0.01" name="price" id="price"
                                                   placeholder="<?php echo edd_get_currency(); ?>" class="quater_width"
                                                   value="<?php if ( isset( $course ) ) {
                                                       echo $course[ 'price' ];
                                                   } ?>">
                                            <div
                                                class="desc"><?php _e( 'How much your course cost?', BPMJ_EDDCM_DOMAIN ); ?></div>
                                        </div>

                                        <div class="form-group">
                                            <label for="sale-price"><?php _e( 'Sale price', BPMJ_EDDCM_DOMAIN ); ?></label>
                                            <input type="number" step="0.01" name="sale_price" id="sale_price"
                                                   placeholder="<?php echo edd_get_currency(); ?>" class="quater_width"
                                                   value="<?php if ( isset( $course ) ) {
                                                       echo $course[ 'sale_price' ];
                                                   } ?>">
                                            <div
                                                class="desc"><?php _e( 'What\'s the discounted price for your course? Leave blank if none.', BPMJ_EDDCM_DOMAIN ); ?></div>
                                        </div>

                                        <div class="form-group">
                                            <label
                                                for="access_time"><?php _e( 'Access Time', BPMJ_EDDCM_DOMAIN ); ?></label>

                                            <input type="number" step="1" name="access_time" id="access_time"
                                                   class="quater_width" value="<?php if ( isset( $course ) ) {
                                                echo $course[ 'access_time' ];
                                            } ?>" <?php disabled( $no_access_to_access_time ); ?>>
                                            <?php
                                            $access_time_units = array(
                                                'minutes' => __( 'Minutes', BPMJ_EDDCM_DOMAIN ),
                                                'hours'   => __( 'Hours', BPMJ_EDDCM_DOMAIN ),
                                                'days'    => __( 'Days', BPMJ_EDDCM_DOMAIN ),
                                                'months'  => __( 'Months', BPMJ_EDDCM_DOMAIN ),
                                                'years'   => __( 'Years', BPMJ_EDDCM_DOMAIN )
                                            );
                                            ?>
                                            <select name="access_time_unit"
                                                    class="quater_width" <?php disabled( $no_access_to_access_time ); ?>>
                                                <?php
                                                foreach ( $access_time_units as $unit => $name ) {
                                                    $selected = '';
                                                    if ( isset( $course ) ) {
                                                        $selected = $unit == $course[ 'access_time_unit' ] ? 'selected' : '';
                                                    }

                                                    echo '<option value="' . $unit . '" ' . $selected . '>' . $name . '</option>';
                                                }
                                                ?>
                                            </select>

                                            <div
                                                class="desc"><?php _e( 'How long will the user be able to use course?<br>Leave blank to set unlimited access time.', BPMJ_EDDCM_DOMAIN ); ?></div>
                                            <?php if ( $no_access_to_access_time ): ?>
                                                <div class="desc text-danger">
                                                    <strong><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?>:</strong>
                                                    <?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_COURSE_ACCESS_TIME, __( 'In order to be allowed to set course access time, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group">
                                            <label
                                                for="purchase_limit"><?php _e( 'Purchase limit', BPMJ_EDDCM_DOMAIN ); ?></label>

                                            <div
                                                class="desc"><?php _e( 'Leave blank or set to 0 to disable', BPMJ_EDDCM_DOMAIN ); ?></div>
                                            <input type="number" name="purchase_limit" id="purchase_limit" step="1" min="0"
                                                   style="width: 80px;"
                                                   value="<?php echo isset( $course ) && ! empty( $course[ 'purchase_limit' ] ) ? esc_attr( $course[ 'purchase_limit' ] ) : ''; ?>"/>
                                        </div>
                                    </div>

                                    <div id="variable-prices"
                                         style="<?php echo $variable_pricing ? '' : 'display:none;' ?>">
                                        <table class="widefat edd_repeatable_table" width="100%" cellpadding="0"
                                               cellspacing="0">
                                            <thead>
                                            <tr>
                                                <th><?php _e( 'Option Name', 'easy-digital-downloads' ); ?></th>
                                                <th style="width: 100px"><?php _e( 'Price', 'easy-digital-downloads' ); ?></th>
                                                <th style="width: 100px"><?php _e( 'Sale price', 'edd-sale-price' ); ?></th>
                                                <th style="width: 50px;"><?php _e( 'Purchase limit', BPMJ_EDDCM_DOMAIN ); ?></th>
                                                <th style="width: 2%"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $currency_position         = edd_get_option( 'currency_position', 'before' );
                                            $default_price_id          = isset( $course ) && isset( $course[ 'default_price_id' ] ) ? $course[ 'default_price_id' ] : 1;
                                            $variable_prices           = isset( $course ) && isset( $course[ 'variable_prices' ] ) && is_array( $course[ 'variable_prices' ] ) ? array_filter( $course[ 'variable_prices' ] ) : array();
                                            $variable_prices_row_macro = function ( $key, $index, $args ) use ( $currency_position, $default_price_id ) {
                                                ?>
                                                <tr class="edd_variable_prices_wrapper edd_repeatable_row"
                                                    data-key="<?php echo $key; ?>">
                                                    <td>
                                                        <?php echo EDD()->html->text( array(
                                                            'name'        => 'variable_prices[' . $key . '][name]',
                                                            'value'       => esc_attr( $args[ 'name' ] ),
                                                            'placeholder' => __( 'Option Name', 'easy-digital-downloads' ),
                                                            'class'       => 'edd_variable_prices_name large-text'
                                                        ) ); ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $price_args = array(
                                                            'name'        => 'variable_prices[' . $key . '][amount]',
                                                            'value'       => $args[ 'amount' ],
                                                            'placeholder' => edd_format_amount( 9.99 ),
                                                            'class'       => 'edd-price-field'
                                                        );
                                                        ?>

                                                        <?php echo EDD()->html->text( $price_args ); ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $price_args = array(
                                                            'name'        => 'variable_prices[' . $key . '][sale_price]',
                                                            'value'       => ! empty( $args[ 'sale_price' ] ) ? $args[ 'sale_price' ] : '',
                                                            'placeholder' => edd_format_amount( 9.99 ),
                                                            'class'       => 'edd-price-field edd-sale-price-field',
                                                        );
                                                        ?>

                                                        <?php echo EDD()->html->text( $price_args ); ?>
                                                    </td>
                                                    <td><input type="number"
                                                               name="variable_prices[<?php echo $key; ?>][bpmj_eddcm_purchase_limit]"
                                                               value="<?php echo isset( $args[ 'bpmj_eddcm_purchase_limit' ] ) ? esc_attr( $args[ 'bpmj_eddcm_purchase_limit' ] ) : ''; ?>"
                                                               title="<?php esc_attr_e( 'Purchase limit', BPMJ_EDDCM_DOMAIN ); ?>"
                                                               style="width: 50px;"/>
                                                    </td>
                                                    <td>
                                                        <a href="#" class="edd_remove_repeatable" data-type="price"
                                                           style="background: url(<?php echo admin_url( '/images/xit.gif' ); ?>) no-repeat;">&times;</a>
                                                    </td>
                                                </tr>
                                                <?php
                                            };
                                            ?>
                                            <?php
                                            if ( ! empty( $variable_prices ) ) {
                                                foreach ( $variable_prices as $key => $price ) {
                                                    $name   = isset( $price[ 'name' ] ) ? $price[ 'name' ] : '';
                                                    $amount = isset( $price[ 'amount' ] ) ? $price[ 'amount' ] : '';
                                                    $index  = isset( $price[ 'index' ] ) ? $price[ 'index' ] : $key;
                                                    $variable_prices_row_macro( $key, $index, $price );
                                                }
                                            } else {
                                                $variable_prices_row_macro( 1, 1, array( 'name' => '', 'amount' => '' ) );
                                            }
                                            ?>
                                            <tr>
                                                <td class="submit" colspan="99"
                                                    style="float: none; clear:both; background:#fff;">
                                                    <a class="button-secondary edd_add_repeatable"
                                                       style="margin: 6px 0;"><?php _e( 'Add New Price', 'easy-digital-downloads' ); ?></a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>


                                    <div class="form-group">
                                        <label for="drip_value"><?php _e( 'Drip Value', BPMJ_EDDCM_DOMAIN ); ?></label>
                                        <input type="number" step="1" name="drip_value" id="drip_value" class="quater_width"
                                               value="<?php echo ($course[ 'drip_value' ]) ?? ''; ?>" <?php disabled( $no_access_to_dripping ); ?>>

                                        <select name="drip_unit"
                                                class="quater_width" <?php disabled( $no_access_to_dripping ); ?>>
                                            <?php
                                            foreach ( $access_time_units as $unit => $name ) {
                                                $selected = '';
                                                if ( isset( $course ) ) {
                                                    $selected = $unit == $course[ 'drip_unit' ] ? 'selected' : '';
                                                }

                                                echo '<option value="' . $unit . '" ' . $selected . '>' . $name . '</option>';
                                            }
                                            ?>
                                        </select>

                                        <div
                                            class="desc"><?php _e( 'Set for example <b>1 day</b>, to release one lesson every day.<br>Leave blank to release all lessons immediately after purchase.', BPMJ_EDDCM_DOMAIN ); ?></div>
                                        <?php if ( $no_access_to_dripping ): ?>
                                            <div class="desc text-danger">
                                                <strong><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?>:</strong>
                                                <?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_DELAYED_ACCESS, __( 'In order to drip courses, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label
                                            for="access_start"><?php _e( 'Access start date', BPMJ_EDDCM_DOMAIN ); ?></label>

                                        <div
                                            class="desc"><?php _e( 'Use this to choose when the course will be accessible to participants. Leave blank to disable', BPMJ_EDDCM_DOMAIN ); ?></div>

                                        <input type="text" name="access_start" id="access_start"
                                               class="half_width wp-datepicker-field" <?php disabled( $no_access_to_access_start ); ?> />

                                        <div class="desc inline"><?php _e( 'on time of the day', BPMJ_EDDCM_DOMAIN ); ?>:</div>
                                        <select name="access_start_hh"
                                                style="width: 50px;" <?php disabled( $no_access_to_access_start ); ?>>
                                            <?php foreach ( range( 0, 23 ) as $hour ):
                                                $hour_str = str_pad( $hour, 2, '0', STR_PAD_LEFT );
                                                ?>
                                                <option value="<?php echo $hour_str; ?>"><?php echo $hour_str; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        :
                                        <select name="access_start_mm"
                                                style="width: 50px;" <?php disabled( $no_access_to_access_start ); ?>>
                                            <?php foreach ( range( 0, 59 ) as $minute ):
                                                $minute_str = str_pad( $minute, 2, '0', STR_PAD_LEFT );
                                                ?>
                                                <option
                                                    value="<?php echo $minute_str; ?>"><?php echo $minute_str; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if ( $no_access_to_access_start ): ?>
                                            <div class="desc text-danger">
                                                <strong><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?>:</strong>
                                                <?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_COURSE_ACCESS_START, __( 'In order to course access start, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ( function_exists( 'edd_any_enabled_gateway_supports_recurring_payments' ) && edd_any_enabled_gateway_supports_recurring_payments() ): ?>
                                        <div class="form-group">
                                            <input type="hidden" name="recurring_payments_enabled"
                                                   value=""/>
                                            <label>
                                                <input type="checkbox"
                                                       name="recurring_payments_enabled"
                                                       value="1"
                                                    <?php checked( isset( $course ) ? $course[ 'recurring_payments' ] : false, '1' ); ?>
                                                    <?php disabled( $no_access_to_recurring_payments ); ?>/>
                                                <?php _e( 'Enable recurring payments for this course', BPMJ_EDDCM_DOMAIN ); ?>
                                            </label>
                                            <?php if ( $no_access_to_recurring_payments ): ?>
                                                <div class="desc text-danger">
                                                    <strong><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?>:</strong>
                                                    <?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_RECURRING_PAYMENTS, __( 'In order to enable recurring payments, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <?php if ( $integrations ) { ?>
                    <section class="step-four integrations" style="display: none">
                        <div class="row">
                            <div class="container">

                                <?php if ( $invoices_enabled ) { ?>
                                    <div class="panel" style="margin-bottom: 20px;">
                                        <div class="panel-heading">
                                            <?php _e( 'Invoices', BPMJ_EDDCM_DOMAIN ); ?>
                                        </div>

                                        <div class="panel-body">
                                            <?php $editor->metabox_invoice_settings( $product_id ); ?>
                                        </div>
                                    </div>
                                <?php } ?>


                                <?php if ( WPI()->diagnostic->mailer_integration() ) { ?>
                                    <div class="panel">
                                        <div class="panel-heading" style="margin-bottom: 0px;">
                                            <?php _e( 'Mailers', BPMJ_EDDCM_DOMAIN ); ?>
                                        </div>

                                        <div class="panel-body" style="padding-top: 0px;">
                                            <?php $editor->metabox_mailer_settings( $product_id ); ?>
                                        </div>
                                    </div>
                                <?php } ?>


                            </div>
                        </div>
                    </section>
                <?php } ?>


                <?php if ( ! $no_access ): ?>
                    <section class="navigation text-center animated fadeInUp">

                        <h4 class="question"><?php _e( 'Are you ready for next step?', BPMJ_EDDCM_DOMAIN ); ?></h4>
                        <button type="button" class="btn-eddcm btn-eddcm-default" data-action="previous-step"
                                style="display: none;"><?php _e( 'Back', BPMJ_EDDCM_DOMAIN ); ?></button>
                        <button type="button" class="btn-eddcm btn-eddcm-primary btn-eddcm-big"
                                data-action="next-step"><?php _e( 'Create modules and lessons', BPMJ_EDDCM_DOMAIN ); ?></button>

                    </section>
                <?php endif; ?>

                <input type="hidden" name="cpt_id" id="cpt_id" value="<?php echo $clone_mode ? '' : $id; ?>">
                <input type="hidden" name="cloned_from_id" value="<?php echo $clone_mode ? $id : ''; ?>">
            </form>

        <?php else : ?>
            <h3 class="text-center"><?php _e( 'Your license is inactive.', BPMJ_EDDCM_DOMAIN ); ?></h3>
        <?php endif; ?>

	</div>
</div>
