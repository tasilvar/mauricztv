jQuery(document).ready(function ($) {
  // Uruchamianie select2 dla tagów
  if( $( '#salesmanago-tags' ).length ){
	  $( '#salesmanago-tags' ).tagsInput({
		   'height' : '65px',
		   'width':'auto',
		   'interactive':true,
		   'defaultText':'Dodaj tag',
		   'removeWithBackspace' : true,
		   'placeholderColor' : '#666666'
		});
	}  
});