<?php
/**
 * API-API class for a scoped external request
 *
 * @package APIAPICore
 * @subpackage Request
 * @since 1.0.0
 */

namespace APIAPI\Core\Request;

use APIAPI\Core\Exception;

if ( ! class_exists( 'APIAPI\Core\Request\Route_Request' ) ) {

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
		 * @var APIAPI\Core\Structures\Route
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
		 * URI parameters which are part of the base URI.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $base_params = array();

		/**
		 * URI parameters which are part of the route URI path.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $uri_params = array();

		/**
		 * Query parameters. Only used if explicitly declared when the method is
		 * different from 'GET'.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $query_params = array();

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
		 * @param string                       $base_uri            Base URI for the request.
		 * @param string                       $method              Either 'GET', 'POST', 'PUT', 'PATCH' or
		 *                                                          'DELETE'.
		 * @param APIAPI\Core\Structures\Route $route               Route object for the request.
		 * @param string                       $route_uri           Route URI for the request.
		 * @param string                       $authenticator       Optional. Authenticator name. Default
		 *                                                          empty string.
		 * @param array                        $authentication_data Optional. Authentication data to pass
		 *                                                          to the authenticator. Default empty array.
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
			$base_uri = parent::get_uri();
			if ( '/' !== substr( $base_uri, -1 ) ) {
				$base_uri .= '/';
			}

			$search  = array();
			$replace = array();
			foreach ( $this->base_params as $param => $value ) {
				$search[]  = '{' . $param . '}';
				$replace[] = $value;
			}

			if ( ! empty( $search ) ) {
				$base_uri = str_replace( $search, $replace, $base_uri );
			}

			$route_uri = $this->route_uri;

			foreach ( $this->uri_params as $param => $value ) {
				$route_uri = preg_replace( '@\/\(\?P\<' . $param . '\>\[(.+)\]\+\)@U', '/' . $value, $route_uri );
			}

			if ( ! empty( $this->query_params ) ) {
				$query_args = array();
				foreach ( $this->query_params as $param => $value ) {
					$query_args[] = $param . '=' . urlencode( $value );
				}

				if ( preg_match( '/\?([A-Za-z0-9_\-]+)=/', $route_uri ) ) {
					$route_uri .= '&' . implode( '&', $query_args );
				} else {
					$route_uri .= '?' . implode( '&', $query_args );
				}
			}

			return $base_uri . ltrim( $route_uri, '/' );
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
			$params = $this->route->get_base_uri_params( $this->uri );
			if ( isset( $params[ $param ] ) ) {
				$this->set_base_param( $param, $value, $params[ $param ] );
				return;
			}

			$params = $this->route->get_method_params( $this->method );

			if ( ! isset( $params[ $param ] ) ) {
				$this->set_custom_param( $param, $value );
			} elseif ( isset( $params[ $param ]['primary'] ) ) {
				$this->set_uri_param( $param, $value, $params[ $param ] );
			} elseif ( 'GET' !== $this->method && 'query' === $params[ $param ]['location'] ) {
				$this->set_query_param( $param, $value, $params[ $param ] );
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
			$params = $this->route->get_base_uri_params( $this->uri );
			if ( isset( $params[ $param ] ) ) {
				return $this->get_base_param( $param, $params[ $param ] );
			}

			$params = $this->route->get_method_params( $this->method );

			if ( ! isset( $params[ $param ] ) ) {
				return $this->get_custom_param( $param );
			} elseif ( isset( $params[ $param ]['primary'] ) ) {
				return $this->get_uri_param( $param, $params[ $param ] );
			} elseif ( 'GET' !== $this->method && 'query' === $params[ $param ]['location'] ) {
				return $this->get_query_param( $param, $params[ $param ] );
			} else {
				return $this->get_regular_param( $param, $params[ $param ] );
			}
		}

		/**
		 * Gets all parameters.
		 *
		 * URI and query parameters are not included as they are part of the URI.
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

				$value = $this->get_regular_param( $param, $param_info );
				if ( null === $value ) {
					continue;
				}

				$all_params[ $param ] = $value;
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
			$missing_params = array();

			$params = $this->route->get_base_uri_params( $this->uri );
			foreach ( $params as $param => $param_info ) {
				if ( null !== $this->get_param( $param ) ) {
					continue;
				}

				$missing_params[] = $param;
			}

			$params = $this->route->get_method_params( $this->method );
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
			if ( ! $this->route->method_needs_authentication( $this->method ) ) {
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
		 * @return APIAPI\Core\Structures\Route Route object.
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
			$value = $this->parse_param_value( $value, $param_info );

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
		 * Sets a base URI parameter.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $param      Parameter name.
		 * @param mixed  $value      Parameter value.
		 * @param array  $param_info Parameter info.
		 */
		protected function set_base_param( $param, $value, $param_info ) {
			$value = $this->parse_param_value( $value, $param_info );

			$this->base_params[ $param ] = $value;
		}

		/**
		 * Gets a base URI parameter.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $param      Parameter name.
		 * @param array  $param_info Parameter info.
		 * @return mixed Parameter value, or null if unset.
		 */
		protected function get_base_param( $param, $param_info ) {
			if ( isset( $this->base_params[ $param ] ) ) {
				return $this->base_params[ $param ];
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
			$value = $this->parse_param_value( $value, $param_info );

			$this->uri_params[ $param ] = $value;
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
			if ( isset( $this->uri_params[ $param ] ) ) {
				return $this->uri_params[ $param ];
			}

			return $param_info['default'];
		}

		/**
		 * Sets a query parameter.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $param      Parameter name.
		 * @param mixed  $value      Parameter value.
		 * @param array  $param_info Parameter info.
		 */
		protected function set_query_param( $param, $value, $param_info ) {
			$value = $this->parse_param_value( $value, $param_info );

			$this->query_params[ $param ] = $value;
		}

		/**
		 * Gets a query parameter.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $param      Parameter name.
		 * @param array  $param_info Parameter info.
		 * @return mixed Parameter value, or null if unset.
		 */
		protected function get_query_param( $param, $param_info ) {
			if ( isset( $this->query_params[ $param ] ) ) {
				return $this->query_params[ $param ];
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
		 * @param mixed $value      The input value.
		 * @param array $param_info Parameter info.
		 * @return mixed The parsed value.
		 */
		protected function parse_param_value( $value, $param_info ) {
			switch ( $param_info['type'] ) {
				case 'boolean':
					$value = boolval( $value );
					break;
				case 'float':
				case 'number':
					$value = floatval( $value );
					break;
				case 'integer':
					$value = intval( $value );

					if ( isset( $param_info['minimum'] ) && $value < $param_info['minimum'] ) {
						throw new Exception( sprintf( 'The value %1$s is smaller than the minimum allowed value of %2$s.', $value, $param_info['minimum'] ) );
					}

					if ( isset( $param_info['maximum'] ) && $value > $param_info['maximum'] ) {
						throw new Exception( sprintf( 'The value %1$s is greater than the maximum allowed value of %2$s.', $value, $param_info['maximum'] ) );
					}

					break;
				case 'string':
					$value = strval( $value );

					if ( ! empty( $param_info['enum'] ) && ! in_array( $value, $param_info['enum'], true ) ) {
						throw new Exception( sprintf( 'The value %1$s is not within the allowed values of %2$s.', $value, implode( ', ', $param_info['enum'] ) ) );
					}

					break;
				case 'array':
					$value = (array) $value;

					if ( isset( $param_info['items'] ) && isset( $param_info['items']['type'] ) ) {
						switch ( $param_info['items']['type'] ) {
							case 'boolean':
								$value = array_map( 'boolval', $value );
								break;
							case 'float':
							case 'number':
								$value = array_map( 'floatval', $value );
								break;
							case 'integer':
								$value = array_map( 'intval', $value );
								break;
							case 'string':
								$value = array_map( 'strval', $value );
								break;
						}
					}
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
