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
		 * The hooks instance.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var awsmug\APIAPI\Hooks
		 */
		private $hooks;

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
		 * Defaults to register.
		 *
		 * @since 1.0.0
		 * @access private
		 * @static
		 * @var array
		 */
		private static $defaults = array();

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 */
		private function __construct() {
			$this->instances = array();

			$this->transporters   = new Transporters( $this );
			$this->structures     = new Structures( $this );
			$this->authenticators = new Authenticators( $this );

			$this->hooks = new Hooks();

			$this->hooks->trigger( 'apiapi.manager.started', $this );

			$this->hooks->on( 'apiapi.manager.structures.pre_is_registered', array( $this, 'lazyload_structures' ) );
			$this->hooks->on( 'apiapi.manager.authenticators.pre_is_registered', array( $this, 'lazyload_authenticators' ) );
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
			return $this->structures;
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
			return $this->authenticators;
		}

		/**
		 * Returns the hooks instance.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return awsmug\APIAPI\Hooks The hooks instance.
		 */
		public function hooks() {
			return $this->hooks;
		}

		/**
		 * Hook callback to lazyload default structures.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Hook       $hook       Hook object.
		 * @param string                   $name       Transporter name.
		 * @param awsmug\APIAPI\Structures $structures Structures container.
		 */
		public function lazyload_structures( $hook, $name, $structures ) {
			if ( isset( self::$defaults['structures'][ $name ] ) ) {
				$structures->register( $name, self::$defaults['structures'][ $name ] );
				unset( self::$defaults['structures'][ $name ] );
			}

			if ( empty( self::$defaults['structures'] ) ) {
				$hook->remove();
			}
		}

		/**
		 * Hook callback to lazyload default authenticators.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Hook           $hook           Hook object.
		 * @param string                       $name           Transporter name.
		 * @param awsmug\APIAPI\Authenticators $authenticators Authenticators container.
		 */
		public function lazyload_authenticators( $hook, $name, $authenticators ) {
			if ( isset( self::$defaults['authenticators'][ $name ] ) ) {
				$authenticators->register( $name, self::$defaults['authenticators'][ $name ] );
				unset( self::$defaults['authenticators'][ $name ] );
			}

			if ( empty( self::$defaults['authenticators'] ) ) {
				$hook->remove();
			}
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

				self::setup_defaults();
			}

			return self::$instance;
		}

		/**
		 * Registers default transporters, structures and authenticators.
		 *
		 * @since 1.0.0
		 * @access private
		 * @static
		 */
		private static function setup_defaults() {
			self::$defaults = array(
				'transporters'   => array(),
				'structures'     => array(),
				'authenticators' => array(
					'basic'  => 'awsmug\APIAPI_Defaults\Authenticators\Basic_Authenticator',
					'bearer' => 'awsmug\APIAPI_Defaults\Authenticators\Bearer_Authenticator',
					'x'      => 'awsmug\APIAPI_Defaults\Authenticators\X_Authenticator',
					'oauth1' => 'awsmug\APIAPI_Defaults\Authenticators\OAuth1_Authenticator',
				),
			);

			if ( function_exists( 'curl_init' ) ) {
				self::$instance->transporters()->register( 'curl', 'awsmug\APIAPI_Defaults\Transporters\cURL_Transporter' );
			}

			if ( class_exists( 'Requests' ) ) {
				self::$instance->transporters()->register( 'requests', 'awsmug\APIAPI_Defaults\Transporters\Requests_Transporter' );
			}

			if ( function_exists( 'wp_remote_request' ) ) {
				self::$instance->transporters()->register( 'wordpress', 'awsmug\APIAPI_Defaults\Transporters\WordPress_Transporter' );
			}
		}
	}

}
