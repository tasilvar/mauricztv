<?php

namespace bpmj\wp\eddip\api;

class iPressoApiTests {
	/**
	 * @var iPressoApi
	 */
	private $ipresso_api;

	/**
	 * iPressoApiTests constructor.
	 *
	 * @param iPressoApi $ipresso_api
	 */
	public function __construct( iPressoApi $ipresso_api ) {

		$this->ipresso_api = $ipresso_api;
		$this->ipresso_api->set_debug_handler( array( $this, 'output_debug_info' ) );
	}

	/**
	 * @param $test_method
	 */
	public function test( $test_method ) {
		$test_method = 0 === strpos( $test_method, 'test_' ) ? $test_method : 'test_' . $test_method;
		header( 'Content-type: text/plain; charset=UTF-8' );
		if ( method_exists( $this, $test_method ) ) {
			$this->output_debug_info( '#', 'Starting ' . $test_method );
			$this->$test_method();
		} else {
			$this->output_debug_info( '#', 'Method ' . __CLASS__ . '::' . $test_method . ' doesn\'t exist' );
		}
		die;
	}

	/**
	 *
	 */
	public function test_adding_contact() {
		$contact_id = $this->add_test_contact();
		if ( ! $contact_id ) {
			return;
		}
		$this->cleanup_test_contact( $contact_id );
	}

	/**
	 * This method should create only one contact
	 */
	public function test_duplicated_contact() {
		$contact_id = $this->add_test_contact();
		if ( ! $contact_id ) {
			return;
		}
		$contact = $this->ipresso_api->get_contact( $contact_id );

		// this should not add new contact but update old one instead (including tags)
		$this->ipresso_api->add_contact( array(
			'fname' => 'Duplicated first name',
			'lname' => 'Duplicated last name',
			'email' => $contact[ 'email' ],
			'tag'   => array( 'NEW TAG' ),
		) );

		// check if the contact is updated (eg. it should have "Duplicated first name" in fname field)
		$this->ipresso_api->get_contact( $contact_id );
		$this->cleanup_test_contact( $contact_id );
	}

	/**
	 *
	 */
	public function test_updating_contact() {
		$contact_id = $this->add_test_contact();
		if ( ! $contact_id ) {
			return;
		}
		$this->ipresso_api->update_contact( $contact_id, array(
			'lname' => 'Updated test name',
		) );

		// check if the contact is updated (lname should read "Updated test name")
		$this->ipresso_api->get_contact( $contact_id );
		$this->cleanup_test_contact( $contact_id );
	}

	/**
	 *
	 */
	public function test_adding_tags_to_contact() {
		$contact_id = $this->add_test_contact();
		if ( ! $contact_id ) {
			return;
		}
		$this->ipresso_api->add_contact_tags( $contact_id, array( 'NEW TAG 1', 'NEW TAG 2' ) );

		// check if the contact has more tags than before (should be 'SOME TAG', 'OTHER TAG', 'lowercase tag', 'NEW TAG 1', 'NEW TAG 2')
		$this->ipresso_api->get_contact( $contact_id );
		$this->cleanup_test_contact( $contact_id );
	}

	public function test_removing_tags_from_contact() {
		$contact_id = $this->add_test_contact();
		if ( ! $contact_id ) {
			return;
		}
		$contact = $this->ipresso_api->get_contact( $contact_id );
		$this->ipresso_api->remove_contact_tags( $contact[ 'email' ], array( 'SOME TAG' ) );

		// check if the contact has less tags than before (should be 'OTHER TAG', 'lowercase tag')
		$this->ipresso_api->get_contact( $contact_id );
		$this->cleanup_test_contact( $contact_id );
	}

	/**
	 * @return int
	 */
	protected function add_test_contact() {
		$email = 'test@' . md5( microtime() ) . '.com';
		$this->output_debug_info( '#', 'ADD_TEST_CONTACT', $email );
		$contact_data = array(
			'fname' => 'Test first name',
			'lname' => 'Test last name',
			'email' => $email,
			'tag'   => array( 'SOME TAG', 'OTHER TAG', 'lowercase tag' ),
		);
		$response     = $this->ipresso_api->add_contact( $contact_data );
		if ( $response->is_success() ) {
			$added_contact = $response->get_data( 'contact' );
			if ( ! empty( $added_contact ) && ! empty( $added_contact[ 1 ] ) ) {
				return $added_contact[ 1 ][ 'id' ];
			}
		}

		return null;
	}

	/**
	 * @param $contact_id
	 */
	protected function cleanup_test_contact( $contact_id ) {
		$this->output_debug_info( '#', 'CLEANUP_TEST_CONTACT' );
		$this->ipresso_api->remove_contact( $contact_id );
	}

	/**
	 * @param string|int $index
	 * @param string $method
	 * @param mixed $info
	 */
	public function output_debug_info( $index, $method, $info = null ) {
		echo "#$index $method:\n\n";
		if ( $info ) {
			if ( is_array( $info ) ) {
				print_r( $info );
			} else {
				echo $info;
			}

			echo "\n\n";
		}
	}
}