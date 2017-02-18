<?php
/**
 * Shortcut functions to access the main objects.
 *
 * @package APIAPICore
 * @since 1.0.0
 */

if ( ! function_exists( 'apiapi_manager' ) ) {

	/**
	 * Returns the API-API manager instance.
	 *
	 * @since 1.0.0
	 *
	 * @return APIAPI\Core\Manager The API-API manager instance.
	 */
	function apiapi_manager() {
		return APIAPI\Core\Manager::instance();
	}

}

if ( ! function_exists( 'apiapi' ) ) {

	/**
	 * Returns a specific API-API instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string                        $name  Unique slug of the instance.
	 * @param APIAPI\Core\Config|array|bool $force Optional. Whether to create the instance if it does not exist.
	 *                                             Can also be a configuration object or array to fill the set up
	 *                                             the new instance with this configuration. Default false.
	 * @return APIAPI\Core\APIAPI|null The API-API instance, or null if it does not exist.
	 */
	function apiapi( $name, $force = false ) {
		return apiapi_manager()->get_instance( $name, $force );
	}

}

if ( ! function_exists( 'apiapi_register_defaults' ) ) {
	function apiapi_register_defaults() {
		if ( function_exists( 'wp_remote_request' ) ) {
			apiapi_manager()->transporters()->register( 'wordpress', 'APIAPI\Defaults\Transporters\WordPress_Transporter' );
		}

		if ( class_exists( 'Requests' ) ) {
			apiapi_manager()->transporters()->register( 'requests', 'APIAPI\Defaults\Transporters\Requests_Transporter' );
		}

		if ( function_exists( 'curl_init' ) ) {
			apiapi_manager()->transporters()->register( 'curl', 'APIAPI\Defaults\Transporters\cURL_Transporter' );
		}

		apiapi_manager()->storages()->register( 'cookie', 'APIAPI\Defaults\Storages\Cookie_Storage' );
		apiapi_manager()->storages()->register( 'session', 'APIAPI\Defaults\Storages\Session_Storage' );

		if ( function_exists( 'get_option' ) ) {
			apiapi_manager()->storages()->register( 'wordpress-option', 'APIAPI\Defaults\Storages\WordPress_Option_Storage' );
		}

		if ( function_exists( 'get_user_meta' ) ) {
			apiapi_manager()->storages()->register( 'wordpress-user-meta', 'APIAPI\Defaults\Storages\WordPress_User_Meta_Storage' );
		}

		apiapi_manager()->authenticators()->register( 'basic', 'APIAPI\Defaults\Authenticators\Basic_Authenticator' );
		apiapi_manager()->authenticators()->register( 'bearer', 'APIAPI\Defaults\Authenticators\Bearer_Authenticator' );
		apiapi_manager()->authenticators()->register( 'x', 'APIAPI\Defaults\Authenticators\X_Authenticator' );
		apiapi_manager()->authenticators()->register( 'oauth1', 'APIAPI\Defaults\Authenticators\OAuth1_Authenticator' );
		apiapi_manager()->authenticators()->register( 'twitter-oauth1', 'APIAPI\Defaults\Authenticators\Twitter_OAuth1_Authenticator' );

		apiapi_manager()->structures()->register( 'leaves-and-love', 'APIAPI\Defaults\Structures\WordPress_Structure', 'https://leaves-and-love.net/api/' );
		apiapi_manager()->structures()->register( 'google-compute-v1', 'APIAPI\Defaults\Structures\Google_Structure', 'https://www.googleapis.com/discovery/v1/apis/compute/v1/rest' );
		apiapi_manager()->structures()->register( 'twitter', 'APIAPI\Defaults\Structures\Twitter_Structure' );
	}

	apiapi_register_defaults();
}
