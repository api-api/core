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
		use Config_Trait;

		/**
		 * Slug of this instance.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var string
		 */
		private $name;

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
			$this->name    = $name;
			$this->manager = $manager;

			$this->config( $config );
		}

		/**
		 * Returns the slug of this instance.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string Slug of this instance.
		 */
		public function get_name() {
			return $this->name;
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
		//TODO: the following code shows how actual functionality should work.
		/*public function get_api_object( $api_name ) {
			$structures = $this->manager->structures();

			if ( ! $structures->is_registered( $api_name ) ) {
				throw new Exception( sprintf( 'The structure for the API %s is not registered.', $api_name ) );
			}

			$structure = $structures->get( $api_name );

			return $structure->get_api_object( $this );
		}*/

		/**
		 * Returns a request object for an endpoint of a specific API.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $api_name      Unique slug of the API. Must match the slug of a registered structure.
		 * @param string $endpoint_name Endpoint name. Must be one of the endpoints available in the structure.
		 * @return awsmug\APIAPI\Request\Request The request object.
		 */
		//TODO: the following code shows how actual functionality should work.
		/*public function get_request_object( $api_name, $endpoint_name ) {
			$structures = $this->manager->structures();

			if ( ! $structures->is_registered( $api_name ) ) {
				throw new Exception( sprintf( 'The structure for the API %s is not registered.', $api_name ) );
			}

			$structure = $structures->get( $api_name );

			if ( ! $structure->has_endpoint( $endpoint_name ) ) {
				throw new Exception( sprintf( 'The API %1$s does not provide a %2$s endpoint.', $api_name, $endpoint_name ) );
			}

			// The following method does `return new Request( $this, $apiapi );`
			return $structure->get_endpoint( $endpoint_name )->get_request_object( $this );
		}*/
	}

}
