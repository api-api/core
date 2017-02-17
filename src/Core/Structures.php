<?php
/**
 * API-API Structures class
 *
 * @package APIAPICore
 * @since 1.0.0
 */

namespace APIAPI\Core;

if ( ! class_exists( 'APIAPI\Core\Structures' ) ) {

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
		 * @param string                                    $name      Unique slug for the structure.
		 * @param APIAPI\Core\Structures\Structure|string $structure Structure class instance or class name.
		 */
		public function register( $name, $structure ) {
			$args = func_get_args();
			call_user_func_array( array( 'parent', __FUNCTION__ ), $args );
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
		 * @return APIAPI\Core\Structures\Structure|null The structure object, or null if it does not exist.
		 */
		public function get( $name ) {
			return parent::get( $name );
		}

		/**
		 * Returns all registered structures.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return array Array of structure objects.
		 */
		public function get_all() {
			return parent::get_all();
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
			return 'APIAPI\Core\Structures\Structure';
		}
	}

}
