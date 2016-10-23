<?php
/**
 * API-API Main class
 *
 * @package APIAPI
 * @subpackage Core
 * @since 1.0.0
 */

namespace awsmug\APIAPI;

if ( ! class_exists( 'awsmug\APIAPI\APIAPI' ) ) {

	/**
	 * Main class for the API-API.
	 *
	 * @since 1.0.0
	 */
	class APIAPI {
		/**
		 * Configuration object.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var awsmug\APIAPI\Config
		 */
		private $config = null;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Config|array $config Optional. Configuration object or associative array. Default empty.
		 */
		public function __construct( $config = null ) {
			if ( is_a( $config, 'awsmug\APIAPI\Config' ) ) {
				$this->config = $config;
			} else {
				$this->config = new Config( $config );
			}
		}

		/**
		 * Returns the configuration object.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return awsmug\APIAPI\Config Configuration object for the manager.
		 */
		public function config() {
			return $this->config;
		}
	}

}
