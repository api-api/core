<?php
/**
 * API-API Authenticator interface
 *
 * @package APIAPI
 * @subpackage Authenticators
 * @since 1.0.0
 */

namespace awsmug\APIAPI\Authenticators;

if ( ! interface_exists( 'awsmug\APIAPI\Authenticators\Authenticator_Interface' ) ) {

	/**
	 * Authenticator interface for the API-API.
	 *
	 * Represents a specific authenticator.
	 *
	 * @since 1.0.0
	 */
	interface Authenticator_Interface {
		/**
		 * Authenticates a request.
		 *
		 * This method does not yet actually authenticate the request with the server. It only sets
		 * the required values on the request object.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Request\Request $request The request to send.
		 */
		public function authenticate_request( $request );

		/**
		 * Checks whether a request is authenticated.
		 *
		 * This method does not check whether the request was actually authenticated with the server.
		 * It only checks whether authentication data has been properly set on it.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Request\Request $request The request to check.
		 * @return bool True if the request is authenticated, otherwise false.
		 */
		public function is_authenticated( $request );
	}

}
