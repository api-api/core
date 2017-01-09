<?php
/**
 * API-API Manager class
 *
 * @package APIAPI
 * @subpackage Core
 * @since 1.0.0
 */

namespace awsmug\APIAPI;

if ( ! class_exists( 'awsmug\APIAPI\Manager' ) ) {

	/**
	 * Manager class for the API-API.
	 *
	 * This class manages the different API-API instances.
	 *
	 * @since 1.0.0
	 */
	class Manager {
		/**
		 * @const string Version number of the API-API
		 */
		const VERSION = '1.0.0-alpha.1';

		/**
		 * The API-API instances.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var array
		 */
		private $instances;

		/**
		 * The transporters container.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var awsmug\APIAPI\Transporters
		 */
		private $transporters;

		/**
		 * The structures container.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var awsmug\APIAPI\Structures
		 */
		private $structures;

		/**
		 * The authenticators container.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var awsmug\APIAPI\Authenticators
		 */
		private $authenticators;

		/**
		 * Instance holder.
		 *
		 * @since 1.0.0
		 * @access private
		 * @static
		 * @var awsmug\APIAPI\Manager
		 */
		private static $instance = null;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 */
		private function __construct() {
			$this->instances = array();

			$this->transporters   = new Transporters();
			$this->structures     = new Structures();
			$this->authenticators = new Authenticators();
		}

		/**
		 * Creates a new API-API instance.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string                     $name   Unique slug for the instance.
		 * @param awsmug\APIAPI\Config|array $config Optional. Configuration object or associative array. Default empty array.
		 */
		public function create_instance( $name, $config = array() ) {
			if ( isset( $this->instances[ $name ] ) ) {
				throw new Exception( sprintf( 'Instance name %s already exists!', $name ) );
			}

			$this->instances[ $name ] = new APIAPI( $name, $this, $config );
		}

		/**
		 * Returns a specific API-API instance.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string                          $name  Unique slug of the instance.
		 * @param awsmug\APIAPI\Config|array|bool $force Optional. Whether to create the instance if it does not exist.
		 *                                               Can also be a configuration object or array to fill the set up
		 *                                               the new instance with this configuration. Default false.
		 * @return awsmug\APIAPI\APIAPI|null The API-API instance, or null if it does not exist.
		 */
		public function get_instance( $name, $force = false ) {
			if ( ! isset( $this->instances[ $name ] ) ) {
				if ( ! $force ) {
					return null;
				}

				$config = array();
				if ( is_a( $force, 'awsmug\APIAPI\Config' ) || is_array( $force ) ) {
					$config = $force;
				}

				$this->create_instance( $name, $config );
			}

			return $this->instances[ $name ];
		}

		/**
		 * Returns the transporters container.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return awsmug\APIAPI\Transporters The transporters container.
		 */
		public function transporters() {
			return $this->transporters;
		}

		/**
		 * Returns the structures container.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return awsmug\APIAPI\Transporters The structures container.
		 */
		public function structures() {
			return $this->structures();
		}

		/**
		 * Returns the authenticators container.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return awsmug\APIAPI\Transporters The authenticators container.
		 */
		public function authenticators() {
			return $this->authenticators();
		}

		/**
		 * Returns the canonical API-API instance.
		 *
		 * @since 1.0.0
		 * @access public
		 * @static
		 * @return awsmug\APIAPI\Manager
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}

}
