<?php
/**
 * API-API Authenticator class
 *
 * @package APIAPICore
 * @subpackage Authenticators
 * @since 1.0.0
 */

namespace APIAPI\Core\Authenticators;

use APIAPI\Core\Util;
use APIAPI\Core\Name_Trait;

if ( ! class_exists( 'APIAPI\Core\Authenticators\Authenticator' ) ) {

	/**
	 * Authenticator class for the API-API.
	 *
	 * Represents a specific authenticator.
	 *
	 * @since 1.0.0
	 */
	abstract class Authenticator implements Authenticator_Interface {
		use Name_Trait;

		/**
		 * Default authentication arguments.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $default_args = array();

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Slug of the instance.
		 */
		public function __construct( $name ) {
			$this->set_name( $name );
			$this->set_default_args();
		}

		/**
		 * Parses request authentication data.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param APIAPI\Core\Request\Route_Request $request The request to send.
		 * @return array Parsed authentication data for the request.
		 */
		protected function parse_authentication_data( $request ) {
			return Util::parse_args( $request->get_authentication_data(), $this->default_args );
		}

		/**
		 * Sets the default authentication arguments.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected abstract function set_default_args();
	}

}