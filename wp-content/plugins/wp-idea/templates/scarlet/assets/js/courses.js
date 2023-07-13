(
    function ( $ ) {
        /**
         * Toast util
         */
        window.showToast = (content, icon = 'info', hideAfter = 7000, showLoader = true) => {
            $.toast({
                hideAfter: hideAfter,
                text: content,
                icon: icon,
                loader: showLoader
            });
        }

        /**
         *  Handle quizzes
         */
        var loaded_quiz_events = function () {
            $( '#bpmj-eddcm-questions-carousel, #bpmj-eddcm-questions-carousel-answers' ).on( 'slid.bs.carousel', function () {
                let items = $( 'div.item', this ),
                    index = items.filter( '.active' ).index() + 1,
                    count = items.length;

                if ( 1 === index ) {
                    $( this ).next().find('#quizy_contenter_paginacja_prev').addClass( 'nieaktywny' );
                } else {
                    $( this ).next().find('#quizy_contenter_paginacja_prev').removeClass( 'nieaktywny' );
                }

                if ( count === index ) {
                    $( this ).next().find('#quizy_contenter_paginacja_next').css( 'display', 'none' );
                    $( this ).next().find('#quizy_contenter_paginacja_finish').css( 'display', 'inline-block' );
                } else {
                    $( this ).next().find('#quizy_contenter_paginacja_next').css( 'display', 'inline-block' );
                    $( this ).next().find('#quizy_contenter_paginacja_finish').css( 'display', 'none' );
                }

                var width = 0;
                if ( index > 1 )
                    width = ( ( ( index - 1 ) / count ) * 100 );

                var width_text = width;

                if ( 0 === width ) {
                    width = 5;
                    width_text = 0;
                }

                $( this ).closest( '.quizy_contenter' ).find( '.postep_contenter_postep' )
                    .css( 'width', width + '%' )
                    .find( '.postep_liczba' )
                    .text( parseInt( width_text ) + '%' );
            } ).on( 'slide.bs.carousel', function ( e ) {
                var nextH = $( e.relatedTarget ).height();
                $( this ).find( '.active.item' ).parent().animate( {
                    height: nextH
                }, 500 );
            } ).trigger( 'slid.bs.carousel' );

            $( '#quizy_contenter_paginacja_finish' ).on( 'click', function () {
                var active_contenter = $( '.quizy_contenter_quiz.active' ),
                    file_input = active_contenter.find( '.bpmj-eddcm-file-input' );

                if ( file_input.length > 0 ) {
                    if ( active_contenter.hasClass( 'no-quiz-file-alert' ) ) {
                        $( '#bpmj-eddcm-questions-form' ).trigger( 'submit' );
                        return;
                    }

                    if ( file_input.val().length === 0 ) {
                        active_contenter.addClass( 'no-quiz-file-alert' );
                        alert( wpidea.no_quiz_file );
                    } else {
                        $( '#bpmj-eddcm-questions-form' ).trigger( 'submit' );
                    }
                } else {
                    $( '#bpmj-eddcm-questions-form' ).trigger( 'submit' );
                }
            } );

            $( '#quizy_contenter_paginacja_next' ).on( 'click', function () {
                var active_contenter = $( '.quizy_contenter_quiz.active' ),
                    file_input = active_contenter.find( '.bpmj-eddcm-file-input' );

                if ( file_input.length > 0 ) {
                    if ( active_contenter.hasClass( 'no-quiz-file-alert' ) )
                        return;

                    if ( file_input.val().length === 0 ) {
                        active_contenter.addClass( 'no-quiz-file-alert' );
                        alert( wpidea.no_quiz_file );
                    }
                }
            } );

            $( '#bpmj-eddcm-questions-form' ).find( 'input, select, textarea' )
                .on( 'change', function () {
                    var data = new FormData();

                    $( '#bpmj-eddcm-questions-form' ).find( 'input, select, textarea' )
                        .each( function () {
                            var name = $( this ).attr( 'name' );
                            if ( $( this ).is( ':radio' ) ) {
                                data.append( name, $( '[name="' + name + '"]' ).filter( ':checked' ).val() );
                            } else if ( $( this ).is( ':checkbox' ) ) {
                                var values = [ ];
                                $( '[name="' + name + '"]' ).filter( ':checked' )
                                    .each( function () {
                                        values.push( $( this ).val() );
                                    } );

                                $( $( this ).data( 'to-id' ) ).val( values.join( ',' ) );
                                data.append( name.replace( '_fake', '' ), values.join( ',' ) );
                            } else if ( $( this ).is( 'textarea' ) || $( this ).is( 'select' ) ) {
                                data.append( name, $( this ).val() );
                            } else if ( $( this ).hasClass( 'bpmj-eddcm-file-input' ) ) {
                                if ( $( this )[0].files.length > 0 ) {
                                    data.append( name, $( this )[0].files[0] );
                                }
                            } else if ( $( this ).is( ':hidden' ) ) {
                                data.append( name, $( '[name="' + name + '"]' ).val() );
                            }
                        } );

                    data.append( 'action', 'wpi_handler' );
                    data.append( wpidea.nonce_name, wpidea.nonce_value );


                    $.ajax( {
                        type: "POST",
                        data: data,
                        processData: false,
                        contentType: false,
                        dataType: "html",
                        url: wpidea.ajaxurl + '?wpi_route=courses/update_quiz',
                        error: function ( jqXHR, textStatus, errorThrown ) {
                            console.log('Error: ' + jqXHR.responseJSON.error_message)
                        }
                    } );
                } );

            $( '.bpmj-eddcm-file-input' ).on( 'change', function () {
                var types_array = $( this ).data( 'types' ).split( ',' );
                if ( $.inArray( this.files[0].type, types_array ) === -1 ) {
                    alert( wpidea.wrong_quiz_file_type );
                    $( this ).val( '' );
                }
            } );

            $( '#bpmj-eddcm-tfa' ).countdown( ( new Date().getTime() + $( '#bpmj-eddcm-tfa' ).data( 'time' ) ), function ( event ) {
                $( this ).html( event.strftime( '%H:%M:%S' ) );
            } ).on( 'finish.countdown', function () {
                $( '#bpmj-eddcm-questions-form' ).trigger( 'submit' );
            } );
        };

        function scrollToAPositionWhereQuizTopIsVisible() {
            const quizContainer = document.querySelector('.quizy_contenter');
            if (quizContainer) {
                let additionalOffsetToAccountForStickyMenuHeight = 100;

                window.scrollTo(0, quizContainer.offsetTop - additionalOffsetToAccountForStickyMenuHeight)
            }
        }

        $( document ).ready( function () {
            $( '#bpmj-eddcm-start-quiz-button' ).on( 'click', function ( e ) {
                e.preventDefault();

                if ( $( this ).hasClass( 'loading' ) )
                    return;

                $( this ).addClass( 'loading' );

                var button = $( this );

                button.html( '<span class="dashicons dashicons-update"></span>' );


                var data = {
                    action: 'wpi_handler',
                    quiz_post_id: $( this ).data( 'quiz' ),
                    course_post_id: $( this ).data( 'course' ),
                };
                data[wpidea.nonce_name] = wpidea.nonce_value

                $.ajax( {
                    type: "POST",
                    data: data,
                    dataType: "json",
                    url: wpidea.ajaxurl + '?wpi_route=courses/get_quiz',
                    success: function ( response ) {
                        button.closest( '.quizy_contenter' ).html( response.quiz );
                        loaded_quiz_events();

                        scrollToAPositionWhereQuizTopIsVisible();
                    },
                    error: function ( jqXHR, textStatus, errorThrown ) {
                        alert(jqXHR.responseText.replace(/(<([^>]+)>)/gi, ""));
                    }
                } );
            } );

            loaded_quiz_events();

            $( '.bpmj-eddcm-quiz-again' ).on( 'click', function ( e ) {
                e.preventDefault();

                if ( $( this ).hasClass( 'loading' ) )
                    return;

                $( this ).addClass( 'loading' );

                var button = $( this );

                button.html( '<span class="dashicons dashicons-update"></span>' );

                var data = {
                    action: 'wpi_handler',
                    quiz_post_id: $( this ).data( 'quiz' ),
                    course_post_id: $( this ).data( 'course' )
                };
                data[wpidea.nonce_name] = wpidea.nonce_value

                $.ajax( {
                    type: "POST",
                    data: data,
                    dataType: "json",
                    url: wpidea.ajaxurl + '?wpi_route=courses/get_quiz',
                    success: function ( response ) {
                        var podsumowanie = button.closest( '.quiz_podsumowanie' );

                        $( '<div class="quizy_contenter"></div>' ).html( response.quiz )
                            .insertBefore( podsumowanie );
                        podsumowanie.remove();
                        loaded_quiz_events();

                        scrollToAPositionWhereQuizTopIsVisible();
                    },
                    error: function ( jqXHR, textStatus, errorThrown ) {
                        alert(jqXHR.responseText.replace(/(<([^>]+)>)/gi, ""));
                    }
                } );

                $('.niepoprawne_odp').remove();
            } );

            $( '.tab ul li' ).on('click', function () {
                id = $( this ).attr( 'id' );
                $( '.tab ul li' ).removeClass( 'active' );
                $( this ).addClass( 'active' );
                $( '.tab_cont' ).hide();
                $( '.' + id ).show();
            } );
        } );
        $( document ).ready( function () {
            $( '#menu_mobile' ).on('click', function () {
                $( '.menu_glowne' ).toggle();
            } );

            $( '#menu li a' ).on('click', function ( e ) {
                if ( window.matchMedia( '(max-width: 768px)' ).matches ) {
                    let hasSiblingSubMenu = ( $( this ).siblings( '.sub-menu' ).length > 0 );
                    let hasSiblingSubMenuWrap = ( $( this ).siblings( '.sub-menu-scroll-wrapper' ).length > 0 );
                    if ( hasSiblingSubMenu || hasSiblingSubMenuWrap ) {
                        e.preventDefault();
                        $( this ).parent().toggleClass( 'active' );
                    }
                }
            } );

            $( '.user' ).on('click', function () {
                $( '#user_menu' ).toggle();
            } );

            // submenu overflow fix, @see https://codepen.io/agop/pen/itbew and https://css-tricks.com/popping-hidden-overflow/
            $( ".has-sub > .sub-menu, ul:not(.sub-menu) > .menu-item-has-children > .sub-menu" ).wrap( "<div class='sub-menu-scroll-wrapper'></div>" );
            $( function () {
                // whenever we hover over a menu item that has a submenu
                $( '.sub-menu-scroll-wrapper > .sub-menu > li' ).on( 'mouseover', function () {
                    var $menuItem = $( this ),
                        $submenuWrapper = $( '> .sub-menu', $menuItem );
                    // grab the menu item's position relative to its positioned parent
                    var menuItemPos = $menuItem.position();

                    // place the submenu in the correct position relevant to the menu item
                    $submenuWrapper.css( {
                        top: menuItemPos.top,
                        left: "245px"
                    } );
                } );
            } );
            // end submenu fix

        } );
        $( document ).ready( function () {
            // Course navigation
            $( '#course_navigation' ).on( 'click', 'li.module', function () {
                $( this ).toggleClass( 'active' );
            } );

            $( '.niepoprawne' ).on('click', function ( event ) {
                event.preventDefault();
                $('.niepoprawne_odp').show();
                $('.quiz_podsumowanie').hide();

                let answerPreview = $( '#bpmj-eddcm-questions-carousel, #bpmj-eddcm-questions-carousel-answers' );

                let activeElementHeight = 0;
                answerPreview.find('.quizy_contenter_quiz').each(function (i) {
                    if (i === 0) {
                        $(this).addClass('active');
                        activeElementHeight = $(this).height();
                    } else {
                        $(this).removeClass('active');
                    }
                })

                $('.carousel-inner').height(activeElementHeight);
                answerPreview.trigger('slid.bs.carousel');
            });


            $( '.close_answers_preview' ).on('click', function ( e ) {

                $( '.niepoprawne_odp' ).hide();
                $('.quiz_podsumowanie').show();
            } );

            $( '#course-progress' ).on( 'change', 'input[type="checkbox"]', function () {
                var $course_progress = $( '#course-progress' );
                var $box = $( '.lekcja_top' );
                var data = {
                    action: 'wpi_handler',
                    finished: $( this ).is( ':checked' ) ? '1' : '0',
                    course_page_id: $course_progress.data( 'coursePageId' ),
                    lesson_page_id: $course_progress.data( 'lessonPageId' ),
                    nonce: $course_progress.data( 'nonce' ),
                };
                data[wpidea.nonce_name] = wpidea.nonce_value
                var spinner_url = $course_progress.data('spinner');

                $( "<div class='overlay' id='course-progress-overlay'></div>" )
                    .css( {
                        'z-index': 100,
                        'position': 'fixed',
                        'background': '#fff url(' + spinner_url + ') no-repeat center center',
                        'opacity': 0.5,
                        'top': $box.position().top,
                        'left': $box.position().left,
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

                        if ( response.user_can_go_to_next_lesson ) {
                            $( '.lekcja_nas' ).removeClass( 'disabled' ).animate( { opacity: 1 }, 400 );
                            $( '.lekcja_top_nav__item--next' ).removeClass( 'disabled' ).animate( { opacity: 1 }, 400 );
                        } else {
                            $( '.lekcja_nas' ).animate( { opacity: 0 }, 400, function () {
                                $( this ).addClass( 'disabled' );
                                $( '.lekcja_top_nav__item--next' ).addClass( 'disabled' );
                            } );
                        }
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
        } );
        $( document ).ready( function () {
            $panel_logowania = $( '#panel_logowania' );
            if ( 0 === $panel_logowania.length ) {
                return;
            }

            $pass = $panel_logowania.find( '#pass1' );
            $user_login = $panel_logowania.find( '#user_login' ).closest( 'p' );
            $user_password = $panel_logowania.find( '#user_pass' ).closest( 'p' );
            $user_remember = $panel_logowania.find( 'p.forgetmenot' );

            $user_password_wp53 = $panel_logowania.find( 'div.user-pass-wrap' );
            if ( 1 === $user_password_wp53.length ) {
                $user_password = $user_password_wp53;
            }

            if ( 1 === $user_login.length ) {
                $new_user_login = $( '<div class="nazwa_uzytkownika">' +
                    '<div class="nazwa_uzytkownika_ikona">' +
                    '<i class="icon-person"></i>' +
                    '</div>' +
                    '</div>' );
                $user_login_input = $user_login.find( 'input' );
                $user_login_input.attr( 'placeholder', $user_login.find( 'label' ).text() );
                $new_user_login.append( $user_login_input );
                $new_user_login.replaceAll( $user_login );
            }

            if ( 1 === $user_password.length ) {
                $new_user_password = $( '<div class="haslo">' +
                    '<div class="haslo_ikona">' +
                    '<i class="icon-locked"></i>' +
                    '</div>' +
                    '</div>' );
                $user_password_input = $user_password.find( 'input' );
                $user_password_input.attr( 'placeholder', $user_password.find( 'label' ).text() );
                $new_user_password.append( $user_password_input );
                $new_user_password.replaceAll( $user_password );
            }

            if ( 1 === $user_remember.length ) {
                $new_user_remember = $( '<div class="zapamietaj">' +
                    '</div>' );
                $new_user_remember.append( $user_remember.find( 'input' ) );
                $new_user_remember.append( $user_remember.find( 'label' ) );
                $new_user_remember.replaceAll( $user_remember );
            }

            if ( 1 === $pass.length ) {
                $( '#pass1' ).get( 0 ).type = 'text';

                $new_pass = $( '<div class="haslo">' +
                    '<div class="haslo_ikona">' +
                    '<i class="icon-locked" style="cursor: pointer;"></i>' +
                    '<i class="icon-unlocked" style="cursor: pointer;"></i>' +
                    '</div>' +
                    '</div>' );

                $new_pass.find( '.icon-locked' )
                    .css( 'display', 'none' )
                    .on( 'click', function () {
                        $( this ).toggle()
                            .next( '.icon-unlocked' ).toggle();

                        $( '#pass1' ).get( 0 ).type = 'text';
                    } );

                $new_pass.find( '.icon-unlocked' )
                    .on( 'click', function () {
                        $( this ).toggle()
                            .prev( '.icon-locked' ).toggle();

                        $( '#pass1' ).get( 0 ).type = 'password';
                    } );

                $label = $( 'label[for="pass1"]' );
                $pass.attr( 'placeholder', $label.text() );
                $label.remove();
                $new_pass.append( $pass );
                $( '.wp-pwd' ).prepend( $new_pass );
            }

            // performed only on login page
            $panel_logowania.css( 'visibility', 'visible' );
        } );

        $( document ).ready( function () {
            $( '.box_glowna_wariant select' ).change( function () {
                var value = $( this ).val();
                $( this ).next( 'input[type="hidden"]' ).val( value );
            } ).trigger( 'change' );

            var lekcje_paginacja_element = $( '.lekcje_paginacja' ),
                lekcje_paginacja_width = lekcje_paginacja_element.width(),
                links_width = 0;

            if ( lekcje_paginacja_element.find( 'a' ).length > 1 ) {
                lekcje_paginacja_element.find( 'a' ).each( function () {
                    links_width += $( this ).outerWidth( true );
                } );

                if ( links_width > lekcje_paginacja_width ) {
                    lekcje_paginacja_element.addClass( 'width-align' );
                }
            }
        } );

        $( document ).ready( function () {
            var $glowna_boxy = $( '.glowna_boxy' );
            var $glowna_boxy_row_div = $( '.glowna_boxy .row > div' );
            var $glowna_box_zdjecie = $( '.glowna_box_zdjecie' );
            var $glowna_boxy_row_div_nth_child_2 = $( '.glowna_boxy .row > div > div > div:nth-child(2)' );
            var $glowna_boxy_row_div_nth_child_3 = $( '.glowna_boxy .row > div > div > div:nth-child(3)' );

            var switchToGridView = function () {
                $glowna_boxy_row_div.removeClass( 'col-sm-12' );
                $glowna_boxy_row_div.removeClass( 'col-sm-3' );
                $glowna_boxy_row_div.addClass( 'col-sm-4' );


                $glowna_boxy.addClass( 'glowna_boxy--grid' );
                $glowna_boxy.removeClass( 'glowna_boxy--grid-small' );
                $glowna_boxy.removeClass( 'glowna_boxy--list' );

                $( '.glowna_box_zdjecie' ).removeClass( 'col-sm-4' );
                $( '.glowna_boxy .row > div > div > div:nth-child(2)' ).removeClass( 'col-sm-5' );
                $( '.glowna_boxy .row > div > div > div:nth-child(3)' ).removeClass( 'col-sm-3' );

                $( '.opcje_widoku' ).removeClass( 'active' );
                $( '#kwadrat' ).addClass( 'active' );
            }

            var switchToSmallGridView = function () {
                $glowna_boxy_row_div.removeClass( 'col-sm-12' );
                $glowna_boxy_row_div.removeClass( 'col-sm-4' );
                $glowna_boxy_row_div.addClass( 'col-sm-3' );

                $glowna_box_zdjecie.removeClass( 'col-sm-4' );
                $glowna_boxy_row_div_nth_child_2.removeClass( 'col-sm-5' );
                $glowna_boxy_row_div_nth_child_3.removeClass( 'col-sm-3' );

                $glowna_boxy.addClass( 'glowna_boxy--grid' );
                $glowna_boxy.addClass( 'glowna_boxy--grid-small' );
                $glowna_boxy.removeClass( 'glowna_boxy--list' );

                $( '.opcje_widoku' ).removeClass( 'active' );
                $( '#prostokat' ).addClass( 'active' );
            }

            var switchToListView = function () {
                $glowna_boxy_row_div.removeClass( 'col-sm-3' );
                $glowna_boxy_row_div.removeClass( 'col-sm-4' );
                $glowna_boxy_row_div.addClass( 'col-sm-12' );

                $glowna_box_zdjecie.addClass( 'col-sm-4' );
                $glowna_boxy_row_div_nth_child_2.addClass( 'col-sm-5' );
                $glowna_boxy_row_div_nth_child_3.addClass( 'col-sm-3' );

                $glowna_boxy.addClass( 'glowna_boxy--list' );
                $glowna_boxy.removeClass( 'glowna_boxy--grid' );
                $glowna_boxy.removeClass( 'glowna_boxy--grid-small' );

                $( '.opcje_widoku' ).removeClass( 'active' );
                $( '#lista' ).addClass( 'active' );
            }

            var switchToDefaultView = function () {
                let defaultView = $( '.glowna_boxy' ).data( 'default-view' );

                switch ( defaultView ) {
                    case 'grid':
                        switchToGridView();
                        break;
                    case 'grid_small':
                        switchToSmallGridView();
                        break;
                    case 'list':
                        switchToListView();
                        break;
                    default:
                        switchToGridView();
                        break;
                }
            }

            var on_width_change = function () {

                $( '.glowna_boxy .box' ).each( function () {
                    var h_m = $( this ).find( '.glowna_box_zdjecie' ).outerHeight() - 20;
                    $( this ).find( '.glowna_box_cena' ).css( 'top', h_m );
                    $( this ).find( '.glowna_box_cena_dostepny' ).css( 'top', h_m );
                    $( this ).find( '.glowna_box_cena_promo' ).css( 'top', h_m );
                    $( this ).find( '.glowna_box_cena_cena' ).css( 'top', h_m );
                    $( this ).find( '.glowna_box_cena_promocyjna' ).css( 'top', h_m );

                } );

                // Przełącz na widok grida
                var width = $( window ).width();
                if ( width < 1200 ) {

                    switchToGridView();

                    $( '#prostokat' ).hide();
                    $( '#lista' ).hide();

                } else {
                    $( '#prostokat' ).show();
                    $( '#lista' ).show();
                }

            };

            var showBoxes = function () {
                $( '.glowna_boxy' ).css( "opacity", "1" );
            }


            $( window ).resize( on_width_change );

            $( window ).on('load', function () {
                on_width_change();
            } );
            setTimeout(function(){
                on_width_change();
            }, 500);
            switchToDefaultView();
            on_width_change();
            showBoxes();

            $( '.opcje_widoku' ).on('click', function () {
                var width = $( window ).width();
                if ( width > 1150 ) {

                    $( '.opcje_widoku' ).removeClass( 'active' );
                    $( this ).addClass( 'active' );

                    switch ( $( this ).attr( 'id' ) ) {
                        case 'kwadrat':
                            switchToGridView();
                            break;
                        case 'prostokat':
                            switchToSmallGridView();
                            break;
                        case 'lista':
                            switchToListView();
                            break;
                    }
                }
            } );


            $( '.user' ).on('click', function () {
                $( '#user_menu' ).toggle();
            } );

            $( '.edd_options_price_id_multi' ).select2( {
                dropdownAutoWidth: true,
                width: '100%'
            } ).on( 'change.select2', function ( e ) {
                e.preventDefault();
                var $this = $( this ),
                    form = $this.closest( 'form' ),
                    download_id = $this.data( 'download' ),
                    values = $this.val();

                form.find( '.edd_price_option_hidden_checkbox' ).each( function () {
                    this.checked = false
                } );

                if ( $.isArray( values ) ) {
                    $this.prev( '.fake-select' )
                        .find( 'span' ).text( values.length );

                    $.each( values, function ( i, e ) {
                        form.find( '.edd_price_option_hidden_checkbox_' + download_id + '_' + e )[0].checked = true;
                    } );
                } else {
                    $this.prev( '.fake-select' )
                        .find( 'span' ).text( 0 );
                }

                $this.data( 'open', false );
                $this.removeClass( 'select2-dropdown-open' );
            } ).trigger( 'change.select2' );

            $( window ).on( 'click', function () {
                $( '.edd_options_price_id_multi' ).removeClass( 'select2-dropdown-open' );
            } );

            $( '.fake-select' ).on( 'click', function ( e ) {
                e.stopPropagation();
                var $select = $( this ).next( 'select' );
                if ( $select.hasClass( 'select2-dropdown-open' ) ) {
                    $select.removeClass( 'select2-dropdown-open' );
                    $select.select2( 'close' );
                } else {
                    $select.select2( 'open' );
                    $select.addClass( 'select2-dropdown-open' );
                }
            } );

            // lessons nav in bar
            var lekcjaTop = $( '.lekcja_top' );

            if ( !lekcjaTop.length ) {
                $( '#content' ).addClass( 'no-top-bar' );
            }

            cloneNavToBar();
            toggleNavInBarVisibility();
            document.addEventListener( 'scroll', toggleNavInBarVisibility );

            function toggleNavInBarVisibility() {
                if(!lekcjaTop.length) return;

                var lessonNavOutOfScreen = getLessonNavBottomEdgePosition() < getLessonBarBottomEdgePosition();

                if ( lessonNavOutOfScreen ) {
                    lekcjaTop.addClass( 'lekcja_top--nav-in-bar' );
                } else {
                    lekcjaTop.removeClass( 'lekcja_top--nav-in-bar' );
                }
            }

            function getLessonNavBottomEdgePosition(){
                var lekcjaNastTopElement = document.querySelector('.lekcja_nast_pop');

                if (! lekcjaNastTopElement)
                    return 0;

                return lekcjaNastTopElement.getBoundingClientRect().bottom;
            }

            function getLessonBarBottomEdgePosition(){
                return document.querySelector('.lekcja_top').getBoundingClientRect().bottom;
            }

            function cloneNavToBar() {
                var lekcjaNast = $( '.lekcje_paginacja .lekcja_nast_pop.lekcja_nas' ).clone();
                var lekcjaPop = $( '.lekcje_paginacja .lekcja_nast_pop.lekcja_pop' ).clone();
                var lekcjaTopNav = $( '.lekcja_top_nav' );

                if ( lekcjaPop ) {
                    lekcjaPop.addClass( 'lekcja_top_nav__item lekcja_top_nav__item--prev' );
                    lekcjaPop.removeClass( 'lekcja_nast_pop lekcja_pop' );
                    lekcjaTopNav.append( lekcjaPop );
                }

                if ( lekcjaNast ) {
                    lekcjaNast.addClass( 'lekcja_top_nav__item lekcja_top_nav__item--next' );
                    lekcjaNast.removeClass( 'lekcja_nast_pop lekcja_nas' );
                    lekcjaTopNav.append( lekcjaNast );
                }

            }

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

            /**
             * Recalculate VAT on discount applied
             */
            const DiscountVATFix = {
                EVENTS: 'edd_discount_applied edd_discount_removed edd_quantity_updated',

                recalculateVatOnDiscountApplied: function(){
                    let _this = this;

                    $(document.body).on( _this.EVENTS, function(e, payload){

                        const checkout_cart_total_net_price = $('#checkout_cart_total_net_price');
                        const checkout_cart_total_vat = $('#checkout_cart_total_vat');

                        let total_net_price = payload.total_net_price;
                        let total_vat = payload.total_vat;

                        if(!payload.total_net_price && !payload.total_vat){
                            total_net_price = checkout_cart_total_net_price.data("total-net-price");
                            total_vat = checkout_cart_total_vat.data("total-vat");
                        }

                        checkout_cart_total_net_price.html(total_net_price);
                        checkout_cart_total_vat.html(total_vat);

                    } );
                },
            }
            DiscountVATFix.recalculateVatOnDiscountApplied();

            /**
             * Expand modules in the modules list block
             */
            $('.button-expand-module').on('click', function (e) {
                let moduleId = $(this).data('module-id');
                let lessonList = $('ul.module[data-parent-module-id="' + moduleId + '"]');

                lessonList.slideToggle();
                lessonList.prev('.module-title').toggleClass('expanded')
                $(this).toggleClass('expanded');
            })

            /**
             * Handle invoice resending
             */
            $('.resend-order-invoice').on('click', function (e) {
                let _this = $(this);
                const loadingMessage = _this.data('loading-message');
                const defaultText = _this.text()
                const orderId = _this.data('order-id');

                e.preventDefault();

                if(_this.hasClass('loading')) {
                    return;
                }

                _this.addClass('loading');
                _this.text(loadingMessage);

                let data = new FormData();
                data.append('action', 'wpi_handler');
                data.append( wpidea.nonce_name, wpidea.nonce_value );
                data.append('order_id', orderId);

                $.ajax( {
                    type: "POST",
                    data: data,
                    processData: false,
                    contentType: false,
                    dataType: "html",
                    url: wpidea.ajaxurl + '?wpi_route=orders/resend_invoices_for_order',
                    error: function ( jqXHR, textStatus, errorThrown ) {
                        _this.removeClass('loading');
                        _this.text(defaultText);

                        const response = JSON.parse(jqXHR.responseText);

                        console.error(response.error_details)

                        window.showToast(response.message, 'error')
                    }
                })
                .then(response => JSON.parse(response))
                .then(response => {
                    _this.removeClass('loading');
                    _this.text(defaultText);

                    window.showToast(response.message)
                });
            })

            function reload_lowest_price_information(variant_select) {
                let edd_download_purchase_form = $(variant_select).closest('.edd_download_purchase_form');
                edd_download_purchase_form.find('.lowest_price_information').hide();
                edd_download_purchase_form.find('.lowest_price_information.variant_id_' + $(variant_select).val()).show();
            }

            function reload_available_quantities_information(variant_select) {
                let box = $(variant_select).closest('.box, .wp-block-columns, .slider');
                box.find('.available_quantities_information').hide();
                box.find('.available_quantities_information.variant_id_' + $(variant_select).val()).show();
            }

            $('[name="edd_options[price_id]"]').on('change', function () {
                reload_lowest_price_information(this);
                reload_available_quantities_information(this);
            });

            $('[name="edd_options[price_id]"]').each(function () {
                reload_lowest_price_information(this);
                reload_available_quantities_information(this);
            });

            /**
             * Handle purchase form field values remembering
             */
            let purchaseForm = $('#edd_purchase_form');
            const purchaseFormCookieName = 'publigo_purchase_form_values';

            function getPurchaseFormCookieValue() {
                let cookieValue = getCookie(purchaseFormCookieName) ?? JSON.stringify({});

                try {
                    cookieValue = JSON.parse(cookieValue);
                } catch(e) {
                    return {};
                }

                return cookieValue;
            }
            function loadValuesToPurchaseForm() {
                const cookieValue = getPurchaseFormCookieValue();
                for (const [key, value] of Object.entries(cookieValue)) {
                    let input = $(`#edd_purchase_form [name="${key}"]`);

                    if (!input) {
                        continue;
                    }

                    if (input.is(':checkbox')) {
                        input.prop('checked', value);
                    } else {
                        input.val(value);
                    }

                    if (key === 'bpmj_edd_invoice_data_invoice_check' && value) {
                        $('.bpmj_edd_invoice_data_invoice').show();
                    }
                }
            }

            if ($('#edd_payment_mode_select').length) {
                $(document.body).on('edd_gateway_loaded', function () {
                    loadValuesToPurchaseForm();
                });
            } else {
                loadValuesToPurchaseForm();
            }

            purchaseForm.on('change', 'input, textarea, select', function (e) {
                let cookieValue = getPurchaseFormCookieValue();

                let value = e.target.value;
                let key = e.target.name;

                let input = $(`[name="${key}"]`);

                if (input.is(':checkbox')) {
                    value = input.is(':checked');
                }

                if (value) {
                    cookieValue[key] = value;
                } else {
                    delete cookieValue[key];
                }

                cookieValue = JSON.stringify(cookieValue);
                document.cookie = `${purchaseFormCookieName}=${cookieValue}`;
            });

           if(BPMJ_WPI_QUIZ_SETTINGS_I18N.right_click === true){
               $(document).on('contextmenu copy paste',function(e){
                   e.preventDefault();
               });
           }

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
