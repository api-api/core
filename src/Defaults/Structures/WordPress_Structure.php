<?php
/**
 * WordPress_Structure class
 *
 * @package APIAPIDefaults
 * @subpackage Structures
 * @since 1.0.0
 */

namespace APIAPI\Defaults\Structures;

use APIAPI\Core\Structures\Structure;
use APIAPI\Core\Request\Request;
use APIAPI\Core\Request\Response;
use APIAPI\Core\Exception;

if ( ! class_exists( 'APIAPI\Defaults\Structures\WordPress_Structure' ) ) {

	/**
	 * Structure class for a WordPress API.
	 *
	 * @since 1.0.0
	 */
	class WordPress_Structure extends Structure {
		/**
		 * Callback to get a cached structure response.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var callable|null
		 */
		protected $get_cached_structure_callback = null;

		/**
		 * Callback to update the structure response in cache.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var callable|null
		 */
		protected $update_cached_structure_callback = null;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name     Slug of the instance.
		 * @param string $base_uri Base URI to the website's API.
		 * @param array  $args     {
		 *     Optional. Array of arguments. Default empty array.
		 *
		 *     @type callable $get_cached_structure_callback    Callback to get a cached structure response.
		 *                                                      Must accept the base URI as sole parameter.
		 *                                                      Default null.
		 *     @type callable $update_cached_structure_callback Callback to update the structure response in
		 *                                                      cache. Must accept two parameters: The base
		 *                                                      URI as first and the structure array as second.
		 *                                                      Default null.
		 * }
		 */
		public function __construct( $name, $base_uri, $args = array() ) {
			$this->base_uri = $base_uri;

			foreach ( $args as $key => $value ) {
				if ( isset( $this->$key ) ) {
					$this->$key = $value;
				}
			}

			parent::__construct( $name );
		}

		/**
		 * Sets up the API structure.
		 *
		 * This method should populate the routes array, and can also be used to
		 * handle further initialization functionality, like setting the authenticator
		 * class and default authentication data.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function setup() {
			$structure_response = is_callable( $this->get_cached_structure_callback ) ? call_user_func( $this->get_cached_structure_callback, $this->base_uri ) : false;
			if ( ! is_array( $structure_response ) ) {
				$transporter = $this->get_default_transporter();

				$request = new Request( $this->base_uri, 'GET' );
				$response = new Response( $transporter->send_request( $request ) );

				if ( null === $response->get_param( 'routes' ) ) {
					throw new Exception( sprintf( 'The structure request to %s returned an invalid response.', $this->base_uri ) );
				}

				$structure_response = array(
					'namespaces'     => $response->get_param( 'namespaces' ),
					'authentication' => $response->get_param( 'authentication' ),
					'routes'         => $response->get_param( 'routes' ),
				);

				if ( is_callable( $this->update_cached_structure_callback ) ) {
					call_user_func( $this->update_cached_structure_callback, $this->base_uri, $structure_response );
				}
			}

			if ( isset( $structure_response['authentication']['oauth1'] ) ) {
				$this->authenticator = 'oauth1';
				$this->authentication_data_defaults = $structure_response['authentication']['oauth1'];
			} else {
				$this->authenticator = 'x';
				$this->authentication_data_defaults = array(
					'header_name' => 'WP-Nonce',
				);
			}

			$uris_which_require_auth = $this->get_uris_which_require_auth();

			foreach ( $structure_response['routes'] as $uri => $data ) {
				/* Ignore basic namespace discovery endpoints. */
				if ( '/' === $uri || in_array( ltrim( $uri, '/' ), $structure_response['namespaces'], true ) ) {
					continue;
				}

				$route_data = array(
					'methods' => array(),
				);

				foreach ( $data['endpoints'] as $endpoint ) {
					foreach ( $endpoint['methods'] as $method ) {
						$needs_authentication = true;
						if ( 'GET' === $method && ! in_array( $uri, $uris_which_require_auth, true ) ) {
							$needs_authentication = false;
						}

						$route_data['methods'][ $method ] = array(
							'params'                 => $endpoint['args'],
							'supports_custom_params' => false,
							'request_data_type'      => 'raw',
							'needs_authentication'   => $needs_authentication,
						);
					}
				}

				$this->routes[ $uri ] = $route_data;
			}
		}

		/**
		 * Gets the default transporter object.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @return APIAPI\Core\Transporters\Transporter Default transporter object.
		 */
		protected function get_default_transporter() {
			//TODO: This breaks the dependency injection pattern.
			return apiapi_manager()->transporters()->get_default();
		}

		/**
		 * Returns the route URIs which require authentication even on GET requests.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @return array Array of route URIs.
		 */
		protected function get_uris_which_require_auth() {
			return array();
		}
	}

}
