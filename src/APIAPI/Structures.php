<?php
/**
 * API-API Structures class
 *
 * @package APIAPI
 * @subpackage Core
 * @since 1.0.0
 */

namespace awsmug\APIAPI;

if ( ! class_exists( 'awsmug\APIAPI\Structures' ) ) {

	/**
	 * Structures class for the API-API.
	 *
	 * Manages structures.
	 *
	 * @since 1.0.0
	 */
	class Structures extends Container {
		/**
		 * Registers a structure.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string                         $name      Unique slug for the structure.
		 * @param awsmug\APIAPI\Structure|string $structure Structure class instance or class name.
		 */
		public function register( $name, $structure ) {
			parent::register( $name, $structure );
		}

		/**
		 * Unregisters a structure.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Unique slug of the structure.
		 */
		public function unregister( $name ) {
			parent::unregister( $name );
		}

		/**
		 * Returns a specific structure.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Unique slug of the structure.
		 * @return awsmug\APIAPI\Structure|null The structure object, or null if it does not exist.
		 */
		public function get( $name ) {
			return parent::get( $name );
		}

		/**
		 * Checks whether a specific structure is registered.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Unique slug of the structure.
		 * @return bool True if the structure is registered, false otherwise.
		 */
		public function is_registered( $name ) {
			return parent::is_registered( $name );
		}

		/**
		 * Returns the type of the modules in this container.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @return string Type of the modules.
		 */
		protected function get_type() {
			return 'structure';
		}

		/**
		 * Returns the name of the class all modules must inherit.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @return string Name of the base module class.
		 */
		protected function get_module_class_name() {
			return 'awsmug\APIAPI\Structures\Structure';
		}
	}

}
