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
		 * Name of the config class to use with this structure.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $config_class = '';

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
		 * Advanced URIs for the API, for example a sandbox URI.
		 * Must be an associative array of $mode => $uri pairs.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $advanced_uris = array();

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

			$this->config_class = 'APIAPI\Core\Config';
			$this->config_key   = $this->name;

			$this->setup();
			$this->process_routes();
			$this->process_global_params();
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
			$name = $apiapi->get_name();

			if ( ! isset( $this->api_objects[ $name ] ) ) {
				$config = array();
				if ( ! empty( $this->config_key ) && $apiapi->config()->isset( $this->config_key ) ) {
					$config = $apiapi->config()->get( $this->config_key );
				}

				$config_class = $this->config_class;

				$this->api_objects[ $name ] = new API( $this, new $config_class( $config ) );
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
			if ( ! empty( $mode ) && isset( $this->advanced_uris[ $mode ] ) ) {
				return $this->advanced_uris[ $mode ];
			}

			return $this->base_uri;
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
				), true );
			}
		}
	}

}
