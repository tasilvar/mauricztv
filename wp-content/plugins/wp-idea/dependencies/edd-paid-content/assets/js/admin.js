jQuery( document ).ready( function ( $ ) {
    'use strict';

    $( 'body' ).on( 'change', 'select.bpmj_eddpc_download', function () {
        var $this = $( this ), download_id = $this.val(), key = $this.data( 'key' ), postData;

        if ( parseInt( download_id ) > 0 ) {
            $this.parent().next( 'td' ).find( 'select' ).remove();
            $this.parent().next().find( '.bpmj_eddpc_loading' ).show();

            postData = {
                action: 'bpmj_eddpc_check_for_download_price_variations',
                download_id: download_id,
                key: key
            };

            $.ajax( {
                type: "POST",
                data: postData,
                url: ajaxurl,
                success: function ( response ) {
                    if ( response ) {
                        $this.parent().next( 'td' ).find( '.bpmj_eddpc_variable_none' ).hide();
                        $( response ).appendTo( $this.parent().next( 'td' ) );
                    } else {
                        $this.parent().next( 'td' ).find( '.bpmj_eddpc_variable_none' ).show();
                    }
                }
            } ).fail( function ( data ) {
                if ( window.console && window.console.log ) {
                    console.log( data );
                }
            } );

            $this.parent().next().find( '.bpmj_eddpc_loading' ).hide();
        } else {
            $this.parent().next( 'td' ).find( '.bpmj_eddpc_variable_none' ).show();
            $this.parent().next( 'td' ).find( '.edd_price_options_select' ).remove();
        }
    } );

    var timeStartValue = $( '#timeStart' ).val();
    var timeStartDisabled = $( '#timeStart' ).is( ':disabled' );
    $( '#timeStart' ).after( '<select name="edd_settings[bpmj_renewals_start]" id="timeStart" ' + (timeStartDisabled ? 'disabled="disabled"' : '') + '></select>' ).remove();
    for ( var i = 0; i < 20; i++ ) {
        var selected = timeStartValue == i ? 'selected' : '';
        $( '#timeStart' ).append( '<option value="' + i + '" ' + selected + '>' + i + ':00</option>' );
    }

    var timeEndValue = $( '#timeEnd' ).val();
    var timeEndDisabled = $( '#timeEnd' ).is( ':disabled' );
    $( '#timeEnd' ).after( '<select name="edd_settings[bpmj_renewals_end]" id="timeEnd" ' + (timeEndDisabled ? 'disabled="disabled"' : '') + '></select>' ).remove();
    timeStartValue = timeStartValue ? parseInt( timeStartValue ) + 5 : 5;
    for ( var i = timeStartValue; i < 25; i++ ) {
        var selected = timeEndValue == i ? 'selected' : '';
        $( '#timeEnd' ).append( '<option value="' + i + '" ' + selected + '>' + i + ':00</option>' );
    }

    $( document.body ).on( 'change', '#timeStart', function () {
        var value = parseInt( $( '#timeStart' ).val() ) + 5;
        $( '#timeEnd' ).html( '' );
        for ( value; value < 25; value++ ) {
            $( '#timeEnd' ).append( '<option value="' + value + '" ' + selected + '>' + value + ':00</option>' );
        }
    } );


    $.datetimepicker.setLocale( 'pl' );
    /**
     * Modyfikowanie dostępu
     */
    $( document ).on( 'click', '[data-action="edit-access"]', function ( e ) {
        e.preventDefault();

        var id = $( this ).data( 'id' );

        $( '#customer-tables-wrapper table tr.edit-access' ).each( function () {
            $( this ).css( 'display', 'none' );
        } );

        $( '#datetimepicker-' + id ).datetimepicker( {
            format: 'd.m.Y H:i:s',
            inline: true,
            step: 5,
            scrollMonth: false,
            scrollInput: false,
            minDate: 0
        } );

        $( '.download-' + id ).css( 'display', '' );

    } );


    /**
     * Zapis access time
     */
    $( document ).on( 'click', '[data-action="save-access"]', function ( e ) {
        e.preventDefault();

        var download_id = $( this ).data( 'download-id' ),
            user_id = $( this ).data( 'user-id' ),
            time = $( '#datetimepicker-' + download_id ).val();

        var postData = {
            action: 'bpmj_eddpc_save_access_time',
            time: time,
            user_id: user_id,
            download_id: download_id
        };

        $.ajax( {
            type: "POST",
            data: postData,
            dataType: "json",
            url: ajaxurl,
            beforeSend: function () {
                $( '.download-' + download_id + ' .loader-container' ).fadeIn( 'fast' );
            },
            success: function ( response ) {
                $( '.access-time-' + download_id ).html( response );
            },
            complete: function () {
                $( '.download-' + download_id + ' .loader-container' ).fadeOut( 'fast' );
            }
        } ).fail( function ( data ) {
            if ( window.console && window.console.log ) {
                console.log( data );
            }
        } );
    } )


    /**
     * Zapis access time
     */
    $( document ).on( 'change', '[data-action="no-limit-access"]', function ( e ) {
        e.preventDefault();

        var download_id = $( this ).data( 'download-id' ),
            user_id = $( this ).data( 'user-id' );

        var postData = {
            action: 'bpmj_eddpc_no_limit_access',
            nolimit: $( this ).prop( 'checked' ),
            user_id: user_id,
            download_id: download_id
        };

        $.ajax( {
            type: "POST",
            data: postData,
            dataType: "json",
            url: ajaxurl,
            beforeSend: function () {
                $( '.download-' + download_id + ' .loader-container' ).fadeIn( 'fast' );
            },
            success: function ( response ) {
                $( '.access-time-' + download_id ).html( response.info );

                if ( response.status ) {
                    $( '.download-' + download_id ).find( '.calendar' ).css( 'display', 'none' );
                } else {
                    $( '.download-' + download_id ).find( '.calendar' ).css( 'display', '' );
                    $( '#datetimepicker-' + download_id ).attr( 'value', response.date ).datetimepicker( 'reset' );
                }

            },
            complete: function () {
                $( '.download-' + download_id + ' .loader-container' ).fadeOut( 'fast' );
            }
        } ).fail( function ( data ) {
            if ( window.console && window.console.log ) {
                console.log( data );
            }
        } );
    } )


    /**
     * Zapis total time
     */
    $( document ).on( 'click', '[data-action="save-total-time"]', function ( e ) {
        e.preventDefault();

        var download_id = $( this ).data( 'download-id' ),
            user_id = $( this ).data( 'user-id' ),
            time = $( '#total-time-' + download_id ).val();

        var postData = {
            action: 'bpmj_eddpc_save_total_time',
            time: time,
            user_id: user_id,
            download_id: download_id
        };

        $.ajax( {
            type: "POST",
            data: postData,
            dataType: "json",
            url: ajaxurl,
            beforeSend: function () {
                $( '.download-' + download_id + ' .loader-container' ).fadeIn( 'fast' );
            },
            complete: function () {
                $( '.download-' + download_id + ' .loader-container' ).fadeOut( 'fast' );
            }
        } ).fail( function ( data ) {
            if ( window.console && window.console.log ) {
                console.log( data );
            }
        } );
    } )

    /**
     * Usunięcie wpisu
     */
    $( document ).on( 'click', '[data-action="delete-access"]', function ( e ) {
        e.preventDefault();

        if ( !confirm( bpmj_eddpc.delete_renewal_confirm ) ) {
            return;
        }

        var download_id = $( this ).data( 'download-id' ),
            user_id = $( this ).data( 'user-id' );

        var postData = {
            action: 'bpmj_eddpc_delete_access',
            user_id: user_id,
            download_id: download_id
        };

        var $td = $( this ).closest( 'td' );
        $td.css( {
            height: $td.height() + 'px',
            verticalAlign: 'middle',
            textAlign: 'center'
        } );
        $td.html( '<span class="spinner is-active" style="float: none;"></span>' );

        var $tr = $td.closest( 'tr' );
        var $tr_next = $tr.next( 'tr.download-' + download_id );

        $.ajax( {
            type: "POST",
            data: postData,
            dataType: "json",
            url: bpmj_eddpc.ajax,
            success: function ( json ) {
                if ( !json.error ) {
                    $tr.remove();
                    $tr_next.remove();
                }
            }

        } ).fail( function ( data ) {
            if ( window.console && window.console.log ) {
                console.log( data );
            }
        } );
    } )

} );