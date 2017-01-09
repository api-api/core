<?php
/**
 * API-API Name trait
 *
 * @package APIAPI
 * @subpackage Core
 * @since 1.0.0
 */

namespace awsmug\APIAPI;

if ( ! trait_exists( 'awsmug\APIAPI\Name_Trait' ) ) {

	/**
	 * Name trait for the API-API.
	 *
	 * @since 1.0.0
	 */
	trait Name_Trait {
		/**
		 * Slug of the instance.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $name;

		/**
		 * Sets the slug of the instance.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $name Slug of the instance.
		 */
		protected function set_name( $name ) {
			$this->name = $name;
		}

		/**
		 * Returns the slug of the instance.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string Slug of the instance.
		 */
		public function get_name() {
			return $this->name;
		}
	}

}
