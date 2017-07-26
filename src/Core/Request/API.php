<?php
/**
 * API-API class for a scoped external API
 *
 * @package APIAPICore
 * @subpackage Request
 * @since 1.0.0
 */

namespace APIAPI\Core\Request;

use APIAPI\Core\Name_Trait;
use APIAPI\Core\Config_Trait;

if ( ! class_exists( 'APIAPI\Core\Request\API' ) ) {

	/**
	 * API class for the API-API.
	 *
	 * Represents a external API, scoped for an API-API instance.
	 *
	 * @since 1.0.0
	 */
	class API {
		use Name_Trait, Config_Trait;

		/**
		 * The API structure object.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var APIAPI\Core\Structures\Structure
		 */
		protected $structure;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param APIAPI\Core\Structures\Structure $structure The API structure object.
		 * @param APIAPI\Core\Config|array         $config    Optional. Configuration object or associative array. Default empty array.
		 */
		public function __construct( $structure, $config = array() ) {
			$this->structure = $structure;

			$this->set_name( $this->structure->get_name() );
			$this->config( $config );
		}

		/**
		 * Returns a scoped request object for a specific route of this API.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $route_uri URI of the route.
		 * @param string $method    Optional. Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 *                          Default 'GET'.
		 * @return APIAPI\Core\Request\Route_Request Request object for the route.
		 */
		public function get_request_object( $route_uri, $method = 'GET' ) {
			$route = $this->structure->get_route_object( $route_uri );

			$mode                = $this->get_mode();
			$authenticator       = $this->get_authenticator();
			$authentication_data = $this->get_authentication_data();

			return $route->create_request_object( $route_uri, $method, $mode, $authenticator, $authentication_data );
		}

		/**
		 * Returns a scoped response object for a given request and its response data.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param APIAPI\Core\Request\Route_Request $request       Request object.
		 * @param array                             $response_data Response array containing keys
		 *                                                         'headers', 'body', and 'response'.
		 *                                                         Not necessarily all of these are
		 *                                                         included though.
		 * @return APIAPI\Core\Request\Route_Response Response object for the request.
		 */
		public function get_response_object( $request, $response_data ) {
			$route = $request->get_route_object();

			return $route->create_response_object( $response, $request->get_method() );
		}

		/**
		 * Returns the current mode in which to call the API.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string Mode identifier, or empty for the default mode.
		 */
		public function get_mode() {
			$config_key = $this->structure->get_config_key();

			if ( $this->config->isset( $config_key, 'mode' ) ) {
				return $this->config->get( $config_key, 'mode' );
			}

			return '';
		}

		/**
		 * Returns the identifier of the authenticator to use when calling the API.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string Authenticator identifier.
		 */
		public function get_authenticator() {
			$config_key = $this->structure->get_config_key();

			if ( $this->config->isset( $config_key, 'authenticator' ) ) {
				return $this->config->get( $config_key, 'authenticator' );
			}

			return $this->structure->get_authenticator();
		}

		/**
		 * Returns the authentication data passed to the authenticator.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return array Authentication data as `$key => $value` pairs. May be empty.
		 */
		public function get_authentication_data() {
			$config_key = $this->structure->get_config_key();

			if ( $this->config->isset( $config_key, 'authentication_data' ) ) {
				return $this->config->get( $config_key, 'authentication_data' );
			}

			return array();
		}

		/**
		 * Magic call method that proxies to the structure methods.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $method Method name.
		 * @param array  $args   Method arguments.
		 * @return mixed Result of the called structure method, or null if invalid method.
		 */
		public function __call( $method, $args ) {
			if ( is_callable( array( $this->structure, $method ) ) ) {
				return call_user_func_array( array( $this->structure, $method ), $args );
			}

			return null;
		}
	}

}
