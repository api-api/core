<?php
/**
 * API-API Structure class
 *
 * @package APIAPI
 * @subpackage Structures
 * @since 1.0.0
 */

namespace awsmug\APIAPI\Structures;

use awsmug\APIAPI\Request\API;
use awsmug\APIAPI\Name_Trait;

if ( ! class_exists( 'awsmug\APIAPI\Structures\Structure' ) ) {

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

			$this->config_class = 'awsmug\APIAPI\Config';
			$this->config_key   = $this->name;

			$this->setup();
			$this->process_routes();
		}

		/**
		 * Returns the general API object for a specific API-API scope.
		 *
		 * The API object will be instantiated if it does not exist yet.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\APIAPI $apiapi The API-API instance to get the API object for.
		 * @return awsmug\APIAPI\Request\API The API object.
		 */
		public function get_api_object( $apiapi ) {
			$name = $apiapi->get_name();

			if ( ! isset( $this->api_objects[ $name ] ) ) {
				$config = array();
				if ( ! empty( $this->config_key ) && $apiapi->config()->isset( $this->config_key ) ) {
					$config = $apiapi->config()->get( $this->config_key );
				}

				$config_class = $this->config_class;

				//TODO: Pass more things, like transport, authentication etc.
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
		 * @param awsmug\APIAPI\APIAPI $apiapi    The API-API instance to get the API object for.
		 * @param string               $route_uri URI of the route.
		 * @param string               $method    Optional. Either 'GET', 'POST', 'PUT', 'PATCH'
		 *                                        or 'DELETE'. Default 'GET'.
		 * @return awsmug\APIAPI\Request\Request Request object for the route.
		 */
		public function get_request_object( $apiapi, $route_uri, $method = 'GET' ) {
			return $this->get_api_object( $apiapi )->get_request_object( $route_uri, $method );
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
		 * Returns the route object for a specific route.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $route_uri URI of the route.
		 * @return awsmug\APIAPI\Request\Route The route object.
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
		 * Sets up the API structure.
		 *
		 * This method should populate the routes array, and can also be used to
		 * handle further initialization functionality.
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
				if ( is_a( $data, 'awsmug\APIAPI\Request\Route' ) ) {
					$route_objects[ $uri ] = $data;
				} else {
					$route_objects[ $uri ] = new Route( $uri, $data, $this );
				}
			}

			$this->routes = $route_objects;
		}
	}

}
