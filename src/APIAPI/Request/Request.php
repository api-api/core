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

if ( ! class_exists( 'awsmug\APIAPI\Request\Request' ) ) {

	/**
	 * Request class for the API-API.
	 *
	 * Represents a external API request, scoped for an API-API instance.
	 *
	 * @since 1.0.0
	 */
	class Request {
		/**
		 * The route object for this request.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var awsmug\APIAPI\Structures\Route
		 */
		private $route;

		/**
		 * The method for this request. Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var string
		 */
		private $method = 'GET';

		/**
		 * Base URI for this request.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var string
		 */
		private $base_uri = '';

		/**
		 * Route URI for this request.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var string
		 */
		private $route_uri = '';

		/**
		 * Request parameters.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var array
		 */
		private $params = array();

		/**
		 * Custom request parameters.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var array
		 */
		private $custom_params = array();

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string                         $base_uri  Base URI for the request.
		 * @param string                         $route_uri Route URI for the request.
		 * @param awsmug\APIAPI\Structures\Route $route     Route object for the request.
		 */
		public function __construct( $base_uri, $route_uri, $route ) {
			$this->base_uri  = $base_uri;
			$this->route_uri = $route_uri;
			$this->route     = $route;
		}

		/**
		 * Sets the method for this request.
		 *
		 * Not all methods are necessarily supported by each route.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $method Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 */
		public function set_method( $method ) {
			if ( ! $this->route->is_method_supported( $method ) ) {
				throw new Exception( sprintf( 'The method %1$s is not supported in the route %2$s.', $method, $this->route->get_base_uri() ) );
			}

			$this->method = $method;
		}

		/**
		 * Returns the method for this request.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string The method.
		 */
		public function get_method() {
			return $this->method;
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
		 * Sets a regular parameter.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param string $param      Parameter name.
		 * @param mixed  $value      Parameter value.
		 * @param array  $param_info Parameter info.
		 */
		private function set_regular_param( $param, $value, $param_info ) {
			$value = $this->parse_param_value( $value, $param_info['type'], $param_info['enum'] );

			$this->params[ $param ] = $value;
		}

		/**
		 * Gets a regular parameter.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param string $param      Parameter name.
		 * @param array  $param_info Parameter info.
		 * @return mixed Parameter value, or null if unset.
		 */
		private function get_regular_param( $param, $param_info ) {
			if ( isset( $this->params[ $param ] ) ) {
				return $this->params[ $param ];
			}

			return $param_info['default'];
		}

		/**
		 * Sets a URI parameter.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param string $param      Parameter name.
		 * @param mixed  $value      Parameter value.
		 * @param array  $param_info Parameter info.
		 */
		private function set_uri_param( $param, $value, $param_info ) {
			$value = $this->parse_param_value( $value, $param_info['type'], $param_info['enum'] );

			//TODO: Adjust $route_uri accordingly.
		}

		/**
		 * Gets a URI parameter.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param string $param      Parameter name.
		 * @param array  $param_info Parameter info.
		 * @return mixed Parameter value, or null if unset.
		 */
		private function get_uri_param( $param, $param_info ) {
			preg_match( '@^' . $this->route->get_base_uri() . '$@i', $this->route_uri, $matches );

			if ( isset( $matches[ $param ] ) ) {
				return $this->parse_param_value( $matches[ $param ], $param_info['type'], $param_info['enum'] );
			}

			return $param_info['default'];
		}

		/**
		 * Sets a custom parameter.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param string $param      Parameter name.
		 * @param mixed  $value      Parameter value.
		 * @param array  $param_info Parameter info.
		 */
		private function set_custom_param( $param, $value ) {
			if ( ! $this->route->method_supports_custom_params( $this->method ) ) {
				throw new Exception( sprintf( 'Cannot set unsupported parameter %1$s for route %2$s with method %3$s.', $param, $this->route->get_base_uri(), $this->method ) );
			}

			$this->custom_params[ $param ] = $value;
		}

		/**
		 * Gets a custom parameter.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param string $param      Parameter name.
		 * @param array  $param_info Parameter info.
		 * @return mixed Parameter value, or null if unset.
		 */
		private function get_custom_param( $param ) {
			if ( ! $this->route->method_supports_custom_params( $this->method ) ) {
				throw new Exception( sprintf( 'Cannot get unsupported parameter %1$s for route %2$s with method %3$s.', $param, $this->route->get_base_uri(), $this->method ) );
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
		 * @access private
		 *
		 * @param mixed  $value The input value.
		 * @param string $type  The parameter type.
		 * @param array  $enum  Optional. Allowed values for the parameter. An empty array
		 *                      will be ignored. Default empty.
		 * @return mixed The parsed value.
		 */
		private function parse_param_value( $value, $type, $enum = array() ) {
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
	}

}
