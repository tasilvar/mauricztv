<?php 
/*
 * Plugin Name:       Raport sprzedazy Mauricz.tv
 * Description:       Generuje nam raport sprzedazy do pliku csv
 * Version:           1.0.0
 * Author:            VirtualPeople Michał Jendraszczyk
 */

ini_set('display_errors',0);
error_reporting (E_ERROR | E_PARSE);

class RaportSprzedazy {
	private $raport_sprzedazy_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'raport_sprzedazy_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'raport_sprzedazy_page_init' ) );
	}

	public function raport_sprzedazy_add_plugin_page() {
		add_menu_page(
			'Raport sprzedazy Mauricz', // page_title
			'Raport sprzedazy Mauricz', // menu_title
			'manage_options', // capability
			'raport-sprzedazy', // menu_slug
			array( $this, 'raport_sprzedazy_create_admin_page' ), // function
			'dashicons-book', // icon_url
			80 // position
		);
	}

	public function raport_sprzedazy_create_admin_page() {
		$this->raport_sprzedazy_options = get_option( 'raport_sprzedazy_option_name' ); ?>

		<div class="wrap">
			<h2>Raport sprzedazy</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'raport_sprzedazy_option_group' );
					do_settings_sections( 'raport-sprzedazy-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function raport_sprzedazy_page_init() {
		register_setting(
			'raport_sprzedazy_option_group', // option_group
			'raport_sprzedazy_option_name', // option_name
			array( $this, 'raport_sprzedazy_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'raport_sprzedazy_setting_section', // id
			'Settings', // title
			array( $this, 'raport_sprzedazy_section_info' ), // callback
			'raport-sprzedazy-admin' // page
		);

		add_settings_field(
			'raport_typ', // id
			'Rodzaj raportu', // title
			array( $this, 'raport_typ_callback' ), // callback
			'raport-sprzedazy-admin', // page
			'raport_sprzedazy_setting_section' // section
		);

		//Jakub Mauricz

		add_settings_field(
			'raport_szkoleniowiec', // id
			'Szkoleniowiec', // title
			array( $this, 'raport_szkoleniowiec_callback' ), // callback
			'raport-sprzedazy-admin', // page
			'raport_sprzedazy_setting_section' // section
		);

		add_settings_field(
			'raport_kurs', // id
			'Kurs', // title
			array( $this, 'raport_kurs_callback' ), // callback
			'raport-sprzedazy-admin', // page
			'raport_sprzedazy_setting_section' // section
		);
		add_settings_field(
			'raport_rabat', // id
			'Rabat', // title
			array( $this, 'raport_rabat_callback' ), // callback
			'raport-sprzedazy-admin', // page
			'raport_sprzedazy_setting_section' // section
		);

		add_settings_field(
			'raport_data_od', // id
			'Data od', // title
			array( $this, 'raport_data_od_callback' ), // callback
			'raport-sprzedazy-admin', // page
			'raport_sprzedazy_setting_section' // section
		);

		add_settings_field(
			'raport_data_do', // id
			'Data do', // title
			array( $this, 'raport_data_do_callback' ), // callback
			'raport-sprzedazy-admin', // page
			'raport_sprzedazy_setting_section' // section
		);
	}

	public function raport_typ_callback() {

		echo '
		<select class="form-control" name="raport_typ">
		<option value="ogolny">Sprzedaz ogólna</option>
		<option value="produkt">Sprzedaz wg produktu</option>
		<option value="szkoleniowiec">Sprzedaz wg szkoleniowca</option>
		<option value="rabat">Sprzedaz wg rabatu</option>
		</select>
		';
	}

	public function raport_szkoleniowiec_callback() {

		$szkoleniowcy = '';
		foreach((@unserialize(@get_post(324)->post_content)['choices']) as $key => $choice) {
			$szkoleniowcy .= '<option  value="'.$choice.'">'.$choice. '</option>';
		}

		echo '
		<select class="form-control" name="szkoleniowiec">
		'.$szkoleniowcy.'
		</select>
		';
	}

	/**
	 * Zwrócenie rabatów do wyboru dla ekposrtu raportu
	 */
	public function raport_rabat_callback() {
		$rabaty = '';

		$pobierzRabaty =  edd_get_discounts();

		// print_r($pobierzRabaty);
		// exit();
		foreach($pobierzRabaty as $rabat) {
			if(!empty(edd_get_discount_code( $rabat->ID ))) {
				$rabaty .= '<option value="'.edd_get_discount_code( $rabat->ID ) .'">ID rabatu:'.$rabat->ID.' - '.edd_get_discount_code( $rabat->ID ).'</option>';
			}
		}
		echo '
		<select class="form-control" name="rabat_id[]" multiple style="height:200px;width:100%;">
		'.$rabaty.'
		</select>
		';
	}
	public function raport_kurs_callback() {

		$kursy = '';

		$argsAll = array(
			'post_type'      => 'download',//download courses
			'post_status' => 'publish',
			'posts_per_page' => -1,
			// 'meta_key' => 'sales_disabled',
			// 'meta_value' => 'off',
		);

		$rows = (get_posts($argsAll));
		foreach($rows as $row) {
			$kursy .= '<option value="'.$row->ID.'">
			'.$row->post_title.'
			</option>';
		}
		echo '
		<select class="form-control" name="product_id[]" multiple style="height:200px;">
		'.$kursy.'
		</select>
		';
	}
	public function raport_data_od_callback() {
		printf(
			'<input class="regular-text" type="date" name="raport_sprzedazy_option_name[raport_data_od]" id="raport_data_od" value="%s">',
			isset( $this->raport_sprzedazy_options['raport_data_od'] ) ? esc_attr( $this->raport_sprzedazy_options['raport_data_od']) : ''
		);
	}
	public function raport_data_do_callback() {
		printf(
			'<input class="regular-text" type="date" name="raport_sprzedazy_option_name[raport_data_do]" id="raport_data_do" value="%s">',
			isset( $this->raport_sprzedazy_options['raport_data_do'] ) ? esc_attr( $this->raport_sprzedazy_options['raport_data_do']) : ''
		);
	}

	public function raport_sprzedazy_sanitize($input) {
        
		$sanitary_values = array();

		$this->generateCSV();

		return $sanitary_values;
	}

	public function raport_sprzedazy_section_info() {

	}
	/**
	 * Generowanie raportu csv
	 */
	public function generateCSV() { 
		
        // $getPosts = get_posts([
        //     'post_status' => 'publish',
        //     'numberposts' => $limit
        // ]
        // );

		//Filtruj zamówienia po uzytkowniku
		#'user' => $current_user->ID,
		// echo "TEST SZKOLENIOWIEC:".get_field('prowadzacy','815');
		//status
		$args = [
			'number' => '99999',
			'status' => 'publish',
			'date_query' => array(
				array(
					'after'     => $_POST['raport_sprzedazy_option_name']['raport_data_od'],
					'before'    => $_POST['raport_sprzedazy_option_name']['raport_data_do'],
				),
			),
			
		];
		$getPayments = edd_get_payments($args);

 
		// print_r($getPayments);
        // exit();
		// echo "CSV".$_POST['raport_sprzedazy_option_name']['raport_data_do']. ' '.$_POST['raport_sprzedazy_option_name']['raport_data_od'];
		// print_r($_POST);
		
		// print_r(edd_get_payment('41543'));

		$output = null;
		$kodyRabatow = [];

		if($_POST['raport_typ'] == 'ogolny') {
			header('Content-Type: text/csv');
			header('Content-Type: application/force-download; charset=UTF-8');
			header('Cache-Control: no-store, no-cache');
			header('Content-Disposition: attachment; filename="raport_sprzedazy_'.$_POST['raport_typ'].'.csv"');
			$output .= "id_zamowienia;data;wartosc_z_rabatem;wartosc_bez_rabatu;kod_rabatowy;\n";
		}

		if($_POST['raport_typ'] == 'szkoleniowiec') {
			header('Content-Type: text/csv');
			header('Content-Type: application/force-download; charset=UTF-8');
			header('Cache-Control: no-store, no-cache');
			header('Content-Disposition: attachment; filename="raport_sprzedazy_'.$_POST['raport_typ'].'.csv"');
			$output .= "id_zamowienia;data;wartosc;rabat;id_produkt;product_name;szkoleniowiec;\n";
		}

		if($_POST['raport_typ'] == 'produkt') {
			header('Content-Type: text/csv');
			header('Content-Type: application/force-download; charset=UTF-8');
			header('Cache-Control: no-store, no-cache');
			header('Content-Disposition: attachment; filename="raport_sprzedazy_'.$_POST['raport_typ'].'.csv"');
			$output .= "id_zamowienia;data;wartosc;rabat;id_produkt;product_name;szkoleniowiec;\n";
		}

		if($_POST['raport_typ'] == 'rabat') {
			header('Content-Type: text/csv');
			header('Content-Type: application/force-download; charset=UTF-8');
			header('Cache-Control: no-store, no-cache');
			header('Content-Disposition: attachment; filename="raport_sprzedazy_'.$_POST['raport_typ'].'.csv"');
			$output .= "kod_rabatowy;ilosc_zamowien;wartosc_bez_rabatu;wartosc_z_rabatem;\n";
		}

        $file = fopen('php://output', 'w');
        
        foreach ($getPayments as $key => $payment) {

			$getDetailPayment = edd_get_payment($payment->ID);
			// print_r($payment);
			// echo "<br/>----<br/>";
			// print_r($getDetailPayment);
			// exit();
			// Jesli jest to raport ogolny zwroc informacje o zamowieniach tylko w odniesienu do ram czasowych
			if($_POST['raport_typ'] == 'ogolny') {
					$output .= '"'.$getDetailPayment->ID.'";';
					$output .= '"'.$getDetailPayment->date.'";';
					// Wartosc z rabatem
					$output .= '"'.$getDetailPayment->total.'";';
					// Wartosc bez rabatu
					$output .= '"'.$getDetailPayment->subtotal.'";';
					//Rabat
					$output .= '"'.$getDetailPayment->discounts.'";';
					$output .= "\n";
			}
			 
			// print_r($getDetailPayment);
		 
			// Jesli jest to raport o sprzedazy wg szkoleniowca sprawdz w szczegolach produktow jaki szkoleniowiec jest powiazany z zakupionym produktem 

			if($_POST['raport_typ'] == 'szkoleniowiec') {

				// print_r(count($getDetailPayment->cart_details));
				// print_r(($getDetailPayment->cart_details));

				$cartDetails = $getDetailPayment->cart_details;
				foreach($cartDetails as $item) { 
					// Jesli szkoleniowiec z pozycji zamowienia jest zgodny z wybranym szkoleniowcem
					if(get_field('prowadzacy', $item['id']) == $_POST['szkoleniowiec']) {
						// print_r($item);
						// exit();
						$output .= '"'.$getDetailPayment->ID.'";';
						$output .= '"'.$getDetailPayment->date.'";'; 
						$output .= '"'.$item['item_price'].'";';
						$output .= '"'.$item['discount'].'";';
						$output .= '"'.$item['id'].'";';
						$output .= '"'.$item['name'].'";';
						$output .= '"'.get_field('prowadzacy', $item['id']).'";';
						$output .= "\n";
					}
				}
			}
			// Jesli jest to raport sprzeazy wg produktu, sprawdz zawsze jaki jes prdukt w zamowieniu
			if($_POST['raport_typ'] == 'produkt') {

				$cartDetails = $getDetailPayment->cart_details;
				foreach($cartDetails as $item) { 
					
					// Jesli produkt z pozycji zamowienia jest zgodny z wybranym produktem
					// print_r($_POST['product_id']);
					// exit();
					if(in_array($item['id'], $_POST['product_id'])) {
						$output .= '"'.$getDetailPayment->ID.'";';
						$output .= '"'.$getDetailPayment->date.'";'; 
						$output .= '"'.$item['item_price'].'";';
						$output .= '"'.$item['discount'].'";';
						$output .= '"'.$item['id'].'";';
						$output .= '"'.$item['name'].'";';
						$output .= '"'.get_field('prowadzacy', $item['id']).'";';
						$output .= "\n";
					}
				}
			} 

			/**
			 * Zwróć listing rabatów i przychodu z kazdego z nich
			 * rabat | liczba zamowien | przychod
			 */

			if($_POST['raport_typ'] == 'rabat') {

				//Pobierz szczegóły zamówienia
				$getDetailPayment = edd_get_payment($payment->ID);

				// Przejdź pętla po zamówieniach, odłóz rabat wg kodu, przychód wg kodu i liczbę iteracji (zamówień) wg kodu
				if(($getDetailPayment->discounts != 'none') && !empty($getDetailPayment->discounts)) {
					$kodyRabatow[$getDetailPayment->discounts]['id_zamowienia'][] = $payment->ID;
					$kodyRabatow[$getDetailPayment->discounts]['wartosc_bez_rabatu'][] = $getDetailPayment->subtotal;
					$kodyRabatow[$getDetailPayment->discounts]['wartosc_z_rabatem'][] = $getDetailPayment->total;
				}
			}
            
        }

		/**
		 * Zwróć zagregowane dane z zamówień
		 */
		if($_POST['raport_typ'] == 'rabat') {
			print_r($kodyRabatow);
			exit();
			foreach($kodyRabatow as $keyKod => $kod) { 
				/**
				 * Jeśli wybrany przez nas kod przy eksporcie jest zgodny z tym z zebranym z zamówienia 
				 */
				// echo "KOD".$keyKod;
				//  exit();
				if(in_array($keyKod, $_POST['rabat_id'])) {
					$output .= '"'.@$keyKod.'";';
					$output .= '"'.@count($kod['id_zamowienia']).'";'; 
					$output .= '"'.(float)@array_sum($kod['wartosc_bez_rabatu']).'";';
					$output .= '"'.(float)@array_sum($kod['wartosc_z_rabatem']).'";';
					$output .= "\n";
				} else { 
					// if(count($_POST['rabat_id']) == 0) {
						if(!empty($keyKod)) {
							try {
								$output .= '"'.@$keyKod.'";';
								$output .= '"'.@count($kod['id_zamowienia']).'";'; 
								$output .= '"'.(float)@array_sum($kod['wartosc_bez_rabatu']).'";';
								$output .= '"'.(float)@array_sum($kod['wartosc_z_rabatem']).'";';
								$output .= "\n";
						} catch(\Exception $e) {
							continue;
						}
					//}
					}
				}
			}
			// print_r($kodyRabatow);
				// $payment->discount
				// echo "RABAT";
				// exit();
		}
        echo trim($output);
        exit();

      
    }
}


if ( is_admin() )
	$raport_sprzedazy = new RaportSprzedazy();
