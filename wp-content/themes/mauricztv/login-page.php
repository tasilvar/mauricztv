<?php
/**
 * Template Name: Szablon logowania
 */

 global $user_ID;

 global $wpdb;

 if($user_ID) { 
  // Zalogowany
  $getLinkAccount = get_permalink(57);
  header("Location:".$getLinkAccount);
  //echo "TEST".$getLinkAccount;
 }
 
get_header();
 
 if(!$user_ID) { 
    // Niezalogowany
    ?>



    <div class="row login-section">

    <div class="col-md-6">
    <h1>Zaloguj się</h1>
    <?php 
    wp_login_form( ['echo' =>true] );
    ?>
	
	<?php /*
    <p class="inner-login-left">Nie pamiętasz hasła? <a href="<?= bloginfo('url') ?>/wp-login.php?action=lostpassword">Kliknij tutaj</a></p>
	*/?>
	
	<p class="inner-login-left">Nie pamiętasz hasła? <a href="<?= bloginfo('url') ?>/lostpassword/">Kliknij tutaj</a></p>
	
    </div>

    <div class="col-md-6">
    <div>
    <h1>Zarejestruj się</h1>
    <h3>Otrzymasz liczne dodatkowe korzyści</h3>
    <ul>

    <li>
    dostęp do Twoich szkoleń
    </li>
    <li>
    podgląd historii zakupów
    </li>
    <li>
    brak konieczności wprowadzania swoich danych w kolejnych zakupach
    </li>
    <li>
    mozliwość otrzymania rabatów i kuponów promocyjnych
    </li>

    </ul>

	<?php /*
    <a href='<?= bloginfo('url') ?>/wp-login.php?action=register' class='more btn btn-secondary register'>Zarejestruj się</a>
	*/?>
	
	
	<a href='<?= bloginfo('url') ?>/register/' class='more btn btn-secondary register'>Zarejestruj się</a>

    </div>

    </div>
    </div>
    



    <?php
 } else { 
  // Zalogowany
 }

 get_footer();