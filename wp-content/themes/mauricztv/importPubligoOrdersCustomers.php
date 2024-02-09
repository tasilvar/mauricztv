<?php
require_once("../../../wp-load.php");
 

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';


//$mail = new PHPMailer(true);
/*
try {
    $headers = array('Content-Type: text/html; charset=UTF-8');

    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'mail.mauricz.tv';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'powiadomienia@mauricz.tv';                     //SMTP username
    $mail->Password   = 'pass';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('powiadomienia@mauricz.tv', 'Mauricz.tv');

    //$entry->response['email_1']

    $login = 'loginUser';
    $generatePassword = '123';
    
    $mail->addAddress('michal.jendraszczyk@gmail.com', $login);      

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
   
    $mail->Subject = 'Migracja konta w mauricz.tv';
    $mail->Body    = 'Twoje konto zostało przeniesione do nowej wersji platrofmy mauricz.tv<br/><br/>Dane do logowania:<br/><br/>Login: '.$entry->response['names_1'].'<br/>Hasło:'.$generatePassword;
    $mail->AltBody = 'Twoje konto zostało zarejestrowane<br/><br/>Dane do logowania:<br/><br/>Login: '.$login.'<br/>Hasło:'.$generatePassword;

    $mail->send();
    echo 'Message has been sent'; 
     
} catch (Exception $e) {
    echo "Message could not be sent.";
}
*/

// addCourseToUser(389, 16); //389
// exit();
$file = fopen("import_mauricz_prod.csv","r");

$kolumnyCsv = explode(";", str_replace('"', '' ,fgetcsv($file)[0]));
$export = [];
$i = 0;

$productsNames = [];
$productsIds = [];
$data = [];
echo"stop";
exit();
while(! feof($file)) { 
                try {   
                    $wartosciCsv = @explode(";", fgetcsv($file, 0, "\n")[0]);
                    
                    foreach($wartosciCsv as $keyWartosc => $wartosc) {
                        
                        $export[$i][$kolumnyCsv[$keyWartosc]] = trim(str_replace('"', '' ,$wartosc));
                    }
                } catch(\Exception $e) { 
                    continue;
                }
                $i++;
}
//print_r($kolumnyCsv);
//print_r($export);


foreach($export as $ex) { 
    try {
        $ids_product=[];
        if(!get_user_by('email', $ex['Billing: E-mail Address'])) {
            $generatePassword = wp_generate_password( 8, true, false );
            /**
             * Stworz uzytkownika
             */
            $user = wp_insert_user( array(
                'user_login' => $ex['Billing: E-mail Address'],
                'user_pass' => $generatePassword,
                'user_email' => $ex['Billing: E-mail Address'],
                'first_name' => $ex['Billing: First Name'],
                'last_name' => $ex['Billing: Last Name'],
                'display_name' => $ex['Billing: First Name'].' '.$ex['Billing: Last Name'],
                'role' => 'subscriber'
               ));
            /**
             * Wyślij maila z powiadomieniem o utworzeniu konta i koniecznosci resetu hasła
             */

            }
          

            /**
             * Pobierz id kursu z publigo na podstawie pobranej nazwy z starego systemu
             */
            
            $dt=0;
             for($dx=1;$dx<=10;$dx++) {
                if(!empty(trim($ex['Order Item #'.$dx.': Product Name']))) { 
                    $productsNames[] = trim($ex['Order Item #'.$dx.': Product Name']);


                    $args = array(
                        'post_type'      => 'download',
                        'posts_per_page' => 1,
                        'title' => trim($ex['Order Item #'.$dx.': Product Name'])
                    );

                    $getProduct = get_posts( $args );
                    if(count($getProduct) > 0) {
                        $productsIds[] = $getProduct[0]->ID;
                        $data['downloads'][$dt]['id'] = $getProduct[0]->ID;
                    } 
                    
                }
            }
            
             
         
            /**
             * Stworz zamowienie 
             */
            
                global $edd_options;
                
                $data['first'] = $ex['Billing: First Name'];
                $data['last'] = $ex['Billing: Last Name'];
                //Adres
                $data['edd-payment-address'][0]['line1'] = $ex['Billing: Street Address 1'];
                $data['edd-payment-address'][0]['line2'] = $ex['Billing: Street Address 2'];
                $data['edd-payment-address'][0]['city'] = $ex['Billing: City'];
                $data['edd-payment-address'][0]['zip'] = $ex['Billing: ZIP Code'];
                $data['edd-payment-address'][0]['state'] = $ex['Billing: State'];
                $data['edd-payment-address'][0]['country'] = $ex['Billing: Country (prefix)'];

            
                if( empty( $data['downloads'][0]['id'] ) ) {
                    continue;
                }
    
                $user = strip_tags( trim( $ex['Billing: E-mail Address'] ) );
    
                if( empty( $user ) ) {
                    continue;
                }
    
                if( is_numeric( $user ) )
                    $user = get_userdata( $user );
                elseif ( is_email( $user ) )
                    $user = get_user_by( 'email', $user );
                elseif ( is_string( $user ) )
                    $user = get_user_by( 'login', $user );
                else
                    return; // no user assigned
    
                $user_id 	= $user ? $user->ID : 0;


                 // Adres dla uzytkownika 
                 $address = array(
                    'line1'    => $ex['Billing: Street Address 1'],
                    'line2'    => $ex['Billing: Street Address 2'],
                    'city'     => $ex['Billing: City'],
                    'state'    => $ex['Billing: State'],
                    'zip'      => $ex['Billing: ZIP Code'],
                    'country'  => $ex['Billing: Country (prefix)']
                );

                //$userdata
                $meta    = update_user_meta( $user_id, '_edd_user_address', $address );
                #$updated = wp_update_user( $userdata );

                // Koniec adresu 


                $email 		= $user ? $user->user_email : strip_tags( trim( $ex['Billing: E-mail Address'] )  );
                if( isset( $data['first'] ) ) {
                    $user_first = sanitize_text_field( $data['first'] );
                } else {
                    $user_first	= $user ? $user->first_name : '';
                }
    
                if( isset( $data['last'] ) ) {
                    $user_last = sanitize_text_field( $data['last'] );
                } else {
                    $user_last	= $user ? $user->last_name : '';
                }
    
                $user_info = array(
                    'id' 			=> $user_id,
                    'email' 		=> $email,
                    'first_name'	=> $user_first,
                    'last_name'		=> $user_last,
                    'discount'		=> 'none'
                );
    
                $price = false;
                //$price = ! empty( $data['amount'] ) ? edd_sanitize_amount( strip_tags( trim( $data['amount'] ) ) ) : false;
    
                $cart_details = array();
   
                $total = 0;
                foreach( $data['downloads'] as $key => $download ) {
    
                    // calculate total purchase cost
    
                    if( isset( $download['options'] ) ) {
    
                        $prices     = get_post_meta( $download['id'], 'edd_variable_prices', true );
                        $price_key  = $download['options']['price_id'];
                        $item_price = $prices[$price_key]['amount'];
    
                    } else {
                        $item_price = edd_get_download_price( $download['id'] );
                    }
    
                    $cart_details[$key] = array(
                        'name'        => get_the_title( $download['id'] ),
                        'id'          => $download['id'],
                        'item_number' => $download,
                        'price'       => $price ? $price : $item_price,
                        'subtotal'    => $price ? $price : $item_price,
                        'quantity'    => 1,
                        'tax'         => 0,
                    );
                    $total += $item_price;
    
                    $ids_product[] = $download['id'];
                }
    
                // assign total to the price given, if any
                if( $price ) {
                    $total = $price;
                }
     
                
    
                $date = ! empty( $data['date'] ) ? date( 'Y-m-d H:i:s', strtotime( strip_tags( trim( $data['date'] ) ) ) ) : false;
                if( ! $date ) {
                    $date = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
                }
    
                if( strtotime( $date, time() ) > time() ) {
                    $date = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
                }
    
                // status zamówienia
                $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'publish'; // wczesniej pending

                $tax    = ! empty( $_POST['tax'] ) ? edd_sanitize_amount( sanitize_text_field( $_POST['tax'] ) ) : 0;
    
                $purchase_data     = array(
                    'price'        => edd_sanitize_amount( $total ),
                    'tax'          => $tax,
                    'post_date'    => $date,
                    'purchase_key' => strtolower( md5( uniqid() ) ), // random key
                    'user_email'   => $email,
                    'user_info'    => $user_info,
                    'currency'     => edd_get_currency(),
                    'downloads'    => $data['downloads'],
                    'cart_details' => $cart_details,
                    'status'       => 'publish', // wczesniej pending
                );
    
                //
                // Dodawanie adresu do zamówienia
                //
                $payment_id = edd_insert_payment( $purchase_data );
    
                edd_update_payment_meta( $payment_id, '_edd_payment_tax', $tax );

                if( empty( $data['receipt'] ) || $data['receipt'] != '1' ) {
                    remove_action( 'edd_complete_purchase', 'edd_trigger_purchase_receipt', 999 );
                }
    
                if( ! empty( $data['expiration'] ) && class_exists( 'EDD_Recurring_Customer' ) && $user_id > 0 ) {
    
                    $expiration = strtotime( $data['expiration'] . ' 23:59:59' );
    
                    EDD_Recurring_Customer::set_as_subscriber( $user_id );
                    EDD_Recurring_Customer::set_customer_payment_id( $user_id, $payment_id );
                    EDD_Recurring_Customer::set_customer_status( $user_id, 'active' );
                    EDD_Recurring_Customer::set_customer_expiration( $user_id, $expiration );
                }
    
                if( ! empty( $data['shipped'] ) ) {
                    update_post_meta( $payment_id, '_edd_payment_shipping_status', '2' );
                    //publish
                }
    
                // increase stats and log earnings
                edd_update_payment_status( $payment_id, $status ) ;
                


                // Przypisywanie produktu do usera 
                // print_r($ids_product);
                // exit();
                foreach($ids_product as $id) {
                    addCourseToUser($id, $user_id);
                }
                // Koniec przypisywania produktu do usera 

                /**
                 * Zaktualizuj adres
                 */
/*
                 
                $getCustomer = EDD()->customers->get_customer_by( 'user_id', $user_id );
                $customer = new EDD_Customer( $getCustomer->ID );
                 
                if ( empty( $customer->id ) ) {
                    return false;
                }
                
                // Setup the customer address, if present
                $address = array();
                if ( intval( $user_id ) > 0 ) {
            
                    $current_address = get_user_meta( $user_id, '_edd_user_address', true );
            
                  
                        $current_address    = wp_parse_args( $current_address, array( 'line1', 'line2', 'city', 'zip', 'state', 'country' ) );

                        $address['line1']   =   $ex['Billing: Street Address 1'];

                        $address['line2']   =  $ex['Billing: Street Address 2'];

                        $address['city']    =  $ex['Billing: City'];

                        $address['country'] =   $ex['Billing: Country (prefix)'];

                        $address['zip']     =  $ex['Billing: ZIP Code'];

                        $address['state']   =  $ex['Billing: State'];
                }

                // Sanitize the inputs
                $customer_data            = array(); 
                // $customer_data['name']    = strip_tags( stripslashes( $customer_info['name'] ) );
	$customer_data['email']   = $email;
	$customer_data['user_id'] = $user_id;
              
                $address       = apply_filters( 'edd_edit_customer_address', $address, $customer_id );
            // echo "AA";
            //     print_r($address);
            //     exit();
            
                $customer_data = array_map( 'sanitize_text_field', $customer_data );
                $address       = array_map( 'sanitize_text_field', $address );
            
                do_action( 'edd_pre_edit_customer', $customer_id, $customer_data, $address );
             
            
                if ( $customer->update( $customer_data ) ) {
            
                    if ( ! empty( $customer->user_id ) && $customer->user_id > 0 ) {
                        update_user_meta( $customer->user_id, '_edd_user_address', $address );
                    }
                }
                */

                 // Zaktualizuj dane adresowe klienta 

                 $payment    = new EDD_Payment( $payment_id );

                 
                 $address = array_map( 'trim', $data['edd-payment-address'][0] );
                 
                 // Set new meta values
                 $payment->user_id        = $user_id;
                 $payment->email          = $email;
                 $payment->first_name     = $user_first;
                 $payment->last_name      = $user_last;
                 $payment->address        = $address;
 
                 $payment->total          = $total;
                 $payment->tax            = $tax;
 
                 $payment->has_unlimited_downloads = 1;
                 $payment->save();
                 
                 do_action( 'edd_updated_edited_purchase', $payment_id );

                 
    } catch(\Exception $e) {
        continue;
    }
    // echo "Stop";
    // exit();
}
echo "Stop";
exit();
print_r($productsNames);
echo "<br/>----<br/>";
print_r($productsIds);
fclose($file);
exit();
//print_r($entries['data']);
echo "ilossc: ".count($entries);

foreach($entries['data'] as $entry) { 
try {
    if(!get_user_by('email', $entry->response['email_1'])) {
    $generatePassword = wp_generate_password( 8, true, false );
    /**
     * Stworz uzytkownika
     */
    $user = wp_insert_user( array(
        'user_login' => $entry->response['email_1'],
        'user_pass' => $generatePassword,
        'user_email' => $entry->response['email_1'],
        'first_name' => $entry->response['names_1']['first_name'],
        'last_name' => $entry->response['names_1']['last_name'],
        'display_name' => $entry->response['names_1']['first_name'].' '.$entry->response['names_1']['last_name'],
        'role' => 'customer'
       ));

/**
 * Stworz obiekt WC_customer
 */
 
 $customer = new WC_Customer( $user );
  
 $customer->set_first_name( $entry->response['names_1']['first_name'] );
 $customer->set_last_name( $entry->response['names_1']['last_name'] );

 $customer->set_billing_email( $entry->response['email_1'] );
 $customer->set_billing_first_name( $entry->response['names_1']['first_name'] );
 $customer->set_billing_last_name( $entry->response['names_1']['last_name']  );
 $customer->set_billing_address_1( $entry->response['address_1']['address_line_1']);
 $customer->set_billing_address_2( '' );
 $customer->set_billing_city( $entry->response['address_1']['city'] );
 $customer->set_billing_postcode( $entry->response['address_1']['zip'] );
 $customer->set_billing_country( $entry->response['address_1']['country'] );
 $customer->set_billing_phone( $entry->response['numeric-field'] );
 
 //$customer->set_shipping_email( $entry->response['email_1'] );
 $customer->set_shipping_first_name( $entry->response['names_1']['first_name'] );
 $customer->set_shipping_last_name( $entry->response['names_1']['last_name']  );
 $customer->set_shipping_address_1( $entry->response['address_1']['address_line_1']);
 $customer->set_shipping_address_2( '' );
 $customer->set_shipping_city( $entry->response['address_1']['city'] );
 $customer->set_shipping_postcode( $entry->response['address_1']['zip'] );
 $customer->set_shipping_country( $entry->response['address_1']['country'] );
 $customer->set_shipping_phone( $entry->response['numeric-field'] );

 $customer->save();




 try {
    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail("michal.jendraszczyk@gmail.com","Rejestracja konta w myluxe.pl","Twoje konto zostało zarejestrowane<br/><br/>Dane do logowania:<br/><br/>Login: ". $entry->response['email_1']." <br/>Hasło: ".$generatePassword, $headers);
     /*
     /*
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'mail.myluxe.pl';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'info@myluxe.pl';                     //SMTP username
    $mail->Password   = 'GoldLuxe46!';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('info@myluxe.pl', 'Wordpress');

    //$entry->response['email_1']
    $mail->addAddress('michal.jendraszczyk@gmail.com', $entry->response['names_1']['first_name'].' '.$entry->response['names_1']['last_name']);      

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Rejestracja konta w myluxe.pl';
    $mail->Body    = 'Twoje konto zostało zarejestrowane<br/><br/>Dane do logowania:<br/><br/>Login: '.$entry->response['names_1'].'<br/>Hasło:'.$generatePassword;
    $mail->AltBody = 'Twoje konto zostało zarejestrowane<br/><br/>Dane do logowania:<br/><br/>Login: '.$entry->response['names_1'].'<br/>Hasło:'.$generatePassword;

    $mail->send();
    echo 'Message has been sent'; 
    */
} catch (Exception $e) {
    echo "Message could not be sent.";
}
    }
} catch(\Exception $e) { 
    continue;
}
}
  
function addCourseToUser($product_id,$user_id) { 
    
                $user_id = (int)$user_id;
                $product_id = (int)$product_id;
                $days = (int)365;
                $hours = (int)0;
                $minutes = (int)0;
                $seconds = (int)0;
                $sign = '';
    
                $total_time = $days * 86400 + $hours * 3600 + $minutes * 60 + $seconds;
    
                $price_id = (int)0;

                //Zdrowe Jelita
                //Dietetyka i suplementacja poziom l,ll,lll
                //Dietetyczne Abecadło
                //AS Dietetyki
                //Fit & Beauty
                //Król Dietetyki
                //Diagnostyka
                //Pogromca stresu
                //Przyszła Mama
                //Zdrowa kobieta
                //Mistrz Odchudzania
                //	Konferencja "Dietetyka Przyszłości"
    
                if ($user_id && $product_id) {
                    bpmj_eddpc_add_time($user_id, $product_id, $price_id, $total_time);
    
                    $course_id = WPI()->courses->get_course_by_product($product_id);
                    // print_r($course_id);
                    // echo "KURS:".$course_id;
                    // exit();
                    if($course_id){

                        $access_time_data = get_user_meta($user_id, "_bpmj_eddpc_access", true);


                        $timestamp = time()+$total_time;
                        $due_date = gmdate("Y-m-d", $timestamp);


                        $access_time_str = $due_date . ' ' .'00:00';
                        $access_time = bpmj_eddpc_adjust_timestamp(strtotime($access_time_str), false);
                        
                        $access_time_data[$product_id]['access_time'] = $access_time;
                
                        update_user_meta($user_id, '_bpmj_eddpc_access', $access_time_data);
                        // $course = new Courses($course_id);
                        // $course->events->emit(Event_Name::STUDENT_ENROLLED_IN_COURSE, $course_id->ID, $user_id);
                    }
                }
/*
                if ($user_id && $product_id) {
                    //$data['total_time'] = $sign === '-' ? -1 * $total_time : $total_time;
                     
                    $access_time_data = get_user_meta($user_id, "_bpmj_eddpc_access", true);
                    #echo "#".$product_id.'U'.$user_id;

                    echo $product_id.' '.$user_id;
                    print_r($access_time_data);
                    echo "===";
                    exit();
                    //access_time
                    //$access_time_data[$product_id] = $access_time;
                    #echo "TIME:".$total_time;
                    $access_time_data[$product_id]['total_time'] = $total_time;
                    
                    $timestamp = time()+$total_time;
                    $due_date = gmdate("Y-m-d", $timestamp);


                    $access_time_str = $due_date . ' ' .'00:00';
                    $access_time = bpmj_eddpc_adjust_timestamp(strtotime($access_time_str), false);
                    
                    $access_time_data[$product_id]['access_time'] = $access_time;
              
                    update_user_meta($user_id, '_bpmj_eddpc_access', $access_time_data);

                    // print_r($access_time_data);
                    // exit();
                    
                }
                */
}