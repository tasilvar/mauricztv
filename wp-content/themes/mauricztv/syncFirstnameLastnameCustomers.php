<?php
require_once("../../../wp-load.php");

global $wpdb;

$emptyCustomers = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "edd_customers WHERE name = ''" );
// print_r(get_users());
print_r($emptyCustomers);
foreach($emptyCustomers as $user) {
    $getUser = get_user_by('id', $user->user_id);
    #echo $user->email."<br/>";
    syncFirstnameLastnameCustomers($getUser, $user->user_id);
    #print_r($getUser);
    #echo "<br/>";
}
echo "OK";
exit();
function syncFirstnameLastnameCustomers($current_user, $current_user_id) {
    global $wpdb;
        // Pobierz klientow ktorzy nie maja uzupelnionego imienia/nazwiska

	$current_user = $current_user;
	$getUser = get_userdata( $current_user_id);
 
	// 	$offset = 0, $number = 20, $mode = 'live', $orderby = 'ID', $order = 'DESC',
	//  * $user = null, $status = 'any', $meta_key = null
	$args = [
		//Filtruj zamówienia po uzytkowniku
		'user' => $current_user->ID
	];
	$getPayments = edd_get_payments($args);
	
	// Jesli klient cokolwiek kupił
	if(count($getPayments) > 0) { 

		// Pobierz dane adresu rozliczeniowego i skopiuj je do danych uzytkownika
		# print_r($getPayments);
		$getLastPaymentID = $getPayments[0]->ID;
		#echo "last payment ID: ".$getLastPaymentID;
		
		#print_r(edd_get_payment('40600'));
		#$getLastPayment = edd_get_payment($getLastPaymentID)->user_info;
		$getLastPaymentName = edd_get_payment($getLastPaymentID)->payment_meta['bpmj_edd_invoice_person_name'];
		 
        
		//Pobierz imie i nazwisko 
		$sliceName = explode(" ",trim($getLastPaymentName));

		$getFirstname = $sliceName[0];
		if(count($sliceName) > 1) {
			$getLastname = $sliceName[1];
		} else{ 
			$getLastname = $sliceName[0];	
		}
		# print_r($getLastPayment);

		$getOldFirstName = get_user_meta( $current_user->ID, 'first_name');
		$getOldLastName = get_user_meta( $current_user->ID, 'last_name');

		// Jesli uzytkownik nie posiada na koncie ustawionego imienia lub nazwiska do zamowienia zaktualizuj je do obiektu klienta
        
		if(is_array($getOldFirstName) || is_array($getOldFirstName) || empty($getOldFirstName) || empty($getOldFirstName)) {
		
            #echo "POBRANE IMIE Z ZAMOWIENIA:".$getFirstname." ".$getLastname;

			$getEDD_Customer = new EDD_Customer($current_user->ID);
			$getEDD_Customer->name = $getFirstname.' '.$getLastname;
					
			$customer_data = array( 'name' => $getFirstname. ' '.$getLastname );
		
			$getEDD_Customer->update( $customer_data );
		 
			update_user_meta( $current_user->ID, 'first_name', $getFirstname);
			update_user_meta( $current_user->ID, 'last_name', $getLastname);

            // $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->prefix}cmplz_cookiebanners SET message_optin = %s", $banner_text) );

           

            $table_name = 'edd_customers';
	        $userID = $current_user->ID;
	        $data_update = array('name' => $getFirstname.' '.$getLastname);
	        $data_where = array('user_id' => $userID);
		
            $wpdb->query("UPDATE ". $wpdb->prefix . "edd_customers SET name = '".$getFirstname.' '.$getLastname."' WHERE user_id = '".$userID."'" );

  	      //  $wpdb->update($table_name, $data_update, $data_where);


            //"UPDATE {$wpdb->prefix}cmplz_cookiebanners SET message_optin = %s", $banner_text
		}
    }
}