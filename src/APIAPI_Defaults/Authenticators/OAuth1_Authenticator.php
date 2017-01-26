<?php
/**
 * OAuth1_Authenticator class
 *
 * @package APIAPI_Defaults
 * @subpackage Authenticators
 * @since 1.0.0
 */

namespace awsmug\APIAPI_Defaults\Authenticators;

use awsmug\APIAPI\Authenticators\Authenticator;
use awsmug\APIAPI\Request\Request;
use awsmug\APIAPI\Request\Response;
use awsmug\APIAPI\Exception;
use awsmug\APIAPI\Authenticators\Authorization_Required_Exception;

if ( ! class_exists( 'awsmug\APIAPI_Defaults\Authenticators\OAuth1_Authenticator' ) ) {

	/**
	 * Authenticator class for OAuth 1.0 Authentication.
	 *
	 * @since 1.0.0
	 */
	class OAuth1_Authenticator extends Authenticator {
		/**
		 * Authenticates a request.
		 *
		 * This method does not yet actually authenticate the request with the server. It only sets
		 * the required values on the request object.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Request\Route_Request $request The request to send.
		 */
		public function authenticate_request( $request ) {
			$data = $this->parse_authentication_data( $request );

			foreach ( array( 'request_url', 'authorize_url', 'access_url', 'callback_url' ) as $url ) {
				if ( empty( $url ) ) {
					throw new Exception( sprintf( 'The request to %s could not be authenticated as one of the required URLs has not been provided.', $request->get_uri() ) );
				}
			}

			if ( empty( $data['consumer_key'] ) ) {
				throw new Exception( sprintf( 'The request to %s could not be authenticated as no consumer key has been provided.', $request->get_uri() ) );
			}

			if ( empty( $data['consumer_secret'] ) ) {
				throw new Exception( sprintf( 'The request to %s could not be authenticated as no consumer secret has been provided.', $request->get_uri() ) );
			}

			if ( empty( $data['apply_token_callback'] ) ) {
				throw new Exception( sprintf( 'The request to %s could not be authenticated as no callback function for applying the token has been provided.', $request->get_uri() ) );
			}

			$consumer_key             = $data['consumer_key'];
			$consumer_secret          = $data['consumer_secret'];
			$temporary_token          = isset( $data['temporary_token'] ) ? $data['temporary_token'] : '';
			$temporary_token_secret   = isset( $data['temporary_token_secret'] ) ? $data['temporary_token_secret'] : '';
			$temporary_token_verifier = isset( $data['temporary_token_verifier'] ) ? $data['temporary_token_verifier'] : '';
			$token                    = isset( $data['token'] ) ? $data['token'] : '';
			$token_secret             = isset( $data['token_secret'] ) ? $data['token_secret'] : '';

			if ( empty( $token ) || empty( $token_secret ) ) {
				if ( empty( $temporary_token ) || empty( $temporary_token_secret ) ) {
					list( $temporary_token, $temporary_token_secret ) = $this->get_temporary_credentials( $data['request_url'], $consumer_key, $consumer_secret, $data['callback_url'], 'POST' );
				}

				if ( empty( $temporary_token_verifier ) ) {
					$this->authorize_user( $data['authorize_url'], $temporary_token, $temporary_token_secret );
				}

				list( $token, $token_secret ) = $this->get_token_credentials( $data['access_url'], $consumer_key, $consumer_secret, $temporary_token, $temporary_token_secret, $temporary_token_verifier, 'POST' );

				call_user_func( $data['apply_token_callback'], $consumer_key, $consumer_secret, $token, $token_secret );
			}

			$url    = $request->get_uri();
			$method = $request->get_method();

			$protocol_params = $this->get_protocol_params( $consumer_key, array( 'oauth_token' => $token ) );

			$params_to_sign = $protocol_params;
			if ( 'GET' !== $method ) {
				$request_params = $request->get_params();
				if ( ! empty( $request_params ) ) {
					$params_to_sign = array_merge( $protocol_params, $request_params );
				}
			}

			$protocol_params['oauth_signature'] = $this->sign_params( $url, $params_to_sign, $consumer_secret, $token_secret, $method );

			$request->set_header( 'Authorization', $this->normalize_protocol_params( $protocol_params ) );
		}

		/**
		 * Checks whether a request is authenticated.
		 *
		 * This method does not check whether the request was actually authenticated with the server.
		 * It only checks whether authentication data has been properly set on it.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Request\Route_Request $request The request to check.
		 * @return bool True if the request is authenticated, otherwise false.
		 */
		public function is_authenticated( $request ) {
			$data = $this->parse_authentication_data( $request );

			$header_value = $request->get_header( 'Authorization' );
			if ( null === $header_value ) {
				return false;
			}

			return 0 === strpos( $header_value, 'OAuth ' );
		}

		/**
		 * Retrieves temporary credentials to use for authorization.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $request_url     The API's request URL that provides temporary credentials.
		 * @param string $consumer_key    The consumer key for the API.
		 * @param string $consumer_secret The consumer secret for the API.
		 * @param string $callback_url    The callback URL the authorize URL should redirect to.
		 * @param string $method          Optional. HTTP request method. Default 'POST'.
		 * @return array Array with the temporary token as first and temporary token secret as second
		 *               element.
		 */
		protected function get_temporary_credentials( $request_url, $consumer_key, $consumer_secret, $callback_url, $method = 'POST' ) {
			$protocol_params = $this->get_protocol_params( $consumer_key, array( 'oauth_callback' => $callback_url ) );
			$protocol_params['oauth_signature'] = $this->sign_params( $request_url, $protocol_params, $consumer_secret, '', $method );

			$request = new Request( $request_url, $method );
			$request->set_header( 'Authorization', $this->normalize_protocol_params( $protocol_params ) );

			$transporter = $this->get_default_transporter();

			$response = new Response( $transporter->send_request( $request ) );

			if ( 'true' !== $response->get_param( 'oauth_callback_confirmed' ) ) {
				throw new Exception( sprintf( 'The request to %s returned an invalid response.', $request_url ) );
			}

			return array( $response->get_param( 'oauth_token' ), $response->get_param( 'oauth_token_secret' ) );
		}

		/**
		 * Authorizes the user.
		 *
		 * This method will throw an `Authorization_Required_Exception` that must be caught and handled.
		 * See the docs for that exception class for more information.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $authorize_url          The API's authorize URL that asks the user to grant
		 *                                       access to the service.
		 * @param string $temporary_token        The temporary token.
		 * @param string $temporary_token_secret The temporary token secret.
		 */
		protected function authorize_user( $authorize_url, $temporary_token, $temporary_token_secret ) {
			$authorize_query_string = http_build_query( array( 'oauth_token' => $temporary_token ) );
			$authorize_url = $authorize_url . ( false !== strpos( $authorize_url, '?' ) ? '&' : '?' ) . $authorize_query_string;

			throw new Authorization_Required_Exception( 'Authorization is required.', 0, array(
				'authorize_url'          => $authorize_url,
				'temporary_token'        => $temporary_token,
				'temporary_token_secret' => $temporary_token_secret,
			) );
		}

		/**
		 * Retrieves token credentials to use for authentication.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $access_url               The API's access URL that provides token credentials.
		 * @param string $consumer_key             The consumer key for the API.
		 * @param string $consumer_secret          The consumer secret for the API.
		 * @param string $temporary_token          The temporary token.
		 * @param string $temporary_token_secret   The temporary token secret.
		 * @param string $temporary_token_verifier The temporary token verifier.
		 * @param string $method                   Optional. HTTP request method. Default 'POST'.
		 * @return array Array with the token as first and token secret as second element.
		 */
		protected function get_token_credentials( $access_url, $consumer_key, $consumer_secret, $temporary_token, $temporary_token_secret, $temporary_token_verifier, $method = 'POST' ) {
			$request_params = array(
				'oauth_verifier' => $temporary_token_verifier,
			);

			$protocol_params = $this->get_protocol_params( $consumer_key, array( 'oauth_token' => $temporary_token ) );
			$protocol_params['oauth_signature'] = $this->sign_params( $access_url, array_merge( $protocol_params, $request_params ), $consumer_secret, $temporary_token_secret, $method );

			$request = new Request( $access_url, $method );
			$request->set_header( 'Authorization', $this->normalize_protocol_params( $protocol_params ) );
			$request->set_params( $request_params );

			$transporter = $this->get_default_transporter();

			$response = new Response( $transporter->send_request( $request ) );

			if ( null === $response->get_param( 'oauth_token' ) ) {
				throw new Exception( sprintf( 'The request to %s returned an invalid response.', $access_url ) );
			}

			if ( null !== ( $error = $response->get_param( 'error' ) ) ) {
				throw new Exception( sprintf( 'The request to %1$s returned an error: %2$s', $access_url, $error ) );
			}

			return array( $response->get_param( 'oauth_token' ), $response->get_param( 'oauth_token_secret' ) );
		}

		/**
		 * Returns protocol parameters.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $consumer_key      The consumer key for the API.
		 * @param array  $additional_params Additional protocol parameters to merge.
		 * @return array Array of protocol parameters.
		 */
		protected function get_protocol_params( $consumer_key, $additional_params = array() ) {
			return array_merge( array(
				'oauth_consumer_key'     => $consumer_key,
				'oauth_nonce'            => $this->create_nonce(),
				'oauth_signature_method' => 'HMAC-SHA1',
				'oauth_timestamp'        => time(),
				'oauth_version'          => '1.0',
			), $additional_params );
		}

		/**
		 * Creates a random string.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @return string Nonce string.
		 */
		protected function create_nonce() {
			$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			return substr( str_shuffle( str_repeat( $pool, 5 ) ), 0, $length );
		}

		/**
		 * Signs parameters for a request.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $url             URL the request should be sent to.
		 * @param array  $params          Authorization parameters.
		 * @param string $consumer_secret Consumer secret.
		 * @param string $token_secret    Optional. Token secret. Default empty string.
		 * @param string $method          Optional. Request method. Default 'POST'.
		 * @return string Signing string to append to the parameters.
		 */
		protected function sign_params( $url, $params, $consumer_secret, $token_secret = '', $method = 'POST' ) {
			$base_string = rawurlencode( $method ) . '&';

			$parts = parse_url( $url );
			$normalized_url = ( isset( $parts['scheme'] ) ? $parts['scheme'] . ':' : '' ) . '//' . $parts['host'] . $parts['path'];

			$base_string .= rawurlencode( $normalized_url ) . '&';

			if ( isset( $parts['query'] ) ) {
				parse_str( $parts['query'], $query );
				$params = array_merge( $query, $params );
			}

			array_walk_recursive( $params, function( &$key, &$value ) {
				$key   = rawurlencode( rawurldecode( $key ) );
				$value = rawurlencode( rawurldecode( $key ) );
			});
			ksort( $params );

			$query_parts = array();
			foreach ( $authorization as $key => $value ) {
				$query_parts[] = $key . '=' . $value;
			}

			$base_string .= implode( '&', $query_parts );

			$key = rawurlencode( $consumer_secret );
			if ( ! empty( $token_secret ) ) {
				$key .= '&' . rawurlencode( $token_secret );
			}

			return base64_encode( hash_hmac( 'sha1', $base_string, $key, true ) );
		}

		/**
		 * Normalizes protocol parameters into an Authorization header string.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param array $protocol_params Array of protocol parameters.
		 * @return string Normalized header string.
		 */
		protected function normalize_protocol_params( $protocol_params ) {
			array_walk( $protocol_params, function( &$value, $key ) {
				$value = rawurlencode( $key ) . '="' . rawurlencode( $value ) . '"';
			});

			return 'OAuth ' . implode( ', ', $protocol_params );
		}

		/**
		 * Gets the default transporter object.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @return awsmug\APIAPI\Transporters\Transporter Default transporter object.
		 */
		protected function get_default_transporter() {
			//TODO: This breaks the dependency injection pattern.
			return apiapi_manager()->transporters()->get_default();
		}

		/**
		 * Sets the default authentication arguments.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function set_default_args() {
			$this->default_args = array(
				'request_url'              => '',
				'authorize_url'            => '',
				'access_url'               => '',
				'callback_url'             => '',
				'apply_token_callback'     => null,
				'consumer_key'             => '',
				'consumer_secret'          => '',
				'temporary_token'          => '',
				'temporary_token_secret'   => '',
				'temporary_token_verifier' => '',
				'token'                    => '',
				'token_secret'             => '',
			);
		}
	}

}
