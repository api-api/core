<?php
/**
 * Twitter_OAuth1_Authenticator class
 *
 * @package APIAPIDefaults
 * @subpackage Authenticators
 * @since 1.0.0
 */

namespace APIAPI\Defaults\Authenticators;

if ( ! class_exists( 'APIAPI\Defaults\Authenticators\Twitter_OAuth1_Authenticator' ) ) {

	/**
	 * Authenticator class for OAuth 1.0 Authentication adjusted for Twitter.
	 *
	 * @since 1.0.0
	 */
	class Twitter_OAuth1_Authenticator extends OAuth1_Authenticator {
		/**
		 * Returns protocol parameters.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $consumer_key      The consumer key for the API.
		 * @param array  $additional_params Additional protocol parameters to merge.
		 * @return array Array of protocol parameters.
		 */
		protected function get_protocol_params( $consumer_key, $additional_params = array() ) {
			$protocol_params = parent::get_protocol_params( $consumer_key, $additional_params );

			/* Twitter does not want this as it can be set in their app configuration. */
			if ( isset( $protocol_params['oauth_callback'] ) ) {
				unset( $protocol_params['oauth_callback'] );
			}

			return $protocol_params;
		}
	}

}
