<?php
/**
 * API-API Authenticators class
 *
 * @package APIAPICore
 * @since 1.0.0
 */

namespace APIAPI\Core;

if ( ! class_exists( 'APIAPI\Core\Authenticators' ) ) {

	/**
	 * Authenticators class for the API-API.
	 *
	 * Manages authenticators.
	 *
	 * @since 1.0.0
	 */
	class Authenticators extends Container {
		/**
		 * Registers an authenticator.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string                                            $name          Unique slug for the authenticator.
		 * @param APIAPI\Core\Authenticators\Authenticator|string $authenticator Authenticator class instance or class name.
		 */
		public function register( $name, $authenticator ) {
			parent::register( $name, $authenticator );
		}

		/**
		 * Unregisters an authenticator.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Unique slug of the authenticator.
		 */
		public function unregister( $name ) {
			parent::unregister( $name );
		}

		/**
		 * Returns a specific authenticator.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Unique slug of the authenticator.
		 * @return APIAPI\Core\Authenticators\Authenticator|null The authenticator object, or null if it does not exist.
		 */
		public function get( $name ) {
			return parent::get( $name );
		}

		/**
		 * Checks whether a specific authenticator is registered.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Unique slug of the authenticator.
		 * @return bool True if the authenticator is registered, false otherwise.
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
			return 'authenticator';
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
			return 'APIAPI\Core\Authenticators\Authenticator';
		}
	}

}
