<?php

use bpmj\wpidea\wolverine\user\User;

function bpmj_eddcm_parse_register_email_tags( $default_email_body, $first_name, $username, $password ) {
	$reset_link = User::findByLogin($username)->getPasswordResetLink();

	$default_email_body	 = str_replace( '{password_reset_link}', $reset_link, $default_email_body );

	return $default_email_body;
}

add_filter( 'edd_auto_register_email_body', 'bpmj_eddcm_parse_register_email_tags', 20, 4 ); //priority greater than 10 to make it run after edd-auto-register filter

?>
