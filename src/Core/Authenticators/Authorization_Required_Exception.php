<?php
/**
 * API-API Exception class for required authorization
 *
 * @package APIAPICore
 * @subpackage Authenticators
 * @since 1.0.0
 */

namespace APIAPI\Core\Authenticators;

use APIAPI\Core\Exception;

if ( ! class_exists( 'APIAPI\Core\Authenticators\Authorization_Required_Exception' ) ) {

	/**
	 * Required authorization exception class for the API-API.
	 *
	 * When this exception is thrown, the client should catch it. It must call the methods
	 * `Authorization_Required_Exception::get_temporary_token()` and
	 * `Authorization_Required_Exception::get_temporary_token_secret()` and store the two
	 * values temporarily. After storing them, the user should be redirected to the URL available
	 * through `Authorization_Required_Exception::get_authorize()`.
	 *
	 * After redirecting the user to that URL, they will be automatically redirected back to the
	 * callback URL specified in the respective API config. The GET parameters `oauth_token` and
	 * `oauth_verifier` will be passed to it as well.
	 *
	 * It must be verified that the passed `oauth_token` is identical to the temporary token
	 * that has been stored before. Then the following parameters need to be added to the
	 * respective API's config object:
	 *
	 * The `oauth_token` GET parameter should be passed as `temporary_token`.
	 * The `oauth_verifier` GET parameter should be passed as `temporary_token_verifier`.
	 * The temporary token secret stored before should be passed as `temporary_token_secret`.
	 *
	 * @since 1.0.0
	 */
	class Authorization_Required_Exception extends Exception {
		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $message Optional. Message to print out. Default empty.
		 * @param int    $code    Optional. Code for the exception. Default 0.
		 * @param array  $data    {
		 *     Additional data related to the exception. Default null.
		 *
		 *     @type string $authorize              URL for authorization.
		 *     @type string $temporary_token        Temporary OAuth token.
		 *     @type string $temporary_token_secret Temporary OAuth token secret.
		 * }
		 */
		public function __construct( $message = '', $code = 0, $data = null ) {
			parent::__construct( $message, $code );

			$this->data = $data;
		}

		/**
		 * Returns the authorize URL.
		 *
		 * The current user should be redirected to this URL.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string URL for authorization.
		 */
		public function get_authorize() {
			return $this->data['authorize'];
		}

		/**
		 * Returns the temporary token.
		 *
		 * This should be stored and added to the API config object.
		 * The `oauth_token` passed to the callback URL must match this one.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string URL for authorization.
		 */
		public function get_temporary_token() {
			return $this->data['temporary_token'];
		}

		/**
		 * Returns the temporary token secret.
		 *
		 * This should be stored and added to the API config object.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string URL for authorization.
		 */
		public function get_temporary_token_secret() {
			return $this->data['temporary_token_secret'];
		}
	}

}
