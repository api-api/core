<?php
/**
 * Requests_Transporter class
 *
 * @package APIAPI_Defaults
 * @subpackage Transporters
 * @since 1.0.0
 */

namespace awsmug\APIAPI_Defaults\Transporters;

use awsmug\APIAPI\Transporters\Transporter;
use awsmug\APIAPI\Exception;

if ( ! class_exists( 'awsmug\APIAPI_Defaults\Transporters\Requests_Transporter' ) ) {

	/**
	 * Transporter class for Requests.
	 *
	 * @since 1.0.0
	 */
	class Requests_Transporter extends Transporter {
		/**
		 * Sends a request and returns the response.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Request\Request $request The request to send.
		 * @return array The returned response as an array with 'headers', 'body',
		 *               and 'response' key. The array does not necessarily
		 *               need to include all of these keys.
		 */
		public function send_request( $request ) {
			$url     = $request->get_uri();
			$headers = $request->get_headers();
			$data    = $request->get_params();
			$type    = $request->get_method();
			$options = array();

			if ( ! empty( $data ) ) {
				if ( 'GET' === $type ) {
					$options['data_format'] = 'query';
				} else {
					$options['data_format'] = 'body';

					if ( $request->should_use_json() ) {
						$data = wp_json_encode( $data );
						if ( ! $data ) {
							throw new Exception( sprintf( 'The request to %s could not be sent as the data could not be JSON-encoded.', $url ) );
						}
					}
				}
			}

			try {
				$requests_response = \Requests::request( $url, $headers, $data, $type, $options );
			} catch ( \Requests_Exception $e ) {
				throw new Exception( sprintf( 'The request to %1$s could not be sent: %2$s', $url, $e->getMessage() ) );
			}

			$response = array(
				'headers'  => $requests_response->headers->getAll(),
				'body'     => $requests_response->body,
				'response' => array(
					'code'    => (int) $requests_response->status_code,
					'message' => $this->get_status_message( $requests_response->status_code ),
				),
			);

			return $response;
		}
	}

}
