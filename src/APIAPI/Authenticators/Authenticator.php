<?php
/**
 * API-API Authenticator class
 *
 * @package APIAPI
 * @subpackage Authenticators
 * @since 1.0.0
 */

namespace awsmug\APIAPI\Authenticators;

use awsmug\APIAPI\Name_Trait;

if ( ! class_exists( 'awsmug\APIAPI\Authenticators\Authenticator' ) ) {

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
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Slug of the instance.
		 */
		public function __construct( $name ) {
			$this->set_name( $name );
		}
	}

}
