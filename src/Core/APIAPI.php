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

			$this->set_config_updater();

			$this->trigger_hook( 'started' );
		}

		/**
		 * Attaches a hook callback for this API-API instance.
		 *
		 * The returned hook object can be passed to `APIAPI\Core\APIAPI::hook_off()` to
		 * remove it again.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string   $hook_name Hook name.
		 * @param callable $callback  Hook callback.
		 * @param int      $priority  Optional. Hook priority. Default 10.
		 * @return APIAPI\Core\Hook Hook object.
		 */
		public function hook_on( $hook_name, $callback, $priority = 10 ) {
			$hook_name = 'apiapi.' . $this->get_name() . '.' . $hook_name;

			return $this->manager->hooks()->on( $hook_name, $callback, $priority );
		}

		/**
		 * Removes a previously attached hook callback for this API-API instance.
		 *
		 * The object to pass to this method must have been returned by `APIAPI\Core\APIAPI::hook_on()`.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param APIAPI\Core\Hook $hook Hook object.
		 */
		public function hook_off( $hook ) {
			$hook_name = $hook->get_name();

			if ( 0 !== strpos( $hook_name, 'apiapi.' . $this->get_name() . '.' ) ) {
				throw new Exception( sprintf( 'Invalid usage of hook object with hook %s.', $hook_name ) );
			}

			$this->manager->hooks()->off( $hook );
		}

		/**
		 * Triggers a hook for this API-API instance.
		 *
		 * Any additional parameters passed to the method are passed to each hook callback.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $hook_name Hook name.
		 */
		public function trigger_hook( $hook_name ) {
			$args = func_get_args();
			$args[0] = 'apiapi.' . $this->get_name() . '.' . $args[0];

			call_user_func_array( array( $this->manager->hooks(), 'trigger' ), $args );
		}

		/**
		 * Checks whether a hook is currently triggered.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $hook_name Hook name.
		 * @return bool True if the hook is triggered, false otherwise.
		 */
		public function is_hook_triggered( $hook_name ) {
			$hook_name = 'apiapi.' . $this->get_name() . '.' . $hook_name;

			return $this->manager->hooks()->is_hook_triggered( $hook_name );
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
			$this->trigger_hook( 'pre_send_request', $request, $this );

			$missing_parameters = $request->is_valid();
			if ( is_array( $missing_parameters ) ) {
				throw new Exception( sprintf( 'The request to send is invalid. The following required parameters have not been provided: %s', implode( ', ', $missing_parameters ) ) );
			}

			$this->authenticate_request( $request );

			$transporters = $this->manager->transporters();

			if ( ! $this->config->exists( 'transporter' ) ) {
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

			$this->trigger_hook( 'response_received', $response, $request, $this );

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
				$this->trigger_hook( 'pre_authenticate_request', $request, $this );

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
		 */
		private function set_config_updater() {
			$config = $this->config();

			$updater = $config->get( 'config_updater' );
			if ( ! $updater ) {
				return;
			}

			if ( is_a( $updater, 'APIAPI\Core\Config_Updater' ) ) {
				$this->config_updater = $updater;
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

			$args = $config->get( 'config_updater_args' );
			if ( ! is_array( $args ) ) {
				$args = array();
			}

			$this->config_updater = new Config_Updater( $this, $storage, $args );
		}
	}

}
