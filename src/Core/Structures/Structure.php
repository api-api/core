<?php
/**
 * API-API Structure class
 *
 * @package APIAPICore
 * @subpackage Structures
 * @since 1.0.0
 */

namespace APIAPI\Core\Structures;

use APIAPI\Core\Request\API;
use APIAPI\Core\Name_Trait;
use APIAPI\Core\Util;

if ( ! class_exists( 'APIAPI\Core\Structures\Structure' ) ) {

	/**
	 * Structure class for the API-API.
	 *
	 * Represents a specific API structure.
	 *
	 * @since 1.0.0
	 */
	abstract class Structure implements Structure_Interface {
		use Name_Trait;

		/**
		 * Key in the main configuration to extract relevant
		 * configuration for this structure.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $config_key = '';

		/**
		 * Base URI for the API.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $base_uri = '';

		/**
		 * Parameters that are part of the base URI. Some APIs use such.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $base_uri_params = array();

		/**
		 * Advanced URIs for the API, for example a sandbox URI.
		 * Must be an associative array of $mode => $uri pairs.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $advanced_uris = array();

		/**
		 * Parameters that are part of the advanced URIs. Some APIs use such.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $advanced_uri_params = array();

		/**
		 * Route objects as part of this structure.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $routes = array();

		/**
		 * Optional global parameters the API supports.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $global_params = array();

		/**
		 * Name of the authenticator to use for the API.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $authenticator = '';

		/**
		 * Default authentication data to pass to the authenticator.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $authentication_data_defaults = array();

		/**
		 * Default authentication data to pass to the authenticator, for additional
		 * modes. Must be an associative array of $mode => $defaults pairs.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $advanced_authentication_data_defaults = array();

		/**
		 * Whether the class has been setup.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var bool
		 */
		protected $setup_loaded = false;

		/**
		 * Container for API-API-specific instances of this API.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var array
		 */
		private $api_objects = array();

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Slug of the instance.
		 */
		public function __construct( $name ) {
			$this->set_name( $name );

			$this->config_key = $this->name;
		}

		/**
		 * Lazily sets up the structure.
		 *
		 * This method is invoked whenever a relevant class method is called.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function lazyload_setup() {
			if ( $this->setup_loaded ) {
				return;
			}

			$this->setup_loaded = true;

			$this->setup();
			$this->process_routes();
			$this->process_global_params();
			$this->process_uri_params();
		}

		/**
		 * Returns the general API object for a specific API-API scope.
		 *
		 * The API object will be instantiated if it does not exist yet.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param APIAPI\Core\APIAPI $apiapi The API-API instance to get the API object for.
		 * @return APIAPI\Core\Request\API The API object.
		 */
		public function get_api_object( $apiapi ) {
			$this->lazyload_setup();

			$name = $apiapi->get_name();

			if ( ! isset( $this->api_objects[ $name ] ) ) {
				$this->api_objects[ $name ] = new API( $this, $apiapi->config() );
			}

			return $this->api_objects[ $name ];
		}

		/**
		 * Returns a scoped request object for a specific route of this API.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param APIAPI\Core\APIAPI $apiapi    The API-API instance to get the API object for.
		 * @param string               $route_uri URI of the route.
		 * @param string               $method    Optional. Either 'GET', 'POST', 'PUT', 'PATCH'
		 *                                        or 'DELETE'. Default 'GET'.
		 * @return APIAPI\Core\Request\Route_Request Request object for the route.
		 */
		public function get_request_object( $apiapi, $route_uri, $method = 'GET' ) {
			return $this->get_api_object( $apiapi )->get_request_object( $route_uri, $method );
		}

		/**
		 * Returns the route object for a specific route.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $route_uri URI of the route.
		 * @return APIAPI\Core\Request\Route The route object.
		 */
		public function get_route_object( $route_uri ) {
			$this->lazyload_setup();

			if ( isset( $this->routes[ $route_uri ] ) ) {
				return $this->routes[ $route_uri ];
			}

			foreach ( $this->routes as $route_base_uri => $route ) {
				if ( preg_match( '@^' . $route_base_uri . '$@i', $route_uri ) ) {
					return $route;
				}
			}

			throw new Exception( sprintf( 'The API %1$s does not provide a route for %2$s.', $this->name, $route_uri ) );

			return null;
		}

		/**
		 * Checks whether a specific route exists.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $route_uri URI of the route.
		 * @return bool True if the route exists, otherwise false.
		 */
		public function has_route( $route_uri ) {
			return ! is_null( $this->get_route_object( $route_uri ) );
		}

		/**
		 * Returns all available routes.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return array Array of route objects.
		 */
		public function get_route_objects() {
			$this->lazyload_setup();

			return $this->routes;
		}

		/**
		 * Returns the available global parameters information.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return array Global parameters as `$param => $param_info` pairs.
		 */
		public function get_global_params() {
			$this->lazyload_setup();

			return $this->global_params;
		}

		/**
		 * Returns the authenticator name for the API.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string Authenticator name, or empty string if not set.
		 */
		public function get_authenticator() {
			$this->lazyload_setup();

			return $this->authenticator;
		}

		/**
		 * Returns the default data to send to the authenticator.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $mode Optional. Mode for which to get the authentication data
		 *                     defaults. Default empty.
		 * @return array Array of default authentication data.
		 */
		public function get_authentication_data_defaults( $mode = '' ) {
			$this->lazyload_setup();

			if ( ! empty( $mode ) && isset( $this->advanced_authentication_data_defaults[ $mode ] ) ) {
				return $this->advanced_authentication_data_defaults[ $mode ];
			}

			return $this->authentication_data_defaults;
		}

		/**
		 * Checks whether the API should use an authenticator.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return bool True if the API should use an authenticator, otherwise false.
		 */
		public function has_authenticator() {
			$this->lazyload_setup();

			return ! empty( $this->authenticator );
		}

		/**
		 * Returns this API's base URI for a specific mode.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $mode Optional. Mode for which to get the base URI. Default empty.
		 * @return string Base URI.
		 */
		public function get_base_uri( $mode = '' ) {
			$this->lazyload_setup();

			if ( ! empty( $mode ) && isset( $this->advanced_uris[ $mode ] ) ) {
				return $this->advanced_uris[ $mode ];
			}

			return $this->base_uri;
		}

		/**
		 * Returns required parameters that are part of this API's base URI for a specific mode.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $mode Optional. Mode for which to get the base URI parameters. Default empty.
		 * @return array Base URI parameters.
		 */
		public function get_base_uri_params( $mode = '' ) {
			$this->lazyload_setup();

			if ( ! empty( $mode ) && isset( $this->advanced_uri_params[ $mode ] ) ) {
				return $this->advanced_uri_params[ $mode ];
			}

			return $this->base_uri_params;
		}

		/**
		 * Returns the config key.
		 *
		 * This identifies the configuration array where values for this API are stored in.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string The config key.
		 */
		public function get_config_key() {
			return $this->config_key;
		}

		/**
		 * Processes the response.
		 *
		 * This method can contain API-specific logic to verify the response is correct.
		 * It should either return the passed $response object in its original state or
		 * throw an exception.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param APIAPI\Core\Request\Route_Response $response Response object.
		 * @return APIAPI\Core\Request\Route_Response Response object.
		 */
		public function process_response( $response ) {
			$this->lazyload_setup();

			return $response;
		}

		/**
		 * Sets up the API structure.
		 *
		 * This method should populate the routes array, and can also be used to
		 * handle further initialization functionality, like setting the authenticator
		 * class and default authentication data.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected abstract function setup();

		/**
		 * Ensures that all routes are real route objects instead of plain arrays.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function process_routes() {
			$route_objects = array();

			foreach ( $this->routes as $uri => $data ) {
				if ( is_a( $data, 'APIAPI\Core\Request\Route' ) ) {
					$route_objects[ $uri ] = $data;
				} else {
					$route_objects[ $uri ] = new Route( $uri, $data, $this );
				}
			}

			$this->routes = $route_objects;
		}

		/**
		 * Ensures that all global parameters contain the necessary data.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function process_global_params() {
			foreach ( $this->global_params as $param => &$data ) {
				$data = Util::parse_args( $data, array(
					'required'    => false,
					'description' => '',
					'type'        => 'string',
					'default'     => null,
					'location'    => '',
					'enum'        => array(),
					'items'       => array(),
					'internal'    => false,
				), true );
			}
		}

		/**
		 * Ensures that all URI parameters contain the necessary data.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function process_uri_params() {
			$this->base_uri_params = $this->process_uri_params_set( $this->base_uri, $this->base_uri_params );

			foreach ( $this->advanced_uris as $mode => $advanced_uri ) {
				if ( ! isset( $this->advanced_uri_params[ $mode ] ) ) {
					$this->advanced_uri_params[ $mode ] = array();
				}

				$this->advanced_uri_params[ $mode ] = $this->process_uri_params_set( $advanced_uri, $this->advanced_uri_params[ $mode ] );
			}
		}

		/**
		 * Processes a single set of URI and its params.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $uri    URI to extract params from.
		 * @param array  $params Parameter definition, if already provided.
		 * @return array Processed set of parameters.
		 */
		protected function process_uri_params_set( $uri, $params ) {
			if ( ! preg_match_all( '#\{([A-Za-z0-9_]+)\}#', $this->uri, $matches ) ) {
				return array();
			}

			$processed_params = array();

			foreach ( $matches[1] as $uri_param ) {
				$processed_params[ $uri_param ] = array(
					'required' => true,
					'description' => '',
					'type'        => 'string',
					'default'     => null,
					'location'    => 'base',
					'enum'        => array(),
					'internal'    => false,
				);

				foreach ( array( 'description', 'enum', 'internal' ) as $field ) {
					if ( isset( $params[ $uri_param ][ $field ] ) ) {
						$processed_params[ $uri_param ][ $field ] = $params[ $uri_param ][ $field ];
					}
				}
			}

			return $processed_params;
		}
	}

}
