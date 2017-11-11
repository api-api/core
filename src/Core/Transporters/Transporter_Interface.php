<?php
/**
 * API-API Transporter interface
 *
 * @package APIAPICore
 * @subpackage Transporters
 * @since 1.0.0
 */

namespace APIAPI\Core\Transporters;

if ( ! interface_exists( 'APIAPI\Core\Transporters\Transporter_Interface' ) ) {

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
		 * @param \APIAPI\Core\Request\Request $request The request to send.
		 * @return array The returned response as an array with 'headers', 'body',
		 *               and 'response' key. The array does not necessarily
		 *               need to include all of these keys.
		 */
		public function send_request( $request );
	}

}
