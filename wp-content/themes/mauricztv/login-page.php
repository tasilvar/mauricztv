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
    
    <form method="post">


    </form>
    <?php 
    wp_login_form( ['echo' =>true] );
    ?>
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


    <a href='wp-login.php?action=register' class='more btn btn-secondary register'>Zarejestruj się</a>

    </div>

    </div>
    </div>
    



    <?php
 } else { 
  // Zalogowany
 }

 get_footer();