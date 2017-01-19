<?php
/**
 * API-API Transporter interface
 *
 * @package APIAPI
 * @subpackage Transporters
 * @since 1.0.0
 */

namespace awsmug\APIAPI\Transporters;

if ( ! interface_exists( 'awsmug\APIAPI\Transporters\Transporter_Interface' ) ) {

	/**
	 * Transporter interface for the API-API.
	 *
	 * Represents a specific transporter method.
	 *
	 * @since 1.0.0
	 */
	interface Transporter_Interface {
		/**
		 * Sends a request and returns the response.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Request\Request $request The request to send.
		 * @return awsmug\APIAPI\Request\Response The returned response.
		 */
		public function send_request( $request );
	}

}
