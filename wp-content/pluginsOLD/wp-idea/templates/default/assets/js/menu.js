(
	function ( $ ) {
		$.fn.menumaker = function ( options ) {
			var cssmenu = $( this ), settings = $.extend( {
				format: "dropdown",
				sticky: false
			}, options );
			return this.each( function () {
				$( this ).find( ".menu-button" ).on( 'click', function () {
					$( this ).toggleClass( 'menu-opened' );
					var mainmenu = $( this ).next( 'ul' );
					if ( mainmenu.hasClass( 'open' ) ) {
						mainmenu.slideToggle().removeClass( 'open' );
					}
					else {
						mainmenu.slideToggle().addClass( 'open' );
						if ( settings.format === "dropdown" ) {
							mainmenu.find( 'ul' ).show();
						}
					}
				} );
				cssmenu.find( 'li ul' ).parent().addClass( 'has-sub' );
				multiTg = function () {
					cssmenu.find( ".has-sub" ).prepend( '<span class="submenu-button"></span>' );
					cssmenu.find( '.submenu-button' ).on( 'click', function () {
						$( this ).toggleClass( 'submenu-opened' );
						if ( $( this ).siblings( 'ul' ).hasClass( 'open' ) ) {
							$( this ).siblings( 'ul' ).removeClass( 'open' ).slideToggle();
						}
						else {
							$( this ).siblings( 'ul' ).addClass( 'open' ).slideToggle();
						}
					} );
				};
				if ( settings.format === 'multitoggle' ) {
					multiTg();
				} else {
					cssmenu.addClass( 'dropdown' );
				}
				if ( settings.sticky === true ) {
					cssmenu.css( 'position', 'fixed' );
				}
				var previousWidth = 0;
				var touch = function ( event ) {
					event.stopImmediatePropagation();
					event.preventDefault();
					$( this ).siblings( '.submenu-button' ).click();
				};
				var resizeFix = function () {
					var mediasize = 700;
					if ( previousWidth !== $( window ).width() ) {
						if ( $( window ).width() > mediasize ) {
							cssmenu.find( 'ul' ).show();
							$( '.has-sub > a' ).off( 'click touchstart', touch );
						}
						if ( $( window ).width() <= mediasize ) {
							cssmenu.find( 'ul' ).hide().removeClass( 'open' );
							$( '.has-sub > a' ).on( 'click touchstart', touch );
						}
					}
				};
				resizeFix();
				return $( window ).on( 'resize', resizeFix );
			} );
		};
	}
)( jQuery );

(
	function ( $ ) {
		$( document ).ready( function () {
			$( "#cssmenu" ).menumaker( {
				format: "multitoggle"
			} );
		} );
	}
)( jQuery );
