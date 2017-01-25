<?php
/**
 * API-API Route class
 *
 * @package APIAPI
 * @subpackage Structures
 * @since 1.0.0
 */

namespace awsmug\APIAPI\Structures;

use awsmug\APIAPI\Util;

if ( ! class_exists( 'awsmug\APIAPI\Structures\Route' ) ) {

	/**
	 * Route class for the API-API.
	 *
	 * Represents a specific route in an API structure.
	 *
	 * @since 1.0.0
	 */
	class Route {
		/**
		 * The route's base URI. May contain regular expressions.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var string
		 */
		private $uri;

		/**
		 * The API structure this route belongs to.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var awsmug\APIAPI\Structures\Structure
		 */
		private $structure;

		/**
		 * Array of primary parameters.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var array
		 */
		private $primary_params = array();

		/**
		 * Array of supported methods and their data.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var array
		 */
		private $data = array();

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string                             $uri       The route's base URI.
		 * @param array                              $data      {
		 *     Array of route data.
		 *
		 *     @type array $primary_params Array of primary parameters as `$param_name => $param_data`
		 *                                 pairs. Each $param_data array can have keys 'required',
		 *                                 'description', 'type', 'enum' and 'default'.
		 *     @type array $methods        Array of supported methods as `$method_name => $method_data`
		 *                                 pairs. Each $method_data array can have keys 'description',
		 *                                 'params' (works similar like $primary_params),
		 *                                 'supports_custom_params', 'request_data_type',
		 *                                 'needs_authentication', 'request_class' and 'response_class'.
		 * }
		 * @param awsmug\APIAPI\Structures\Structure $structure The parent API structure.
		 */
		public function __construct( $uri, $data, $structure ) {
			$this->uri = $uri;

			$this->data = $this->parse_data( $data );

			$this->structure = $structure;

			$this->set_primary_params();
		}

		/**
		 * Returns the base URI for this route.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string The base URI.
		 */
		public function get_base_uri() {
			return $this->uri;
		}

		/**
		 * Returns the description for what a specific method does.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $method Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 * @return string Description for what the method does at this route, or empty
		 *                string if method not supported.
		 */
		public function get_method_description( $method ) {
			if ( ! $this->is_method_supported( $method ) ) {
				return '';
			}

			return $this->data['methods'][ $method ]['description'];
		}

		/**
		 * Returns the available parameter information for a specific method.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $method Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 * @return array Array of method parameters, or empty array if method not supported.
		 */
		public function get_method_params( $method ) {
			if ( ! $this->is_method_supported( $method ) ) {
				return array();
			}

			return array_merge( $this->primary_params, $this->data['methods'][ $method ]['params'] );
		}

		/**
		 * Returns the available primary parameter information.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return array Array of primary parameters.
		 */
		public function get_primary_params() {
			return $this->primary_params;
		}

		/**
		 * Checks whether a specific method supports custom parameters.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $method Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 * @return bool Whether custom parameters are supported, or false if method not supported.
		 */
		public function method_supports_custom_params( $method ) {
			if ( ! $this->is_method_supported( $method ) ) {
				return false;
			}

			return $this->data['methods'][ $method ]['supports_custom_params'];
		}

		/**
		 * Checks whether a specific method requires the request data as JSON.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $method Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 * @return bool Whether request data is used as JSON, or false if method not supported.
		 */
		public function method_uses_json_request( $method ) {
			if ( ! $this->is_method_supported( $method ) ) {
				return false;
			}

			return 'json' === $this->data['methods'][ $method ]['request_data_type'];
		}

		/**
		 * Checks whether a specific method needs authentication.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $method Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 * @return bool Whether authentication is needed, or false if method not supported.
		 */
		public function method_needs_authentication( $method ) {
			if ( ! $this->is_method_supported( $method ) ) {
				return false;
			}

			return $this->data['methods'][ $method ]['needs_authentication'];
		}

		/**
		 * Checks whether a specific method is supported.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $method Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 * @return bool True if the method is supported, otherwise false.
		 */
		public function is_method_supported( $method ) {
			return isset( $this->data['methods'][ $method ] );
		}

		/**
		 * Creates a request object based on parameters.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $route_uri           Route URI for the request.
		 * @param string $method              Optional. Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 *                                    Default 'GET'.
		 * @param string $mode                Optional. API mode to use for the request. Available values
		 *                                    depend on the API structure. Default empty string.
		 * @param string $authenticator       Optional. Authenticator name. Default empty string.
		 * @param array  $authentication_data Optional. Authentication data to pass to the authenticator.
		 *                                    Default empty array.
		 * @return awsmug\APIAPI\Request\Request Request object.
		 */
		public function create_request_object( $route_uri, $method = 'GET', $mode = '', $authenticator = '', $authentication_data = array() ) {
			if ( ! $this->is_method_supported( $method ) ) {
				throw new Exception( sprintf( 'The method %1$s is not supported in the route %2$s.', $method, $this->get_base_uri() ) );
			}

			$class_name = $this->data['methods'][ $method ]['request_class'];

			return new $class_name( $this->structure->get_base_uri( $mode ), $route_uri, $method, $this, $authenticator, $authentication_data );
		}

		/**
		 * Creates a response object based on parameters.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param array  $response_data Response array containing keys 'headers', 'body', and 'response'.
		 *                              Not necessarily all of these are included though.
		 * @param string $method        Optional. Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 *                              Default 'GET'.
		 * @return awsmug\APIAPI\Request\Response Response object.
		 */
		public function create_response_object( $response_data, $method = 'GET' ) {
			if ( ! $this->is_method_supported( $method ) ) {
				throw new Exception( sprintf( 'The method %1$s is not supported in the route %2$s.', $method, $this->get_base_uri() ) );
			}

			$class_name = $this->data['methods'][ $method ]['response_class'];

			return new $class_name( $response_data, $method, $this );
		}

		/**
		 * Sets the primary parameters depending on the route's base URI.
		 *
		 * Primary parameters are regular expression parts of the URI.
		 *
		 * @since 1.0.0
		 * @access private
		 */
		private function set_primary_params() {
			preg_match_all( '@\/\(\?P\<([A-Za-z_]+)\>\[(.+)\]\+\)@U', $this->uri, $matches );

			$this->primary_params = array();
			for ( $i = 0; $i < count( $matches[0] ); $i++ ) {
				$type = '\d' === $matches[2][ $i ] ? 'integer' : 'string';

				$description = '';
				$default     = null;
				if ( isset( $this->data['primary_params'][ $matches[1][ $i ] ] ) ) {
					$description = $this->data['primary_params'][ $matches[1][ $i ] ]['description'];
					$default     = $this->data['primary_params'][ $matches[1][ $i ] ]['default'];
				}

				$this->primary_params[ $matches[1][ $i ] ] = array(
					'required'    => true,
					'description' => $description,
					'type'        => $type,
					'enum'        => array(),
					'default'     => $default,
					'primary'     => true,
				);
			}
		}

		/**
		 * Parses route data.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param array $data Route data.
		 * @return array Parsed route data.
		 */
		private function parse_data( $data ) {
			$data = Util::parse_args( $data, array(
				'primary_params' => array(),
				'methods'        => array(),
			) );

			$data['primary_params'] = $this->parse_param_data( $data['primary_params'] );
			$data['methods']        = $this->parse_method_data( $data['methods'] );

			return $data;
		}

		/**
		 * Parses method data.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param array $method_data Method data.
		 * @return array Parsed method data.
		 */
		private function parse_method_data( $method_data ) {
			$method_data = array_intersect_key( $method_data, array_flip( array( 'GET', 'POST', 'PUT', 'PATCH', 'DELETE' ) ) );

			foreach ( $method_data as $method => &$data ) {
				$data = Util::parse_args( $data, array(
					'description'            => '',
					'params'                 => array(),
					'supports_custom_params' => false,
					'request_data_type'      => 'raw',
					'needs_authentication'   => false,
					'request_class'          => 'awsmug\APIAPI\Request\Request',
					'response_class'         => 'awsmug\APIAPI\Request\Response',
				), true );

				$data['params'] = $this->parse_param_data( $data['params'] );
			}

			return $method_data;
		}

		/**
		 * Parses param data.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param array $param_data Param data.
		 * @return array Parsed param data.
		 */
		private function parse_param_data( $param_data ) {
			foreach ( $param_data as $param => &$data ) {
				$data = Util::parse_args( $data, array(
					'required'    => false,
					'description' => '',
					'type'        => 'string',
					'enum'        => array(),
					'default'     => null,
				), true );
			}

			return $param_data;
		}
	}

}
