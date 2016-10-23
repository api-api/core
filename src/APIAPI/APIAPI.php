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
		/**
		 * Reference to the global APIAPI Manager object.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var awsmug\APIAPI\Manager
		 */
		private $manager;

		/**
		 * Configuration object.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var awsmug\APIAPI\Config
		 */
		private $config;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Manager      $manager The APIAPI Manager object.
		 * @param awsmug\APIAPI\Config|array $config  Optional. Configuration object or associative array. Default empty.
		 */
		public function __construct( $manager, $config = null ) {
			$this->manager = $manager;

			if ( is_a( $config, 'awsmug\APIAPI\Config' ) ) {
				$this->config = $config;
			} else {
				$this->config = new Config( $config );
			}
		}

		/**
		 * Returns the configuration object.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return awsmug\APIAPI\Config Configuration object for the manager.
		 */
		public function config() {
			return $this->config;
		}

		/**
		 * Creates a new request object for an endpoint of a specific API.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $api_name      Unique slug of the API. Must match the slug of a registered structure.
		 * @param string $endpoint_name Endpoint name. Must be one of the endpoints available in the structure.
		 * @return awsmug\APIAPI\Request The new request object.
		 */
		//TODO: the following code shows how actual functionality should work.
		/*public function create_request_object( $api_name, $endpoint_name ) {
			$structures = $this->manager->structures();

			if ( ! $structures->is_registered( $api_name ) ) {
				throw new Exception( sprintf( 'The structure for the API %s is not registered.', $api_name ) );
			}

			$structure = $structures->get( $api_name );

			if ( ! $structure->has_endpoint( $endpoint_name ) ) {
				throw new Exception( sprintf( 'The API %1$s does not provide a %2$s endpoint.', $api_name, $endpoint_name ) );
			}

			// The following method does `return new Request( $this, $apiapi );`
			return $structure->get_endpoint( $endpoint_name )->create_request_object( $this );
		}*/
	}

}
