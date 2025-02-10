<?php

use bpmj\wpidea\admin\Edit_User;
use bpmj\wpidea\Course_Progress;
use bpmj\wpidea\Packages;
use bpmj\wpidea\Caps;
use bpmj\wpidea\Software_Variant;

/* @var $this Edit_User */

$is_admin = current_user_can( Caps::CAP_MANAGE_SETTINGS ) || current_user_can( Caps::CAP_MANAGE_PRODUCTS );
if(!$is_admin) return;

$not_accessible_courses = WPI()->courses->get_courses();
$courses                = WPI()->courses->get_users_accessible_courses( $this->get_user_id(), true );
$access_time            = get_user_meta( $this->get_user_id(), "_bpmj_eddpc_access", true );

/*
 * Prepare courses array
 */
foreach ( $courses as $key => $course ) {
	if ( empty( $access_time[ (int) $course[ 'product_id' ] ] ) ) {
		unset( $courses[ $key ] );
	} else {
		foreach ( $not_accessible_courses as $other_key => $other_course ) {
			if ( $other_course[ 'id' ] === $course[ 'id' ] ) {
				unset( $not_accessible_courses[ $other_key ] );
			}
		}
	}
}

?>

<div class="edd-courses-manager" id="edd-courses-manager">
    <div class="row">
        <div class="heading animated fadeInDown">
			<?php _e( Software_Variant::get_name(), BPMJ_EDDCM_DOMAIN ); ?>
        </div>
    </div>
    <section class="edd-courses-manager-dashboard">
        <div class="row">
            <div class="full-column">
                <div class="panel courses no-courses animated fadeInUp">
                    <div class="panel-heading">
						<?php echo IS_PROFILE_PAGE ? __( 'Your courses', BPMJ_EDDCM_DOMAIN ) : __( 'This user\'s courses', BPMJ_EDDCM_DOMAIN ); ?>
                    </div>
                    <div class="panel-body no-padding">
                        <table>
                            <thead>
                            <tr>
                                <th><?php _e( 'Course title', BPMJ_EDDCM_DOMAIN ); ?></th>
								<?php if ( WPI()->packages->has_access_to_feature( Packages::FEAT_PROGRESS_TRACKING ) ): ?>
                                    <th colspan="2"
                                        class="text-right"><?php _e( 'Course progress', BPMJ_EDDCM_DOMAIN ); ?></th>
								<?php endif; ?>
                                <th class="text-right"
                                    style="width: 100px;"><?php _e( 'Access due', BPMJ_EDDCM_DOMAIN ); ?></th>
                                <th class="text-right"
                                    style="width: 120px;"><?php _e( 'Total time', BPMJ_EDDCM_DOMAIN ); ?></th>
                                <th class="text-right"
                                    style="width: 400px;"><?php _e( 'Actions', BPMJ_EDDCM_DOMAIN ); ?></th>
                            </tr>
                            </thead>
                            <tbody>
							<?php foreach ( $courses as $course ):
								$product_id = $course[ 'product_id' ];
								$access = $access_time[ $product_id ];
								$next_payment = function_exists( 'edd_user_get_nearest_pending_recurring_payment' ) ? edd_user_get_nearest_pending_recurring_payment( $this->get_user_id(), $product_id ) : null;
								?>
                                <tr>
                                    <td><?php echo esc_html( $course[ 'title' ] ); ?></td>
									<?php if ( 'valid' === $course[ 'access' ] ): ?>
										<?php if ( WPI()->packages->has_access_to_feature( Packages::FEAT_PROGRESS_TRACKING ) ): ?>
											<?php
											$progress = new Course_Progress( get_post_meta( $course[ 'id' ], 'course_id', true ), null, true, $this->get_user_id() );
											if ( $progress->is_tracking_enabled() ):
												?>
                                                <td class="right" style="width: 120px;">
                                                    <a href="" data-action="show-course-progress-popup"
                                                       data-user-id="<?php echo $this->get_user_id(); ?>"
                                                       data-product-id="<?php echo $product_id; ?>"
                                                       data-course-id="<?php echo $course[ 'id' ]; ?>">
                                                        (<?php echo $progress->get_finished_lesson_count(); ?>
                                                        / <?php echo $progress->get_course_lesson_count(); ?>)
                                                    </a>
                                                </td>
                                                <td class="right"
                                                    style="width: 40px;"><?php echo $progress->get_progress_percent(); ?>
                                                    %
                                                </td>
											<?php else: ?>
                                                <td colspan="2" style="width: 160px;">
                                                    <i><?php _e( 'Progress tracking is disabled', BPMJ_EDDCM_DOMAIN ); ?></i>
                                                </td>
											<?php endif; ?>
										<?php endif; ?>
                                        <td class="right"
                                            id="access_time_<?php echo $course[ 'id' ]; ?>"><?php echo $this->html_access_time_cell( $access[ 'access_time' ], $this->get_user_id(), $course[ 'id' ], $product_id ); ?> </td>
                                        <td class="right"><span
                                                    id="total_time_<?php echo $course[ 'id' ]; ?>">-:-:-:-</span>
                                            <a href="" style="text-decoration: none; float: right;"
                                               data-action="set-total-time-popup"
                                               data-user-id="<?php echo $this->get_user_id(); ?>"
                                               data-product-id="<?php echo $product_id; ?>"
                                               data-course-id="<?php echo $course[ 'id' ]; ?>"><span
                                                        class="dashicons dashicons-welcome-write-blog icons bpmj-icons"></span></a>
                                        </td>
									<?php elseif ( 'waiting' === $course[ 'access' ] ): ?>
                                        <td colspan="<?php echo WPI()->packages->has_access_to_feature( Packages::FEAT_PROGRESS_TRACKING ) ? 4 : 2; ?>"
                                            class="text-center">
                                            <i><?php _e( 'This course hasn\'t started yet.', BPMJ_EDDCM_DOMAIN ); ?></i>
                                        </td>
									<?php endif; ?>
                                    <td class="text-right">
                                        <a href="<?php echo get_permalink( get_post_meta( $course[ 'id' ], 'course_id', true ) ); ?>"
                                           class="btn-eddcm btn-eddcm-default"><?php _e( 'View Course', BPMJ_EDDCM_DOMAIN ); ?></a>
                                        <a href="<?php echo esc_attr( admin_url( 'post.php?post=' . $course[ 'id' ] . '&action=edit' ) ); ?>"
                                           class="btn-eddcm btn-eddcm-primary"><?php _e( 'Edit', BPMJ_EDDCM_DOMAIN ); ?></a>
                                        <a href=""
                                           data-action="remove-from-course"
                                           data-user-id="<?php echo $this->get_user_id(); ?>"
                                           data-product-id="<?php echo $product_id; ?>"
                                           class="btn-eddcm btn-eddcm-primary"><?php _e( 'Remove user from course', BPMJ_EDDCM_DOMAIN ); ?></a>
                                    </td>
                                </tr>
								<?php if ( $next_payment ): ?>
                                <tr>
                                    <td colspan="5">
                                        <strong><?php _e( 'Next payment', BPMJ_EDDCM_DOMAIN ) ?>: </strong>
                                        #<?php echo $next_payment->ID; ?>
                                        ; <?php echo date_i18n( get_option( 'date_format' ), strtotime( $next_payment->date ) ); ?>
                                        ; <?php
										$payment_amount = $next_payment->total;
										$payment_amount = ! empty( $payment_amount ) ? $payment_amount : 0;
										echo edd_currency_filter( edd_format_amount( $payment_amount ), edd_get_payment_currency_code( $next_payment->ID ) );
										?>
                                        - <?php
										echo '<a href="' . add_query_arg( 'id', $next_payment->ID, admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details' ) ) . '">' . __( 'View Order Details', 'easy-digital-downloads' ) . '</a>';
										?>
                                    </td>
                                    <td class="text-right">
                                        <a href="<?php echo wp_nonce_url( home_url( '?bpmj_eddcm_cancel_subscription&purchase_id=' . $next_payment->ID ), 'edd_payment_nonce' ); ?>" class="btn-eddcm btn-eddcm-primary btn-eddcm-cancel-subscription"><?php _e( 'Cancel subscription', BPMJ_EDDCM_DOMAIN ); ?></a>
                                    </td>
                                </tr>
							<?php endif; ?>
							<?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="text-center" style="padding: 20px 0;">
        <button type="button" class="btn-eddcm btn-eddcm-primary"
                data-action="add-to-course"><?php _e( 'Add a new course', BPMJ_EDDCM_DOMAIN ); ?></button>
    </div>
</div>
<template id="bpmj_eddcm_course_progress">
    <div style="text-align: center;" class="_put_content_here">
        <span class="dashicons dashicons-update"></span>
    </div>
</template>
<template id="bpmj_eddcm_edit_access_time">
    <div style="text-align: center;">
        <form method="post" action="" onsubmit="return false;">
            <input type="hidden" name="user_id" value=""/>
            <input type="hidden" name="product_id" value=""/>
            <input type="hidden" name="course_id" value=""/>
            <p><label><input type="checkbox" name="no_limit"/> <?php _e( 'No limit', BPMJ_EDDCM_DOMAIN ); ?></label>
            </p>
            <div id="bpmj_eddcm_edit_access_time_details">
                <div class="form-group">
                    <label for="bpmj_eddcm_access_due_date"><?php _e( 'Access due date', BPMJ_EDDCM_DOMAIN ); ?>
                        :</label>
                    <input type="text" name="access_due_date" id="bpmj_eddcm_access_due_date"
                           class="wp-datepicker-field"
                           value=""/>
                </div>
                <div class="form-group">
                    <label><?php _e( 'Access due time', BPMJ_EDDCM_DOMAIN ); ?>:</label>
                    <select name="access_due_hh"
                            style="width: 50px;">
						<?php foreach ( range( 0, 23 ) as $hour ):
							$hour_str = str_pad( $hour, 2, '0', STR_PAD_LEFT );
							?>
                            <option value="<?php echo $hour_str; ?>"><?php echo $hour_str; ?></option>
						<?php endforeach; ?>
                    </select>
                    :
                    <select name="access_due_mm"
                            style="width: 50px;">
						<?php foreach ( range( 0, 59 ) as $minute ):
							$minute_str = str_pad( $minute, 2, '0', STR_PAD_LEFT );
							?>
                            <option value="<?php echo $minute_str; ?>"><?php echo $minute_str; ?></option>
						<?php endforeach; ?>
                    </select>
                </div>
            </div>
            <p>
                <button type="button"
                        class="btn-eddcm btn-eddcm-primary"
                        data-action="set-access-time"><?php _e( 'Set access time', BPMJ_EDDCM_DOMAIN ); ?></button>
            </p>
        </form>
    </div>
</template>
<template id="bpmj_eddcm_edit_total_time">
    <div style="text-align: center;">
        <form method="post" action="" onsubmit="return false;">
            <input type="hidden" name="user_id" value=""/>
            <input type="hidden" name="product_id" value=""/>
            <input type="hidden" name="course_id" value=""/>
            <div class="form-group">
                <label for="bpmj_eddcm_access_due_date"><?php _e( 'Total time (DDD:HH:MM:SS)', BPMJ_EDDCM_DOMAIN ); ?>
                    :</label>
                <select name="total_time_sign" style="width: 40px">
                    <option>+</option>
                    <option>-</option>
                </select>
                <input type="number" name="total_time_dd" style="width: 70px;" value=""/>
                :
                <input type="number" name="total_time_hh" style="width: 60px;" value=""/>
                :
                <input type="number" name="total_time_mm" style="width: 60px;" value=""/>
                :
                <input type="number" name="total_time_ss" style="width: 60px;" value=""/>
            </div>
            <p>
                <button type="button"
                        class="btn-eddcm btn-eddcm-primary"
                        data-action="set-total-time"><?php _e( 'Set total time', BPMJ_EDDCM_DOMAIN ); ?></button>
            </p>
        </form>
    </div>
</template>
<template id="bpmj_eddcm_add_user_to_a_course">
    <section class="edd-courses-manager-dashboard">
        <div class="row">
            <div class="full-column">
                <div class="panel courses no-courses animated fadeInUp">
                    <div class="panel-body no-padding">
                        <table>
                            <thead>
                            <tr>
                                <th class="title"><?php _e( 'Course title', BPMJ_EDDCM_DOMAIN ); ?></th>
                                <th class="text-right"><?php _e( 'Actions', BPMJ_EDDCM_DOMAIN ); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ( $not_accessible_courses as $course ):
	                            $product_id = get_post_meta( $course[ 'id' ], 'product_id', true );
	                            if ( edd_has_variable_prices( $product_id ) ):
		                            ?>
		                            <tr>
			                            <td class="title" colspan="2"><?php echo esc_html( $course[ 'title' ] ); ?></td>
		                            </tr>
		                            <?php foreach ( edd_get_variable_prices( $product_id ) as $price_id => $variable_price ): ?>
		                            <tr>
			                            <td class="title variable-price">&mdash; <?php echo __( 'Price', BPMJ_EDDCM_DOMAIN ) . ' ' . esc_html( $variable_price[ 'name' ] ); ?></td>
			                            <td class="text-right">
				                            <a href=""
				                               data-action="add-to-course-do"
				                               data-user-id="<?php echo $this->get_user_id(); ?>"
				                               data-product-id="<?php echo $product_id; ?>"
				                               data-price-id="<?php echo $price_id; ?>"
				                               class="btn-eddcm btn-eddcm-primary"><?php _e( 'Add a course', BPMJ_EDDCM_DOMAIN ); ?></a>
			                            </td>
		                            </tr>
	                            <?php endforeach; ?>
	                            <?php else: ?>
		                            <tr>
			                            <td class="title"><?php echo esc_html( $course[ 'title' ] ); ?></td>
			                            <td class="text-right">
				                            <a href=""
				                               data-action="add-to-course-do"
				                               data-user-id="<?php echo $this->get_user_id(); ?>"
				                               data-product-id="<?php echo $product_id; ?>"
				                               class="btn-eddcm btn-eddcm-primary"><?php _e( 'Add a course', BPMJ_EDDCM_DOMAIN ); ?></a>
			                            </td>
		                            </tr>
	                            <?php endif; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script type="text/javascript">
	jQuery( function ( $ ) {
		var zeropad = function ( number, digits ) {
			digits = digits || 2;
			return (
				"0000" + number
			).slice( - digits );
		};
		var format_time = function ( time ) {
			var minus = '';

			if ( time < 0 ) {
				time = - time;
				minus = '-';
			}

			var days = Math.floor( time / (
					60 * 60 * 24
				) );
			time -= days * (
			        60 * 60 * 24
				);

			var hours = Math.floor( time / (
					60 * 60
				) );
			time -= hours * (
			        60 * 60
				);

			var minutes = Math.floor( time / 60 );
			time -= minutes * 60;

			var seconds = Math.floor( time );

			return minus + [
					zeropad( days, 3 ),
					zeropad( hours ),
					zeropad( minutes ),
					zeropad( seconds )
				].join( ':' );
		};
		var start_date = new Date();
		window.bpmj_eddcm_users_manager_nonce = '<?php echo wp_create_nonce( 'bpmj_eddcm_users_manager' ); ?>';
		window.bpmj_eddcm_total_timers = {
			<?php foreach ( $courses as $course ):
			if ( $course[ 'access' ] !== 'valid' ) {
				continue;
			}
			$product_id = $course[ 'product_id' ];
			$access = $access_time[ $product_id ]; ?>
			total_time_<?php echo $course[ 'id' ]; ?>: <?php echo (int) $access[ 'total_time' ]; ?>,
			<?php endforeach; ?>
		};
		setInterval( function () {
			var delta = Math.round( (
				                        new Date() - start_date
			                        ) / 1000 );
			<?php foreach ( $courses as $course ):
			if ( $course[ 'access' ] !== 'valid' ) {
				continue;
			}
			?>
			$( '#total_time_<?php echo $course[ 'id' ]; ?>' ).text( format_time( parseInt( window.bpmj_eddcm_total_timers.total_time_<?php echo $course[ 'id' ]; ?>) + delta ) );
			<?php endforeach; ?>
		}, 800 );
	} );
</script>
