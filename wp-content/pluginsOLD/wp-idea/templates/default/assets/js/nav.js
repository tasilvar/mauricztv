( function( $ ) {
$( document ).ready(function() {
// Cache the elements we'll need
var menu = $('nav');
var menuList = menu.find('ul:first');
var listItems = menu.find('li').not('.toggle-menu-button');

// Create responsive trigger
menuList.prepend('<li class="toggle-menu-button"><a href="#"><span>Menu</span></a></li>');

// Toggle menu visibility
menu.on('click', '.toggle-menu-button', function(){
	listItems.slideToggle('fast');
	listItems.addClass('collapsed');
});
});
} )( jQuery );
