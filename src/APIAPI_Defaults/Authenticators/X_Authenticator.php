<?php
/**
 * X_Authenticator class
 *
 * @package APIAPI_Defaults
 * @subpackage Authenticators
 * @since 1.0.0
 */

namespace awsmug\APIAPI_Defaults\Authenticators;

use awsmug\APIAPI\Authenticators\Authenticator;
use awsmug\APIAPI\Exception;

if ( ! class_exists( 'awsmug\APIAPI_Defaults\Authenticators\X_Authenticator' ) ) {

	/**
	 * Authenticator class for custom HTTP X Authentication.
	 *
	 * @since 1.0.0
	 */
	class X_Authenticator extends Authenticator {
		/**
		 * Authenticates a request.
		 *
		 * This method does not yet actually authenticate the request with the server. It only sets
		 * the required values on the request object.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Request\Route_Request $request The request to send.
		 */
		public function authenticate_request( $request ) {
			$data = $this->parse_authentication_data( $request );

			if ( empty( $data['header_name'] ) ) {
				$data['header_name'] = 'X-Authorization';
			} elseif ( 0 !== strpos( $data['header_name'], 'X-' ) ) {
				$data['header_name'] = 'X-' . $data['header_name'];
			}

			if ( empty( $data['token'] ) ) {
				throw new Exception( sprintf( 'The request to %s could not be authenticated as a token has not been passed.', $request->get_uri() ) );
			}

			$request->set_header( $data['header_name'], $data['token'] );
		}

		/**
		 * Checks whether a request is authenticated.
		 *
		 * This method does not check whether the request was actually authenticated with the server.
		 * It only checks whether authentication data has been properly set on it.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Request\Route_Request $request The request to check.
		 * @return bool True if the request is authenticated, otherwise false.
		 */
		public function is_authenticated( $request ) {
			$data = $this->parse_authentication_data( $request );

			if ( empty( $data['header_name'] ) ) {
				$data['header_name'] = 'X-Authorization';
			} elseif ( 0 !== strpos( $data['header_name'], 'X-' ) ) {
				$data['header_name'] = 'X-' . $data['header_name'];
			}

			$header_value = $request->get_header( $data['header_name'] );

			return null !== $header_value;
		}

		/**
		 * Sets the default authentication arguments.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function set_default_args() {
			$this->default_args = array(
				'header_name' => 'Authorization',
				'token'       => '',
			);
		}
	}

}
