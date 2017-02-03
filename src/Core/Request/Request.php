<?php
/**
 * API-API class for a general external request
 *
 * @package APIAPICore
 * @subpackage Request
 * @since 1.0.0
 */

namespace APIAPI\Core\Request;

if ( ! class_exists( 'APIAPI\Core\Request\Request' ) ) {

	/**
	 * Request class for the API-API.
	 *
	 * Represents a general external API request.
	 *
	 * @since 1.0.0
	 */
	class Request implements Request_Interface {
		/**
		 * URI for this request.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $uri = '';

		/**
		 * The method for this request. Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $method = 'GET';

		/**
		 * Request headers.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $headers = array();

		/**
		 * Request parameters.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $params = array();

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $uri    URI for the request.
		 * @param string $method Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 */
		public function __construct( $uri, $method ) {
			$this->uri    = $uri;
			$this->method = $method;
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
			return $this->uri;
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
		public function set_header( $header, $value, $add = false ) {
			$header = $this->canonicalize_header_name( $header );

			if ( $add && ! empty( $this->headers[ $header ] ) ) {
				$this->headers[ $header ][] = $value;
			} else {
				$this->headers[ $header ] = (array) $value;
			}
		}

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
		public function get_header( $header, $as_array = false ) {
			$header = $this->canonicalize_header_name( $header );

			if ( ! isset( $this->headers[ $header ] ) ) {
				return null;
			}

			if ( $as_array ) {
				return $this->headers[ $header ];
			}

			return implode( ',', $this->headers[ $header ] );
		}

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
		public function get_headers( $as_array = false ) {
			if ( $as_array ) {
				return $this->headers;
			}

			$all_headers = array();

			foreach ( $this->headers as $header_name => $header_values ) {
				$all_headers[ $header_name ] = implode( ',', $header_values );
			}

			return $all_headers;
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
			$this->params[ $param ] = $value;

			$this->maybe_set_default_content_type();
		}

		/**
		 * Sets multiple parameters.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param array $params Array of `$param => $value` pairs.
		 */
		public function set_params( $params ) {
			foreach ( $params as $param => $value ) {
				$this->set_param( $param, $value );
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
			if ( isset( $this->params[ $param ] ) ) {
				return $this->params[ $param ];
			}

			return null;
		}

		/**
		 * Gets all parameters.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return array Array of parameters as `$param_name => $param_value` pairs.
		 */
		public function get_params() {
			return $this->params;
		}

		/**
		 * Sets the default content type if none has been set yet.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function maybe_set_default_content_type() {
			if ( 'GET' !== $this->method && null === $this->get_header( 'content-type' ) ) {
				$this->set_header( 'content-type', 'application/x-www-form-urlencoded' );
			}
		}

		/**
		 * Canonicalizes the header name.
		 *
		 * This ensures that header names are always case insensitive, plus dashes and
		 * underscores are treated as the same character.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $header Header name.
		 * @return string Canonicalized header name.
		 */
		protected function canonicalize_header_name( $header ) {
			return str_replace( '-', '_', strtolower( $header ) );
		}
	}

}
