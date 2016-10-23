<?php
/**
 * Shortcut functions to access the main objects.
 *
 * @package APIAPI
 * @subpackage Core
 * @since 1.0.0
 */

if ( ! function_exists( 'apiapi_manager' ) ) :

/**
 * Returns the API-API manager instance.
 *
 * @since 1.0.0
 *
 * @return awsmug\APIAPI\Manager The API-API manager instance.
 */
function apiapi_manager() {
	return awsmug\APIAPI\Manager::instance();
}

endif;

if ( ! function_exists( 'apiapi' ) ) :

/**
 * Returns a specific API-API instance.
 *
 * @since 1.0.0
 *
 * @param string                          $name  Unique slug of the instance.
 * @param awsmug\APIAPI\Config|array|bool $force Optional. Whether to create the instance if it does not exist.
 *                                               Can also be a configuration object or array to fill the set up
 *                                               the new instance with this configuration. Default false.
 * @return awsmug\APIAPI\APIAPI|null The API-API instance, or null if it does not exist.
 */
function apiapi( $name, $force = false ) {
	return awsmug\APIAPI\Manager::instance()->get_instance( $name, $force );
}

endif;
