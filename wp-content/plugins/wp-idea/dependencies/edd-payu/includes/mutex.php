<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'BPMJ_Mutex' ) ) :

	/**
	 *
	 */
	class BPMJ_Mutex {
		/**
		 * @var resource
		 */
		protected $file_handle = null;

		/**
		 * @var string
		 */
		protected $file_name = null;

		/**
		 * @var string
		 */
		protected $name = null;

		/**
		 * @var bool
		 */
		protected $ignore_user_abort = null;

		/**
		 * @var bool
		 */
		protected $locked = false;

		/**
		 * @var bool
		 */
		protected $can_lock = true;

		/**
		 * BPMJ_Mutex constructor.
		 *
		 * @param string $name
		 * @param string $plugin_dir
		 */
		public function __construct( $name, $plugin_dir ) {
			$this->name = $name;
			$mutex_dir  = $plugin_dir . DIRECTORY_SEPARATOR . 'mutex';
			if ( ! file_exists( $mutex_dir ) ) {
				@mkdir( $mutex_dir );
			}
			$this->file_name = $mutex_dir . DIRECTORY_SEPARATOR . md5( $name ) . '.lock';
			if ( ! @touch( $this->file_name ) ) {
				$this->can_lock = false;
			}
		}

		/**
		 *
		 */
		public function __destruct() {
			$this->unlock();
		}

		/**
		 * @return bool
		 */
		public function lock() {
			if ( ! $this->can_lock || $this->locked ) {
				return false;
			}
			if ( false !== ( $this->file_handle = @fopen( $this->file_name, 'r' ) ) ) {
				$this->ignore_user_abort = ignore_user_abort( true );
				if ( @flock( $this->file_handle, LOCK_EX ) ) {
					$this->locked = true;
				} else {
					@fclose( $this->file_handle );
				}
			}

			return $this->locked;
		}

		/**
		 * @return bool
		 */
		public function unlock() {
			if ( $this->locked ) {
				@flock( $this->file_handle, LOCK_UN );
				@fclose( $this->file_handle );
				$this->locked = false;
				ignore_user_abort( $this->ignore_user_abort );

				return true;
			}

			return false;
		}
	}

endif;