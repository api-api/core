<?php
/**
 * API-API Request interface
 *
 * @package APIAPI
 * @subpackage Request
 * @since 1.0.0
 */

namespace awsmug\APIAPI\Request;

if ( ! interface_exists( 'awsmug\APIAPI\Request\Request_Interface' ) ) {

	/**
	 * Request interface for the API-API.
	 *
	 * Represents an API request.
	 *
	 * @since 1.0.0
	 */
	interface Request_Interface {
		/**
		 * Returns the full URI this request should be sent to.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string The full request URI.
		 */
		public function get_uri();

		/**
		 * Returns the method for this request.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string The method.
		 */
		public function get_method();

		/**
		 * Sets a header.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $header Header name.
		 * @param string $value  Header value.
		 * @param bool   $add    Optional. Whether to add the value instead of replacing it.
		 *                       Default false.
		 */
		public function set_header( $header, $value, $add = false );

		/**
		 * Gets a header.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $header   Header name.
		 * @param bool   $as_array Optional. Whether to return the value as array. Default false.
		 * @return string|array|null Header value as string or array depending on $as_array, or
		 *                           null if not set.
		 */
		public function get_header( $header, $as_array = false );

		/**
		 * Gets all headers.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param bool $as_array Optional. Whether to return the individual values as array.
		 *                       Default false.
		 * @return array Array of headers as `$header_name => $header_values` pairs.
		 */
		public function get_headers( $as_array = false );

		/**
		 * Sets a parameter.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $param Parameter name.
		 * @param mixed  $value Parameter value.
		 */
		public function set_param( $param, $value );

		/**
		 * Sets multiple parameters.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param array $params Array of `$param => $value` pairs.
		 */
		public function set_params( $params );

		/**
		 * Gets a parameter.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $param Parameter name.
		 * @return mixed Parameter value, or null if unset.
		 */
		public function get_param( $param );

		/**
		 * Gets all parameters.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return array Array of parameters as `$param_name => $param_value` pairs.
		 */
		public function get_params();
	}

}