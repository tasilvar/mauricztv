<?php
/**
 * Metabox Functions
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;
	
	
class BPMJ_EDD_SM_Metabox{
	
	
	/*
	 * Konstruktor klasy
	 */
	public function __construct(){
		$this->init();
	} 
	
	
	/*
	 * Inicjalizacja
	 */
	protected function init(){
		
		// Rejestracja Metaboxa
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ), 10, 2 );
		
		// Zapis Metaboxa
		add_action( 'save_post', array( $this, 'save_metabox' ) );
	}
	
	
	/*
	 * Dodaje metabox do karty produktu
	 */
	public function add_metabox() {
		add_meta_box('download', __('Tagi produktu SALESmanago', BPMJ_EDD_SM_DOMAIN), array( $this, 'metabox_tags' ), 'download', 'normal', 'default');
	}
	
	
	/*
	 * Ładuje zawartość metaboxa
	 */
	public function metabox_tags() {
		global $post;
	?>
		<input type="text" name="_bpmj_edd_sm_tags" id="salesmanago-tags" value="<?php echo get_post_meta( $post->ID, '_bpmj_edd_sm_tags', true ); ?>">
		<label for="salesmanago-tags"><i><?php _e( 'Wpisz tagi (oddzielając je przecinkiem), które mają być dodane do kontaktu w panelu SALESmanago po zakupie tego produktu.', BPMJ_EDD_SM_DOMAIN ); ?></i></label>
	<?php	
	}	
	
	
	/*
	 * Zapisywanie danych z metaboxa
	 */
	public function save_metabox($post_id) {
		
		if ( !current_user_can( 'edit_post', $post->ID ))
			return $post_id;
		
		// Podstawienie danych z _POST do zmiennych
		$product_meta['_bpmj_edd_sm_tags'] = isset( $_POST['_bpmj_edd_sm_tags'] ) ? $_POST['_bpmj_edd_sm_tags'] : '';

		
		/* Pętla zapisująca poszczególne produkty
		 * z tablicy ustawionej wyżej
		 */
		foreach ($product_meta as $key => $value) { 
			
			if( $post->post_type == 'revision' ) return; 
			
			if(get_post_meta($post_id, $key, FALSE)) {
				update_post_meta($post_id, $key, $value);
			
			} else { 
				add_post_meta($post_id, $key, $value);
			}
			
			if(!$value) delete_post_meta($post_id, $key); 
		}
	
	}

	
	
}
new BPMJ_EDD_SM_Metabox();