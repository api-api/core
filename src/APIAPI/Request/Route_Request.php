<?php
/**
 * API-API class for a scoped external request
 *
 * @package APIAPI
 * @subpackage Request
 * @since 1.0.0
 */

namespace awsmug\APIAPI\Request;

use awsmug\APIAPI\Exception;

if ( ! class_exists( 'awsmug\APIAPI\Request\Route_Request' ) ) {

	/**
	 * Request class for the API-API.
	 *
	 * Represents a external API request, scoped for an API-API instance.
	 *
	 * @since 1.0.0
	 */
	class Route_Request extends Request {
		/**
		 * The route object for this request.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var awsmug\APIAPI\Structures\Route
		 */
		protected $route;

		/**
		 * Route URI for this request.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $route_uri = '';

		/**
		 * Authenticator name.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $authenticator = '';

		/**
		 * Authentication data.
		 *
		 * Only needed if $authenticator is used.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $authentication_data = array();

		/**
		 * Custom request parameters.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $custom_params = array();

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string                         $base_uri            Base URI for the request.
		 * @param string                         $method              Either 'GET', 'POST', 'PUT', 'PATCH' or
		 *                                                            'DELETE'.
		 * @param awsmug\APIAPI\Structures\Route $route               Route object for the request.
		 * @param string                         $route_uri           Route URI for the request.
		 * @param string                         $authenticator       Optional. Authenticator name. Default
		 *                                                            empty string.
		 * @param array                          $authentication_data Optional. Authentication data to pass
		 *                                                            to the authenticator. Default empty array.
		 */
		public function __construct( $base_uri, $method, $route, $route_uri, $authenticator = '', $authentication_data = array() ) {
			parent::__construct( $base_uri, $method );

			$this->route               = $route;
			$this->route_uri           = $route_uri;
			$this->authenticator       = $authenticator;
			$this->authentication_data = $authentication_data;
		}

		/**
		 * Returns the full URI this request should be sent to.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string The full request URI.
		 */
		public function get_uri() {
			return trailingslashit( parent::get_uri() ) . ltrim( $this->route_uri, '/' );
		}

		/**
		 * Sets a parameter.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $param Parameter name.
		 * @param mixed  $value Parameter value.
		 */
		public function set_param( $param, $value ) {
			$params = $this->route->get_method_params( $this->method );

			if ( ! isset( $params[ $param ] ) ) {
				$this->set_custom_param( $param, $value );
			} elseif ( isset( $params[ $param ]['primary'] ) ) {
				$this->set_uri_param( $param, $value, $params[ $param ] );
			} else {
				$this->set_regular_param( $param, $value, $params[ $param ] );
			}
		}

		/**
		 * Gets a parameter.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $param Parameter name.
		 * @return mixed Parameter value, or null if unset.
		 */
		public function get_param( $param ) {
			$params = $this->route->get_method_params( $this->method );

			if ( ! isset( $params[ $param ] ) ) {
				return $this->get_custom_param( $param );
			} elseif ( isset( $params[ $param ]['primary'] ) ) {
				return $this->get_uri_param( $param, $params[ $param ] );
			} else {
				return $this->get_regular_param( $param, $params[ $param ] );
			}
		}

		/**
		 * Gets all parameters.
		 *
		 * URI parameters are not included as they are part of the URI.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return array Array of parameters as `$param_name => $param_value` pairs.
		 */
		public function get_params() {
			$all_params = array();

			$params = $this->route->get_method_params( $this->method );
			foreach ( $params as $param => $param_info ) {
				if ( isset( $param_info['primary'] ) ) {
					continue;
				}

				$all_params[ $param ] = $this->get_regular_param( $param, $param_info );
			}

			if ( $this->route->method_supports_custom_params( $this->method ) ) {
				$all_params = array_merge( $all_params, $this->custom_params );
			}

			return $all_params;
		}

		/**
		 * Checks whether the request is valid.
		 *
		 * For it to be valid, all required parameters must be filled.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return bool|array True if the request is valid, array of missing parameters otherwise.
		 */
		public function is_valid() {
			$params = $this->route->get_method_params( $this->method );

			$missing_params = array();
			foreach ( $params as $param => $param_info ) {
				if ( ! $param_info['required'] ) {
					continue;
				}

				if ( null !== $this->get_param( $param ) ) {
					continue;
				}

				$missing_params[] = $param;
			}

			if ( ! empty( $missing_params ) ) {
				return $missing_params;
			}

			return true;
		}

		/**
		 * Checks whether the data for this request should be sent as JSON.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return bool True if JSON should be used, otherwise false.
		 */
		public function should_use_json() {
			return $this->route->method_uses_json_request( $this->method );
		}

		/**
		 * Returns the authenticator name.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string Authenticator name, or empty string if authentication is not required.
		 */
		public function get_authenticator() {
			if ( ! $this->route->needs_authentication( $this->method ) ) {
				return '';
			}

			return $this->authenticator;
		}

		/**
		 * Returns the authentication data.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return array Authentication data, or empty array if authentication is not required.
		 */
		public function get_authentication_data() {
			if ( ! $this->get_authenticator() ) {
				return array();
			}

			return $this->authentication_data;
		}

		/**
		 * Returns the route object.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return awsmug\APIAPI\Structures\Route Route object.
		 */
		public function get_route_object() {
			return $this->route;
		}

		/**
		 * Sets a regular parameter.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $param      Parameter name.
		 * @param mixed  $value      Parameter value.
		 * @param array  $param_info Parameter info.
		 */
		protected function set_regular_param( $param, $value, $param_info ) {
			$value = $this->parse_param_value( $value, $param_info['type'], $param_info['enum'] );

			$this->params[ $param ] = $value;

			$this->maybe_set_default_content_type();
		}

		/**
		 * Gets a regular parameter.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $param      Parameter name.
		 * @param array  $param_info Parameter info.
		 * @return mixed Parameter value, or null if unset.
		 */
		protected function get_regular_param( $param, $param_info ) {
			if ( isset( $this->params[ $param ] ) ) {
				return $this->params[ $param ];
			}

			return $param_info['default'];
		}

		/**
		 * Sets a URI parameter.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $param      Parameter name.
		 * @param mixed  $value      Parameter value.
		 * @param array  $param_info Parameter info.
		 */
		protected function set_uri_param( $param, $value, $param_info ) {
			$value = $this->parse_param_value( $value, $param_info['type'], $param_info['enum'] );

			$new_route_uri = $this->route->get_uri();
			foreach ( $this->route->get_primary_params() as $name => $param_info ) {
				if ( $name === $param ) {
					$new_route_uri = preg_replace( '@\/\(\?P\<' . $param . '\>\[(.+)\]\+\)@U', '/' . $value, $new_route_uri );
				} else {
					$new_route_uri = preg_replace( '@\/\(\?P\<' . $name . '\>\[(.+)\]\+\)@U', '/' . $this->get_uri_param( $name, $param_info ), $new_route_uri );
				}
			}

			$this->route_uri = $new_route_uri;
		}

		/**
		 * Gets a URI parameter.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $param      Parameter name.
		 * @param array  $param_info Parameter info.
		 * @return mixed Parameter value, or null if unset.
		 */
		protected function get_uri_param( $param, $param_info ) {
			preg_match( '@^' . $this->route->get_uri() . '$@i', $this->route_uri, $matches );

			if ( isset( $matches[ $param ] ) ) {
				return $this->parse_param_value( $matches[ $param ], $param_info['type'], $param_info['enum'] );
			}

			return $param_info['default'];
		}

		/**
		 * Sets a custom parameter.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $param      Parameter name.
		 * @param mixed  $value      Parameter value.
		 * @param array  $param_info Parameter info.
		 */
		protected function set_custom_param( $param, $value ) {
			if ( ! $this->route->method_supports_custom_params( $this->method ) ) {
				throw new Exception( sprintf( 'Cannot set unsupported parameter %1$s for route %2$s with method %3$s.', $param, $this->route->get_uri(), $this->method ) );
			}

			$this->custom_params[ $param ] = $value;

			$this->maybe_set_default_content_type();
		}

		/**
		 * Gets a custom parameter.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $param      Parameter name.
		 * @param array  $param_info Parameter info.
		 * @return mixed Parameter value, or null if unset.
		 */
		protected function get_custom_param( $param ) {
			if ( ! $this->route->method_supports_custom_params( $this->method ) ) {
				throw new Exception( sprintf( 'Cannot get unsupported parameter %1$s for route %2$s with method %3$s.', $param, $this->route->get_uri(), $this->method ) );
			}

			if ( isset( $this->custom_params[ $param ] ) ) {
				return $this->custom_params[ $param ];
			}

			return null;
		}

		/**
		 * Parses a parameter value.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param mixed  $value The input value.
		 * @param string $type  The parameter type.
		 * @param array  $enum  Optional. Allowed values for the parameter. An empty array
		 *                      will be ignored. Default empty.
		 * @return mixed The parsed value.
		 */
		protected function parse_param_value( $value, $type, $enum = array() ) {
			switch ( $type ) {
				case 'boolean':
					$value = (bool) $value;
					break;
				case 'float':
				case 'number':
					$value = floatval( $value );
					break;
				case 'integer':
					$value = intval( $value );
					break;
				case 'string':
				default:
					$value = strval( $value );
			}

			if ( ! empty( $enum ) && ! in_array( $value, $enum, true ) ) {
				throw new Exception( sprintf( 'The value %1$s is not within the allowed values of %2$s.', $value, implode( ', ', $enum ) ) );
			}

			return $value;
		}

		/**
		 * Sets the default content type if none has been set yet.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function maybe_set_default_content_type() {
			if ( 'GET' !== $this->method && null === $this->get_header( 'content-type' ) ) {
				if ( $this->should_use_json() ) {
					$this->set_header( 'content-type', 'application/json' );
				} else {
					$this->set_header( 'content-type', 'application/x-www-form-urlencoded' );
				}
			}
		}
	}

}
