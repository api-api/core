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
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Request\Request $request The request to send.
		 */
		public function authenticate_request( $request );

		/**
		 * Checks whether a request is authenticated.
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
