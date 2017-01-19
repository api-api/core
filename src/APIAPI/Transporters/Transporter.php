<?php
/**
 * API-API Transporter class
 *
 * @package APIAPI
 * @subpackage Transporters
 * @since 1.0.0
 */

namespace awsmug\APIAPI\Transporters;

use awsmug\APIAPI\Name_Trait;

if ( ! class_exists( 'awsmug\APIAPI\Transporters\Transporter' ) ) {

	/**
	 * Transporter class for the API-API.
	 *
	 * Represents a specific transporter method.
	 *
	 * @since 1.0.0
	 */
	abstract class Transporter implements Transporter_Interface {
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
