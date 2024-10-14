(
    function ( $ ) {
        $( document ).ready( function () {
            if ( 0 < $( '#login' ).length ) {

                $panel_logowania = $( '#login' );
                $pass = $panel_logowania.find( '#pass1' );
                $show_pass = $panel_logowania.find( 'button.wp-hide-pw' );
                $show_pass.remove();

                if ( 1 === $pass.length ) {
                    $( '#pass1' ).get( 0 ).type = 'text';

                    var ikona_hidden = $( '<span class="haslo_ikona dashicons dashicons-visibility"></span>' ),
                    ikona_visibility = $( '<span class="haslo_ikona dashicons dashicons-hidden"></span>' );

                    ikona_hidden.css( 'display', 'none' );

                    ikona_hidden.on( 'click', function () {
                        ikona_hidden.toggle();
                        ikona_visibility.toggle();

                        $( '#pass1' ).get( 0 ).type = 'text';
                    } );

                    ikona_visibility.on( 'click', function () {
                        ikona_hidden.toggle();
                        ikona_visibility.toggle();

                        $( '#pass1' ).get( 0 ).type = 'password';
                    } );

                    if( 1 !== $( '.password-input-wrapper' ).length ) {
                        $( '#pass1' ).wrap( '<div class="password-input-wrapper"></div>' );
                    }
                    $( '.password-input-wrapper' ).append( ikona_hidden ).append( ikona_visibility );
                }
            }

            // Course navigation
            $( '#course_navigation' ).on( 'click', 'li.module', function () {
                $( this ).toggleClass( 'active' );
            } );

            $( '#course-progress' ).on( 'click', 'input[type="checkbox"]', function () {
                var $course_progress = $( '#course-progress' );
                var $box = $course_progress.closest( '.box' );
                var data = {
                    action: 'wpi_handler',
                    finished: $( this ).is( ':checked' ) ? '1' : '0',
                    course_page_id: $course_progress.data( 'coursePageId' ),
                    lesson_page_id: $course_progress.data( 'lessonPageId' )
                };
                data[wpidea.nonce_name] = wpidea.nonce_value
                var spinner_url = $course_progress.data('spinner');

                $( "<div class='overlay' id='course-progress-overlay'></div>" )
                    .css( {
                        'z-index': 100,
                        'position': 'absolute',
                        'background': '#fff url(' + spinner_url + ') no-repeat center center',
                        'opacity': 0.5,
                        'top': $box.offset().top,
                        'left': $box.offset().left,
                        'width': $box.outerWidth(),
                        'height': $box.outerHeight()
                    } )
                    .appendTo( 'body' );

                $.ajax( {
                    type: "POST",
                    data: data,
                    dataType: "json",
                    url: wpidea.ajaxurl+'?wpi_route=courses/update_course_progress',
                    success: function ( response ) {
                        $( '#course-progress' ).html( response.course_progress_widget );
                        $( '#course-navigation-section' ).html( response.course_navigation_section );
                        $( '#course-progress-overlay' ).remove();
                    },
                    error: function ( jqXHR, textStatus, errorThrown ) {
                        console.log('Error: ' + jqXHR.responseJSON.error_message)
                    }
                } );
            } );

            /*
             The code below adjusts the behavior of menus expansion - if the menu item is near the right edge,
             we want it to expand to the left, so that the contents are not displayed outside of document canvas.
             */
            var $css_menu = $( '#cssmenu' );
            var menuWidth = $css_menu.width();
            $css_menu.find( 'li.has-sub' ).each( function () {
                var position = $( this ).position();
                // 190 is the default submenu's width - 2 * 190 is a combined width of submenu and submenu's submenu
                if ( menuWidth - position.left < 2 * 190 ) {
                    $( this ).addClass( 'right-align' );
                }
            } );

            $css_menu.on( 'click', 'li.grayed a', function () {
                return false;
            } );

            $( 'form.edd-sales-disabled' ).each( function () {
                var $form = $( this );
                var $a = $form.find( 'a' );
                $a.on('click', function () {
                    return false;
                } );
                $a.attr( 'title', $form.data( 'eddcmSalesDisabledReasonLong' ) || wpidea.sales_disabled );
                if ( $form.data( 'eddcmSalesDisabledReason' ) ) {
                    $a.find( '.edd-add-to-cart-label' ).text( $form.data( 'eddcmSalesDisabledReason' ) );
                }
            } );

            $( '.bpmj-eddcm-variable-price-purchase-limit-reached' ).each( function () {
                var $li = $( this ).closest( 'li' );
                var $radio = $li.find( 'input[type="radio"]' );
                if ( 1 === $radio.length ) {
                    var radio = $radio.get( 0 );
                    radio.checked = false;
                    radio.disabled = true;
                }
            } );

            $( document ).ready( function () {

                /**
                 * Fix gutenberg embed video
                 */
                var videoFigureElements = $( '.wp-block-embed' );
                videoFigureElements.each( function ( i, figure ) {
                    var wpiVideoWrapper = $( figure ).find( '.wpidea-embed-wrapper' );

                    if ( wpiVideoWrapper ) {
                        var iframe = wpiVideoWrapper.find( 'iframe' );
                        var figureContent = $( figure ).find( '.wp-block-embed__wrapper' );

                        if ( !iframe || !figureContent )
                            return;

                        wpiVideoWrapper.remove();
                        $( figureContent ).append( iframe );
                    }
                } )
            } )

            /**
             * Fix 100% discount invoice data issue
             */
            var DiscountFix = {
                DISCOUNT_APPLIED: 'edd_discount_applied',

                hideInvoiceFormOnFullDiscount: function(){
                    let _this = this;

                    $(document.body).on( _this.DISCOUNT_APPLIED, function(e, payload){
                        let isFullDiscount = ( '0.00' == payload.total_plain );

                        if( ! isFullDiscount ) return;

                        _this.uncheckAndHideInput();
                        _this.hideAndClearInvoiceFields();
                    } );
                },

                uncheckAndHideInput: function(){
                    $('#bpmj_edd_invoice_data_invoice_check').val(0).hide();
                    $('.bpmj_edd_invoice_data_invoice_check').hide();
                },

                hideAndClearInvoiceFields: function(){
                    $('.bpmj_edd_invoice_data_invoice_force').hide();
                    $('.bpmj_edd_invoice_data_invoice_force input').val('');

                    $('.bpmj_edd_invoice_data_invoice').hide();
                    $('.bpmj_edd_invoice_data_invoice input').val('');
                }
            }
            DiscountFix.hideInvoiceFormOnFullDiscount();
        } );
    }
)
    (
        jQuery
        );

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}
