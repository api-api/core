<?php
/**
 * API-API Structure interface
 *
 * @package APIAPI
 * @subpackage Structures
 * @since 1.0.0
 */

namespace awsmug\APIAPI\Structures;

if ( ! interface_exists( 'awsmug\APIAPI\Structures\Structure_Interface' ) ) {

	/**
	 * Structure interface for the API-API.
	 *
	 * Represents a specific API structure.
	 *
	 * @since 1.0.0
	 */
	interface Structure_Interface {
		/**
		 * Returns the general API object for a specific API-API scope.
		 *
		 * The API object will be instantiated if it does not exist yet.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\APIAPI $apiapi The API-API instance to get the API object for.
		 * @return awsmug\APIAPI\Request\API The API object.
		 */
		public function get_api_object( $apiapi );

		/**
		 * Returns a scoped request object for a specific route of this API.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\APIAPI $apiapi    The API-API instance to get the API object for.
		 * @param string               $route_uri URI of the route.
		 * @param string               $method    Optional. Either 'GET', 'POST', 'PUT', 'PATCH'
		 *                                        or 'DELETE'. Default 'GET'.
		 * @return awsmug\APIAPI\Request\Request Request object for the route.
		 */
		public function get_request_object( $apiapi, $route_uri, $method = 'GET' );

		/**
		 * Returns the route object for a specific route.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $route_uri URI of the route.
		 * @return awsmug\APIAPI\Request\Endpoint|null The route object, or null if it does
		 *                                             not exist.
		 */
		public function get_route_object( $route_uri );

		/**
		 * Checks whether a specific route exists.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $route_uri URI of the route.
		 * @return bool True if the route exists, otherwise false.
		 */
		public function has_route( $route_uri );

		/**
		 * Returns all available routes.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return array Array of route objects.
		 */
		public function get_route_objects();

		/**
		 * Returns the authenticator name for the API.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string Authenticator name, or empty string if not set.
		 */
		public function get_authenticator();

		/**
		 * Checks whether the API should use an authenticator.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return bool True if the API should use an authenticator, otherwise false.
		 */
		public function has_authenticator();

		/**
		 * Returns this API's base URI for a specific mode.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $mode Optional. Mode for which to get the base URI. Default empty.
		 * @return string Base URI.
		 */
		public function get_base_uri( $mode = '' );
	}

}
