<?php
/**
 * WordPress_Transporter class
 *
 * @package APIAPI_Defaults
 * @subpackage Transporters
 * @since 1.0.0
 */

namespace awsmug\APIAPI_Defaults\Transporters;

use awsmug\APIAPI\Transporters\Transporter;
use awsmug\APIAPI\Exception;

if ( ! class_exists( 'awsmug\APIAPI_Defaults\Transporters\WordPress_Transporter' ) ) {

	/**
	 * Transporter class for WordPress.
	 *
	 * @since 1.0.0
	 */
	class WordPress_Transporter extends Transporter {
		/**
		 * Sends a request and returns the response.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Request\Request $request The request to send.
		 * @return array The returned response as an array with 'headers', 'body',
		 *               'response' and 'cookies' key. The array does not necessarily
		 *               need to include all of these keys.
		 */
		public function send_request( $request ) {
			$url = $request->get_uri();

			$args = array(
				'method'  => $request->get_method(),
				'headers' => array(),
			);

			foreach ( $request->get_headers( true ) as $header_name => $header_values ) {
				foreach ( $header_values as $header_value ) {
					$args['headers'][] = $header_name . ': ' . $header_value;
				}
			}

			$params = $request->get_params();
			if ( ! empty( $params ) ) {
				if ( 'GET' === $args['method'] ) {
					$url = add_query_arg( $params, $url );
				} elseif ( $request->should_use_json() ) {
					$args['body'] = wp_json_encode( $params );
					if ( ! $args['body'] ) {
						throw new Exception( sprintf( 'The request to %s could not be sent as the data could not be JSON-encoded.', $url ) );
					}
				} else {
					$args['body'] = http_build_query( $params, null, '&' );
				}
			}

			$response = wp_remote_request( $url, $args );
			if ( is_wp_error( $response ) ) {
				throw new Exception( sprintf( 'The request to %1$s could not be sent: %2$s', $url, $response->get_error_message() ) );
			}

			// Cookies are not supported at this point.
			if ( isset( $response['cookies'] ) ) {
				unset( $response['cookies'] );
			}

			return $response;
		}
	}

}
