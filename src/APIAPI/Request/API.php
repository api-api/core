<?php
/**
 * API-API class for a scoped external API
 *
 * @package APIAPI
 * @subpackage Request
 * @since 1.0.0
 */

namespace awsmug\APIAPI\Request;

use awsmug\APIAPI\Name_Trait;
use awsmug\APIAPI\Config_Trait;

if ( ! class_exists( 'awsmug\APIAPI\Request\API' ) ) {

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
		 * @var awsmug\APIAPI\Structures\Structure
		 */
		protected $structure;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Structures\Structure $structure The API structure object.
		 * @param awsmug\APIAPI\Config|array         $config    Optional. Configuration object or associative array. Default empty array.
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
		 * @return awsmug\APIAPI\Request\Request Request object for the route.
		 */
		public function get_request_object( $route_uri, $method = 'GET' ) {
			$route = $this->structure->get_route_object( $route_uri );

			$mode = $this->config->isset( 'mode' ) ? $this->config->get( 'mode' ) : '';
			$authenticator = $this->config->isset( 'authenticator' ) ? $this->config->get( 'authenticator' ) : $this->structure->get_authenticator();
			$authentication_data = $this->config->isset( 'authentication_data' ) ? $this->config->get( 'authentication_data' ) : array();

			return $route->create_request_object( $route_uri, $method, $mode, $authenticator, $authentication_data );
		}

		/**
		 * Returns a scoped response object for a given request and its response data.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Request\Request $request       Request object.
		 * @param array                         $response_data Response array containing keys
		 *                                                     'headers', 'body', and 'response'.
		 *                                                     Not necessarily all of these are
		 *                                                     included though.
		 * @return awsmug\APIAPI\Request\Response Response object for the request.
		 */
		public function get_response_object( $request, $response_data ) {
			$route = $request->get_route_object();

			return $route->create_response_object( $response, $request->get_method() );
		}
	}

}
