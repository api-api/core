<?php
/**
 * API-API Main class
 *
 * @package APIAPICore
 * @since 1.0.0
 */

namespace APIAPI\Core;

use APIAPI\Core\Request\Route_Response;

if ( ! class_exists( 'APIAPI\Core\APIAPI' ) ) {

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
		 * @var APIAPI\Core\Manager
		 */
		private $manager;

		/**
		 * The config updater for this API-API instance.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var APIAPI\Core\Config_Updater
		 */
		private $config_updater;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string                   $name    Slug of this instance.
		 * @param APIAPI\Core\Manager      $manager The APIAPI Manager object.
		 * @param APIAPI\Core\Config|array $config  Optional. Configuration object or associative array. Default empty array.
		 */
		public function __construct( $name, $manager, $config = array() ) {
			$this->manager = $manager;

			$this->set_name( $name );
			$this->config( $config );

			$this->set_config_updater( $this->config() );

			$this->manager->hooks()->trigger( 'apiapi.' . $this->get_name() . '.started' );
		}

		/**
		 * Returns the API object for a specific API.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $api_name Unique slug of the API. Must match the slug of a registered structure.
		 * @return APIAPI\Core\Request\API The API object.
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
		 * @return APIAPI\Core\Request\Route_Request Request object.
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
		 * @param APIAPI\Core\Request\Route_Request $request The request to send.
		 * @return APIAPI\Core\Request\Route_Response The returned response.
		 */
		public function send_request( $request ) {
			$this->manager->hooks()->trigger( 'apiapi.' . $this->get_name() . '.pre_send_request', $request, $this );

			$missing_parameters = $request->is_valid();
			if ( is_array( $missing_parameters ) ) {
				throw new Exception( sprintf( 'The request to send is invalid. The following required parameters have not been provided: %s', implode( ', ', $missing_parameters ) ) );
			}

			$this->authenticate_request( $request );

			$transporters = $this->manager->transporters();

			if ( ! $this->config->isset( 'transporter' ) ) {
				$transporter = $transporters->get_default();
				if ( null === $transporter ) {
					throw new Exception( 'The request cannot be sent as no transporter is available.' );
				}
			} else {
				$transporter_name = $this->config->get( 'transporter' );

				if ( ! $transporters->is_registered( $transporter_name ) ) {
					throw new Exception( sprintf( 'The request cannot be sent as the transporter with the name %s is not registered.', $transporter_name ) );
				}

				$transporter = $transporters->get( $transporter_name );
			}

			$response_data = $transporter->send_request( $request );

			$route = $request->get_route_object();

			$response = $route->create_response_object( $response_data, $request->get_method() );

			$this->manager->hooks()->trigger( 'apiapi.' . $this->get_name() . '.response_received', $response, $request, $this );

			return $response;
		}

		/**
		 * Authenticates a request.
		 *
		 * The request will only be authenticated if it is necessary.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param APIAPI\Core\Request\Route_Request $request The request to authenticate.
		 */
		private function authenticate_request( $request ) {
			$authenticator_name = $request->get_authenticator();
			if ( ! empty( $authenticator_name ) ) {
				$this->manager->hooks()->trigger( 'apiapi.' . $this->get_name() . '.pre_authenticate_request', $request, $this );

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

		/**
		 * Creates a config updater for this API-API instance.
		 *
		 * This only happens if the key 'config_updater' is set to true in the configuration.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param APIAPI\Core\Config $config Configuration object.
		 */
		private function set_config_updater( $config ) {
			if ( ! $config->get( 'config_updater' ) ) {
				return;
			}

			$storage_name = $config->get( 'config_updater_storage' );
			if ( ! $storage_name ) {
				return;
			}

			$storages = $this->manager->storages();

			if ( ! $storages->is_registered( $storage_name ) ) {
				throw new Exception( sprintf( 'The storage %s is not registered.', $storage_name ) );
			}

			$storage = $storages->get( $storage_name );

			$this->config_updater = new Config_Updater( $this->get_name(), $config, $storage );
		}
	}

}
