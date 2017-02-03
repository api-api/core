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
	 * @param string                          $name  Unique slug of the instance.
	 * @param APIAPI\Core\Config|array|bool $force Optional. Whether to create the instance if it does not exist.
	 *                                               Can also be a configuration object or array to fill the set up
	 *                                               the new instance with this configuration. Default false.
	 * @return APIAPI\Core\APIAPI|null The API-API instance, or null if it does not exist.
	 */
	function apiapi( $name, $force = false ) {
		return apiapi_manager()->get_instance( $name, $force );
	}

}
