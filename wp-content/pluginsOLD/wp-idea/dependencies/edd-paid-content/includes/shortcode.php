<?php
/*
 * Utworzenie Shortcode
 * 
 * Postać - [edd_paid_content]zablokowana treść[/edd_paid_content]
 * 
 *  @ Parametr id - id produktów odzielone przecinkami. Dla wariantów przyjmuje się postać id="123:wariant",
 * gdzie po dwukropku podaje się nazwę wariantu
 * 
 *  @ Parametr time - czas podany w formacie s, m, h, d, np. time="10s,40m,1h,5d"
 */

// Zakończ, jeśli połączono bezpośrednio
use bpmj\wpidea\resources\Resource_Type;

if ( !defined( 'ABSPATH' ) )
	exit;

add_shortcode( 'edd_paid_content', 'bpmj_eddpc_form_shortcode' );

function bpmj_eddpc_form_shortcode( $attr, $content ) {
	global $post;
	global $edd_options;

	$attr = shortcode_atts( array(
		'id'	 => '',
		'mode'	 => '',
		'time'	 => '',
		'img'	 => '',
		'txt'	 => ''
	), $attr );

	$inv = false;
	if ( $attr[ 'mode' ] == 'inv' )
		$inv = true;

	$locked	 = true;
	$output	 = '';

	// Sprawdzanie, czy użytkownik jest zalogowany
	if ( is_user_logged_in() ) {

		// ID zalogowanego użytkownika
		$user_id = get_current_user_id();

		// Gdy nie podano strybutu ID, to zostanie pobrane ID bierzącego postu
		if ( !isset( $attr[ 'id' ] ) || empty( $attr[ 'id' ] ) ) {
			$attr[ 'id' ] = $post->ID;
		}

		// Pobiera sformatowane dane z parametru id
		$products_id = bpmj_eddpc_format_shortcode_id_param( $attr[ 'id' ], $post->ID );
		// Sprawdza, czy parametr id został sformatowany pomyślnie
		if ( !isset( $products_id ) || empty( $products_id ) )
			return '<span class="bpmj_eddpc_errors">' . __( 'Shortcode error [edd_paid_content] - invalid parameter value: <b>id<b>.', 'edd-paid-content' ) . '</span>';

		// Pobranie i sformatowanie czasu
		if ( isset( $attr[ 'time' ] ) && !empty( $attr[ 'time' ] ) ) {
			$time = bpmj_eddpc_format_shortcode_time_param( $attr[ 'time' ] );
		} else {
			$time = false;
		}


		// Aktualny czas
		date_default_timezone_set( get_option( 'timezone_string' ) );
		$current_time = time();

		// Wszystkie zamówienia użytkownika
		$purchases = edd_get_users_purchases();

		// Przerzetwarzaj dalej jeśli są zamówienia
		if ( $purchases ) {
			// Pętla po wszytskich zamówieniach
			foreach ( $purchases as $purchase ) {

				if ( !empty( $output ) )
					break;

				// Pobranie daty złożenia zamówienia
				$purchase_date	 = strtotime( edd_get_payment_completed_date( $purchase->ID ) );
				//$purchase_date = strtotime($purchase->post_date);
				// Pobiera informacje o zamówionych produktach
				$payment_meta	 = edd_get_payment_meta( $purchase->ID );

				// Pobiera szczegółowe informacje o zamówieniu
				if ( is_array( $payment_meta[ 'cart_details' ] ) ) {
					$cart = $payment_meta[ 'cart_details' ];
				} else {
					$cart = unserialize( $payment_meta[ 'cart_details' ] );
				}


				// Sprawdza, czy zamówione produkty posiadają zestaw.
				foreach ( $cart as $product ) {

					if ( !empty( $output ) )
						break;

					if ( edd_is_bundled_product( $product[ 'id' ] ) ) {

						// Pobiera ID prodktów z zestawu
						$bundle = edd_get_bundled_products( $product[ 'id' ] );

						if ( empty( $bundle ) ) {
							$bundle = array();
						}

						foreach ( $bundle as $id ) {



							// Sprawdza, czy produkt z zestawu nie ma przypisanych różnych wariantów
							if ( edd_has_variable_prices( $id ) ) {
								$variant_bundle = edd_get_variable_prices( $id );

								$variant_array = array();

								foreach ( $variant_bundle as $variant ) {

									// Zamiana nazwy wariantu na id
									$variant_id = bpmj_eddpc_convert_variant_name_to_variant_id( $id, $variant[ 'name' ] );

									/* Dopisanie do koszyka każdego wariantu. Każdy wariant w EDD jest zwracany jak odzielny produkt.
									 * Produkt z czterema wariantami doda do tablicy $cart 4 rekordy
									 */

									$cart[] = array(
										'id'			 => $id,
										'item_number'	 => array(
											'options' => array(
												'price_id' => (string) $variant_id
											)
										)
									);
								}
							} else {
								// Dopisanie do koszyka produkt z zestawu ( bez wariantów )
								$cart[] = array( 'id' => $id );
							}
						}

						;
					}
				}


				// Pętla po produktach z koszyka
				foreach ( $cart as $product ) {

					// Pętla po wszytskich ID produktów zawartych w shortcode
					foreach ( $products_id as $sc_ids ) {

						$product_id = $product[ 'id' ];

						// Gdy w shortcode zostały wprowadzone warianty
						if ( is_array( $sc_ids ) ) {

							// Wariant z shortcode
							$variant_name = $sc_ids[ 1 ];

							$variant_id = '';
							// Wiadomo na tym etpapie, że nazwa wariantu została wpisana w shortcode.
							// Należy teraz sprawdzić, czy warianty rzeczywiście zostały utworzone w produkcie EDD
							if ( isset( $product[ 'item_number' ][ 'options' ][ 'price_id' ] ) ) {

								$cart_variant_id = $product[ 'item_number' ][ 'options' ][ 'price_id' ];

								// Zamienia nazwy wariantów na id wariantów kryjące się pod kluczem price_id
								$sc_variant_id = bpmj_eddpc_convert_variant_name_to_variant_id( $product_id, $variant_name );

								// Porównuje ID produktu z ID podanym w shortcode
								if ( (int) $sc_ids[ 0 ] === (int) $product_id ) {

									// Porówbnuje id wariantu cenowego produktu z tym podanym w shortcode
									if ( $sc_variant_id === (int) $cart_variant_id ) {

										// Sprawdza, czy nie minął czas publikacji ukrytej treści
										if ( $time ) {

											// Data w timestamp w po upłynięciu której treść ma być znów ukryta
											$time_out = $purchase_date + $time;

											// Sformatowana data wygaśnięcia
											$time_out_format = date( 'l d.m.Y, \g\o\d\z: G:i \s\. s', $time_out );

											if ( $time_out > $current_time ) {

												//  Opublikowanie treśći
												$output = '<div id="bpmj_eddpc_content" data-timecontrol="' . $time_out . '">' . do_shortcode( $content ) . '</div>';
												$output .= bpmj_eddpc_control_time_js_script();

												// Tryb debugowania
												if ( BPMJ_EDD_PC_DEBUG_MODE )
													$output = bpmj_eddpc_debug_times( $output, $purchase_date, $time, $current_time );

												$locked = false;
												//return $output;
											} else {

												// Tryb debugowania
												if ( BPMJ_EDD_PC_DEBUG_MODE )
													$output = bpmj_eddpc_debug_times( '', $purchase_date, $time, $current_time );
											}
										} else { // Gdy nie wpisano czasu w shortcode, pokaż treść
											$locked	 = false;
											$output	 = $content;
										}
									}
								}
							}
						} else { // Gdy cena nie posiada wariantów ( jedna cena )
							//
                        // Porównuje ID produktu z ID podanym w shortcode
							if ( (int) $sc_ids == $product_id ) {

								// Sprawdza, czy nie minął czas publikacji ukrytej treści
								if ( $time ) {

									// Czas w timestamp w którym treść ma po być znów ukryta
									$time_out = $purchase_date + $time;

									// Sformatowana data wygaśnięcia
									$time_out_format = date_i18n( 'l d.m.Y, \g\o\d\z: G:i \s\. s', $time_out, true );

									if ( $time_out > $current_time ) {

										//  Opublikowanie treśći
										$output = '<div id="bpmj_eddpc_content" data-timecontrol="' . $time_out . '">' . do_shortcode( $content ) . '</div>';
										$output .= bpmj_eddpc_control_time_js_script();

										// Tryb debugowania
										if ( BPMJ_EDD_PC_DEBUG_MODE )
											$output = bpmj_eddpc_debug_times( $output, $purchase_date, $time, $current_time );

										$locked = false;
										//return $output;
									} else {

										// Tryb debugowania
										if ( BPMJ_EDD_PC_DEBUG_MODE )
											$output = bpmj_eddpc_debug_times( '', $purchase_date, $time, $current_time );
									}
								} else { // Gdy nie wpisano czasu w shortcode, pokaż treść
									$locked	 = false;
									$output	 = $content;
								}
							}
						}
					}
				}
			}
		}
	}

	if ( empty( $output ) )
		$output = $content;

	// Wyświetlenie treści zastępczej w miejscu ukrytego contentu.

	if ( !$locked xor $inv )
		return do_shortcode( $output );

	$output = '';

	if ( isset( $attr[ 'img' ] ) && !empty( $attr[ 'img' ] ) ) {
		$output .= '<div class="bpmj-eddpc-unavailable-content bpmj-eddpc-img">';
		$output .= '<img src="' . $attr[ 'img' ] . '" alt="' . __( 'Placeholder picture Paid Content', 'edd-paid-content' ) . '" />';
		$output .= '</div>';
	}

	// Gdy podano tylko atrybut text, wyświetl go w miejscu krytej treści.
	if ( isset( $attr[ 'txt' ] ) && !empty( $attr[ 'txt' ] ) ) {
		$output .= '<div class="bpmj-eddpc-unavailable-content bpmj-eddpc-text">' . $attr[ 'txt' ] . '</div>';
	}

	return $output;
}

add_shortcode( 'edd_pc_show_access', 'bpmj_eddpc_show_access' );

function bpmj_eddpc_show_access( $attr, $content ) {

	global $post;

	$attr = shortcode_atts( array(
		'id'		 => $post->ID,
		'download'	 => '',
		'nolimit'	 => __( 'No time limit.', 'edd-paid-content' ),
		'accessto'	 => __( 'Access to: %s %s.', 'edd-paid-content' ),
		'noaccess'	 => __( 'Lack of access.', 'edd-paid-content' ),
		'empty'		 => ''
	), $attr );

	if ( is_user_logged_in() ) {
		return bpmj_eddpc_get_access_time( $attr );
	}

	return '';
}

/*
 * Utworzenie Shortcode
 * 
 * Wyświetla tabelke z zakupionymi przez aktualnie zalogowanego
 * użytkownika produktami.
 *
 * Ten sam zakupiony produkt widoczny jest tylko raz.
 * 
 */

add_shortcode( 'edd_pc_purchased_products', 'bpmj_eddpc_purchased_products_shortcode' );

function bpmj_eddpc_purchased_products_shortcode($atts) {
	ob_start();

	$current_user_id = get_current_user_id();

	// Jeżeli nie zalogowany
	if ( 0 == $current_user_id ) {
		_e( 'Log in to view the history of purchased products.', 'edd-paid-content' );
		return ob_get_clean();
	}

	$access = get_user_meta( $current_user_id, '_bpmj_eddpc_access', true );
	// Jeżeli nie ma kupionych produktów
	if ( !is_array( $access ) ) {
	    ?>
        <div class='bpmj_edd_info_message'>
            <div class='bpmj_edd_info_message__icon-wrap'>
                <span class="dashicons dashicons-welcome-learn-more bpmj_edd_info_message__icon"></span>
            </div>

            <div class='bpmj_edd_info_message__content'>
                <h2><?= __('No products purchased!', 'edd-paid-content') ?></h2>
            </div>

        </div>
        <?php
		return ob_get_clean();
	}
	?>
	<table id="edd_user_history">

		<thead>
			<tr class="edd_purchase_row">	
				<th><?php _e( 'Product name', 'edd-paid-content' ); ?></th>
				<th><?php _e( 'Link', 'edd-paid-content' ); ?></th>
				<th><?php _e( 'Validity', 'edd-paid-content' ); ?></th>
				<th><?php _e( 'Actions', 'edd-paid-content' ); ?></th>
			</tr>
		</thead>


		<tbody>
			<?php
			// Pobieramy płatności danego usera	
			$customer	 = new EDD_Customer( $current_user_id, true );
			$payment_ids = explode( ',', $customer->payment_ids );
			$payment_ids = array_reverse( $payment_ids );

			// Przygotowanie tablicy z kupionymi produktami - brak powtarzających się ID produktów
			$products = array();
			foreach ( $payment_ids as $payment_id ) {
				$downloads		 = edd_get_payment_meta_cart_details( $payment_id, true );
				$purchase_data	 = edd_get_payment_meta( $payment_id );
				foreach ( $downloads as $download ) {
					if ( array_key_exists( $download[ 'id' ], $access ) ) {
						if ( !isset( $products[ $download[ 'id' ] ] ) && edd_is_payment_complete( $payment_id ) ) {
							$products[ $download[ 'id' ] ] = array(
								'info'			 => $download,
								'payment_id'	 => $payment_id,
								'payment_data'	 => $purchase_data
							);
						}
					}
				}
			}

			// Wyświetlenie produtków
			foreach ( $products as $id => $product ) {
			    $show_courses = (bool)($atts['show_courses'] ?? true);
			    $show_digital_products = (bool)($atts['show_digital_products'] ?? true);
			    $show_services = (bool)($atts['show_services'] ?? true);

                $is_digital_product = get_post_meta($id, 'wpi_resource_type', true) === 'digital_product';
                $is_service = get_post_meta($id, 'wpi_resource_type', true) === 'service';
                $is_course = (!$is_digital_product && !$is_service);

                $skip = (!$show_courses && $is_course)
                    || (!$show_digital_products && $is_digital_product)
                    || (!$show_services && $is_service);

                if ($skip) {
                    continue;
                }
                ?>
				<tr class="edd_purchase_row">

					<td><?php echo get_the_title( $id ); ?></td>

					<td>
						<?php
						// Pobranie price_id i wyświetlenie zakupionych linków
						$price_id		 = edd_get_cart_item_price_id( $product[ 'info' ] );
						$download_files	 = edd_get_download_files( $id, $price_id );
						if ( $download_files ) {
							foreach ( $download_files as $filekey => $file ) {
								$download_url = edd_get_download_file_url( $product[ 'payment_data' ][ 'key' ], $product[ 'payment_data' ][ 'email' ], $filekey, $id, $price_id );
								?>
								<a href="<?php echo esc_url( $download_url ); ?>">
									<?php echo isset( $file[ 'name' ] ) ? esc_html( $file[ 'name' ] ) : get_the_title( $id ); ?>
								</a>
								<?php
								// tylko jeden (pierwszy) link
								break;
							}
						}
						?>
					</td>

					<td>
						<?php
						if ( $access[ $id ][ 'access_time' ] ) {
							echo date( 'd.m.Y H:i:s', $access[ $id ][ 'access_time' ] );
						} else {
							_e( 'No limit', 'edd-paid-content' );
						}
						?>
					</td>

					<td>
						<?php
						// Jeżeli mamy wygenerowany kod rabatowy i jest aktywny i jest ustalony czas dostępu
						if ( isset( $access[ $id ][ 'discount' ] ) && edd_is_discount_active( $access[ $id ][ 'discount' ] ) && ( get_post_meta( $access[ $id ][ 'discount' ], '_edd_discount_uses', true ) == '0' ) && $access[ $id ][ 'access_time' ] ) {

							// Kod rabatowy
							$discount_code = get_post_meta( $access[ $id ][ 'discount' ], '_edd_discount_code', true );

							// Typ kodu (percent / flat)
							$discount_type = get_post_meta( $access[ $id ][ 'discount' ], '_edd_discount_type', true );
							if ( $discount_type == 'percent' ) {
								$discount_type = '%';
							} else {
								$discount_type = edd_get_currency();
							}

							// Kwota / Liczba obniżki
							$discount_amount = get_post_meta( $access[ $id ][ 'discount' ], '_edd_discount_amount', true );

							// Kiedy wygasa kod. Jeżeli nie wygasa wyświetlamy "bez limitu"
							$discount_expiration = get_post_meta( $access[ $id ][ 'discount' ], '_edd_discount_expiration', true );
							if ( $discount_expiration ) {
								$discount_expiration = __( 'for', 'edd-paid-content' ) . ' ' . date( 'd.m.Y', strtotime( $discount_expiration ) );
							} else {
								$discount_expiration = __( 'no limit', 'edd-paid-content' );
							}

							// Sprawdzamy czy dany produkt ma warianty cenowe. Jeżeli tak to zaciągamy url do zakupu z parametrem "price_id" z ostatniego zamówienia
							$product_variable = bpmj_eddpc_get_product_variable( $id, $current_user_id, $discount_code );
							if ( $product_variable ) {
								$url = $product_variable[ 'url' ];
							} else {
								$url = edd_get_checkout_uri( array(
									'edd_action'	 => 'add_to_cart',
									'download_id'	 => $id,
									'dicount'		 => $discount_code
								) );
							}
							echo '<a href="' . $url . '">' . __( 'Extend with a discount', 'edd-paid-content' ) . '</a><br>';
							echo '<small>' . __( 'Discount code: ', 'edd-paid-content' ) . ' ' . $discount_code . ' (' . $discount_amount . $discount_type . ') ' . __( 'valid', 'edd-paid-content' ) . ' ' . $discount_expiration;


							// Jeżeli mamy podany czas dostępu (bez kodu zniżkowego) 	
						} elseif ( $access[ $id ][ 'access_time' ] ) {

							// Sprawdzamy czy dany produkt ma warianty cenowe. Jeżeli tak to zaciągamy url do zakupu z parametrem "price_id" z ostatniego zamówienia
							$product_variable = bpmj_eddpc_get_product_variable( $id, $current_user_id );
							if ( $product_variable ) {
								$url = $product_variable[ 'url' ];
							} else {
								$url = edd_get_checkout_uri( array(
									'edd_action'	 => 'add_to_cart',
									'download_id'	 => $id,
								) );
							}
							echo '<a class="edd-pc-button" href="' . $url . '">' . __( 'Renew', 'edd-paid-content' ) . '</a><br>';
						}

                        $next_payment = function_exists( 'edd_user_get_nearest_pending_recurring_payment' ) ? edd_user_get_nearest_pending_recurring_payment( $current_user_id, $id ) : null;
						if ($next_payment) {
                            echo '<a href="' . wp_nonce_url( home_url( '?bpmj_eddcm_cancel_subscription&purchase_id=' . $next_payment->ID ), 'edd_payment_nonce' ) . '" class="btn-eddcm btn-eddcm-primary btn-eddcm-cancel-subscription">' . __( 'Cancel subscription', BPMJ_EDDCM_DOMAIN ) . '</a>';
                        }
						?>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>

	</table>

	<?php
	return ob_get_clean();
}

add_shortcode( 'edd_pc_accessible_courses', 'bpmj_eddpc_accessible_courses' );

function bpmj_eddpc_accessible_courses($atts) {
    ob_start();

    $current_user_id = get_current_user_id();

    if ( 0 == $current_user_id ) {
        _e( 'Log in to view the history of purchased products.', 'edd-paid-content' );
        return ob_get_clean();
    }

    $access = get_user_meta( $current_user_id, '_bpmj_eddpc_access', true );

    if ( !is_array( $access ) ) {
        ?>
        <div class='bpmj_edd_info_message'>
            <div class='bpmj_edd_info_message__icon-wrap'>
                <span class="dashicons dashicons-welcome-learn-more bpmj_edd_info_message__icon"></span>
            </div>

            <div class='bpmj_edd_info_message__content'>
                <h2><?= __('No products purchased!', 'edd-paid-content') ?></h2>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }
    ?>
    <table id="edd_user_history">

        <thead>
        <tr class="edd_purchase_row">
            <th><?php _e( 'Product name', 'edd-paid-content' ); ?></th>
            <th><?php _e( 'Link', 'edd-paid-content' ); ?></th>
            <th><?php _e( 'Validity', 'edd-paid-content' ); ?></th>
            <th><?php _e( 'Actions', 'edd-paid-content' ); ?></th>
        </tr>
        </thead>


        <tbody>
        <?php

        foreach ( $access as $id => $product ) {

            $resource_type = get_post_meta($id, 'wpi_resource_type', true);
            $is_course = empty($resource_type) || $resource_type === Resource_Type::COURSE;

            if (!$is_course) {
                continue;
            }

            ?>
            <tr class="edd_purchase_row">

                <td><?php echo get_the_title( $id ); ?></td>

                <td>
                    <?php
                    $download_url = get_permalink(WPI()->courses->get_course_by_product( $id ));
                    ?>
                    <a href="<?php echo esc_url( $download_url ); ?>">
                        <?php echo isset( $file[ 'name' ] ) ? esc_html( $file[ 'name' ] ) : get_the_title( $id ); ?>
                    </a>
                </td>

                <td>
                    <?php
                    if ( $access[ $id ][ 'access_time' ] ) {
                        echo date( 'd.m.Y H:i:s', bpmj_eddpc_adjust_timestamp($access[ $id ][ 'access_time' ]) );
                    } else {
                        _e( 'No limit', 'edd-paid-content' );
                    }
                    ?>
                </td>

                <td>
                    <?php
                    if ( isset( $access[ $id ][ 'discount' ] ) && edd_is_discount_active( $access[ $id ][ 'discount' ] ) && ( get_post_meta( $access[ $id ][ 'discount' ], '_edd_discount_uses', true ) == '0' ) && $access[ $id ][ 'access_time' ] ) {

                        $discount_code = get_post_meta( $access[ $id ][ 'discount' ], '_edd_discount_code', true );

                        $discount_type = get_post_meta( $access[ $id ][ 'discount' ], '_edd_discount_type', true );
                        if ( $discount_type == 'percent' ) {
                            $discount_type = '%';
                        } else {
                            $discount_type = edd_get_currency();
                        }

                        $discount_amount = get_post_meta( $access[ $id ][ 'discount' ], '_edd_discount_amount', true );

                        $discount_expiration = get_post_meta( $access[ $id ][ 'discount' ], '_edd_discount_expiration', true );
                        if ( $discount_expiration ) {
                            $discount_expiration = __( 'for', 'edd-paid-content' ) . ' ' . date( 'd.m.Y', strtotime( $discount_expiration ) );
                        } else {
                            $discount_expiration = __( 'no limit', 'edd-paid-content' );
                        }

                        $product_variable = bpmj_eddpc_get_product_variable( $id, $current_user_id, $discount_code );
                        if ( $product_variable ) {
                            $url = $product_variable[ 'url' ];
                        } else {
                            $url = edd_get_checkout_uri( array(
                                'edd_action'	 => 'add_to_cart',
                                'download_id'	 => $id,
                                'dicount'		 => $discount_code
                            ) );
                        }
                        echo '<a href="' . $url . '">' . __( 'Extend with a discount', 'edd-paid-content' ) . '</a><br>';
                        echo '<small>' . __( 'Discount code: ', 'edd-paid-content' ) . ' ' . $discount_code . ' (' . $discount_amount . $discount_type . ') ' . __( 'valid', 'edd-paid-content' ) . ' ' . $discount_expiration;

                    } elseif ( $access[ $id ][ 'access_time' ] ) {

                        $product_variable = bpmj_eddpc_get_product_variable( $id, $current_user_id );
                        if ( $product_variable ) {
                            $url = $product_variable[ 'url' ];
                        } else {
                            $url = edd_get_checkout_uri( array(
                                'edd_action'	 => 'add_to_cart',
                                'download_id'	 => $id,
                            ) );
                        }
                        echo '<a class="edd-pc-button" href="' . $url . '">' . __( 'Renew', 'edd-paid-content' ) . '</a><br>';
                    }

                    $next_payment = function_exists( 'edd_user_get_nearest_pending_recurring_payment' ) ? edd_user_get_nearest_pending_recurring_payment( $current_user_id, $id ) : null;
                    if ($next_payment) {
                        echo '<a href="' . wp_nonce_url( home_url( '?bpmj_eddcm_cancel_subscription&purchase_id=' . $next_payment->ID ), 'edd_payment_nonce' ) . '" class="btn-eddcm btn-eddcm-primary btn-eddcm-cancel-subscription">' . __( 'Cancel subscription', BPMJ_EDDCM_DOMAIN ) . '</a>';
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>

    </table>

    <?php
    return ob_get_clean();
}