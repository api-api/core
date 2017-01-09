<?php
/**
 * API-API Main class
 *
 * @package APIAPI
 * @subpackage Core
 * @since 1.0.0
 */

namespace awsmug\APIAPI;

if ( ! class_exists( 'awsmug\APIAPI\APIAPI' ) ) {

	/**
	 * Main class for the API-API.
	 *
	 * @since 1.0.0
	 */
	class APIAPI {
		use Name_Trait, Config_Trait;

		/**
		 * Reference to the global APIAPI Manager object.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var awsmug\APIAPI\Manager
		 */
		private $manager;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string                     $name    Slug of this instance.
		 * @param awsmug\APIAPI\Manager      $manager The APIAPI Manager object.
		 * @param awsmug\APIAPI\Config|array $config  Optional. Configuration object or associative array. Default empty array.
		 */
		public function __construct( $name, $manager, $config = array() ) {
			$this->manager = $manager;

			$this->set_name( $name );
			$this->config( $config );
		}

		/**
		 * Returns the API object for a specific API.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $api_name Unique slug of the API. Must match the slug of a registered structure.
		 * @return awsmug\APIAPI\Request\API The API object.
		 */
		public function get_api_object( $api_name ) {
			$structures = $this->manager->structures();

			if ( ! $structures->is_registered( $api_name ) ) {
				throw new Exception( sprintf( 'The structure for the API %s is not registered.', $api_name ) );
			}

			$structure = $structures->get( $api_name );

			return $structure->get_api_object( $this );
		}

		/**
		 * Returns a request object for a specific route of a specific API.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $api_name  Unique slug of the API. Must match the slug of a registered structure.
		 * @param string $route_uri URI of the route.
		 * @param string $method    Optional. Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'. Default 'GET'.
		 * @return awsmug\APIAPI\Request\Request Request object.
		 */
		public function get_request_object( $api_name, $route_uri, $method = 'GET' ) {
			return $this->get_api_object( $api_name )->get_request_object( $route_uri, $method );
		}
	}

}
