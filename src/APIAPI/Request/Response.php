<?php
/**
 * API-API class for a scoped response
 *
 * @package APIAPI
 * @subpackage Request
 * @since 1.0.0
 */

namespace awsmug\APIAPI\Request;

use awsmug\APIAPI\Exception;

if ( ! class_exists( 'awsmug\APIAPI\Request\Response' ) ) {

	/**
	 * Response class for the API-API.
	 *
	 * Represents an API response, scoped for an API-API instance.
	 *
	 * @since 1.0.0
	 */
	class Response {
		/**
		 * The route object for this response.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var awsmug\APIAPI\Structures\Route
		 */
		private $route;

		/**
		 * The method that was used to get the response.
		 * Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var string
		 */
		private $request_method = 'GET';

		/**
		 * Response headers.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var array
		 */
		private $headers = array();

		/**
		 * Response parameters.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var array
		 */
		private $params = array();

		/**
		 * Response as array with 'code' and 'message' keys.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var array
		 */
		private $response = array();

		/**
		 * Raw body content of the response.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var string
		 */
		private $raw_body = '';

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param array                          $response       Response array containing keys
		 *                                                       'headers', 'body', 'response' and
		 *                                                       'cookies'. Not necessarily all of
		 *                                                       these are included though.
		 * @param string                         $request_method Either 'GET', 'POST', 'PUT', 'PATCH'
		 *                                                       or 'DELETE'.
		 * @param awsmug\APIAPI\Structures\Route $route          Route object for the response.
		 */
		public function __construct( $response, $request_method, $route ) {
			$this->route          = $route;
			$this->request_method = $request_method;

			if ( isset( $response['headers'] ) ) {
				$this->parse_headers( $response['headers'] );
			}

			if ( isset( $response['body'] ) ) {
				$this->parse_body( $response['body'] );
			}

			if ( isset( $response['response'] ) ) {
				$this->parse_response( $response['response'] );
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
		 * Returns the response code.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return int Response code.
		 */
		public function get_response_code() {
			return $this->response['code'];
		}

		/**
		 * Returns the response message.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string Response message.
		 */
		public function get_response_message() {
			return $this->response['message'];
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
		 * Parses the response headers.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param array $headers Array of header strings.
		 */
		private function parse_headers( $headers ) {
			foreach ( $headers as $key => $value ) {
				if ( is_int( $key ) && is_string( $value ) ) {
					list( $key, $value ) = explode( ':', $header, 2 );

					$value = trim( $value );
					preg_replace( '#(\s+)#i', ' ', $value );

					if ( ! isset( $this->headers[ $key ] ) ) {
						$this->headers[ $key ] = array();
					}

					$this->headers[ $key ][] = $value;
				} else {
					$this->headers[ $key ] = (array) $value;
				}
			}
		}

		/**
		 * Parses the response body.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param string $body Body content.
		 */
		private function parse_body( $body ) {
			$this->raw_body = $body;

			if ( $this->route->method_uses_json_response( $this->request_method ) ) {
				$this->params = json_decode( $body, true );
			}
		}

		/**
		 * Parses the response code and message.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param array $response Array with keys 'code' and 'message'.
		 */
		private function parse_response( $response ) {
			$this->response['code'] = isset( $response['code'] ) ? (int) $response['code'] : 200;
			$this->response['message'] = isset( $response['message'] ) ? $response['message'] : 'OK';
		}
	}

}