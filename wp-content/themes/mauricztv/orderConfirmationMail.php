<?php
require_once("../../../wp-load.php");

$current_user = wp_get_current_user();

/**
 * Jeśli jest zalogowany
 */
if($current_user) { 
	/**
	 * Wyślij powiadomienie
	 */
	//echo $current_user->ID;
	// print_r( $current_user->data);
	edd_custom_mauricz_notification($current_user->ID, (array)$current_user->data);
	echo "Wysłano powiadomienie na ".$current_user->user_email;
} else { 
	echo "User niezalogowany";
}

