<?php
/**
 * API-API Main class
 *
 * @package APIAPI
 * @subpackage Core
 * @since 1.0.0
 */

namespace awsmug\APIAPI;

use awsmug\APIAPI\Request\Response;

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

		/**
		 * Sends a request and returns the response.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Request\Request $request The request to send.
		 * @return awsmug\APIAPI\Request\Response The returned response.
		 */
		public function send_request( $request ) {
			$missing_parameters = $request->is_valid();
			if ( is_array( $missing_parameters ) ) {
				throw new Exception( sprintf( 'The request to send is invalid. The following required parameters have not been provided: %s', implode( ', ', $missing_parameters ) ) );
			}

			$this->authenticate_request( $request );

			$transporter_name = $this->config->get( 'transporter' );
			if ( null === $transporter_name ) {
				throw new Exception( 'The request cannot be sent as no transporter has been provided.' );
			}

			$transporters = $this->manager->transporters();

			if ( ! $transporters->is_registered( $transporter_name ) ) {
				throw new Exception( sprintf( 'The request cannot be sent as the transporter with the name %s is not registered.', $transporter_name ) );
			}

			$transporter = $transporters->get( $transporter_name );

			$response_data = $transporter->send_request( $request );

			$route = $request->get_route_object();

			return $route->create_response_object( $response_data, $request->get_method() );
		}

		/**
		 * Authenticates a request.
		 *
		 * The request will only be authenticated if it is necessary.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param awsmug\APIAPI\Request\Request $request The request to authenticate.
		 */
		private function authenticate_request( $request ) {
			$authenticator_name = $request->get_authenticator();
			if ( ! empty( $authenticator_name ) ) {
				$authenticators = $this->manager->authenticators();

				if ( ! $authenticators->is_registered( $authenticator_name ) ) {
					throw new Exception( sprintf( 'The request cannot be authenticated as the authenticator with the name %s is not registered.', $authenticator_name ) );
				}

				$authenticator = $authenticators->get( $authenticator_name );
				if ( ! $authenticator->is_authenticated( $request ) ) {
					$authenticator->authenticate_request( $request );
				}
			}
		}
	}

}
