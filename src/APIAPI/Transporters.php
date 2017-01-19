<?php
/**
 * API-API Transporters class
 *
 * @package APIAPI
 * @subpackage Core
 * @since 1.0.0
 */

namespace awsmug\APIAPI;

if ( ! class_exists( 'awsmug\APIAPI\Transporters' ) ) {

	/**
	 * Transporters class for the API-API.
	 *
	 * Manages transporters.
	 *
	 * @since 1.0.0
	 */
	class Transporters extends Container {
		/**
		 * Registers a transporter.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string                           $name        Unique slug for the transporter.
		 * @param awsmug\APIAPI\Transporters\Transporter|string $transporter Transporter class instance or class name.
		 */
		public function register( $name, $transporter ) {
			parent::register( $name, $transporter );
		}

		/**
		 * Unregisters a transporter.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Unique slug of the transporter.
		 */
		public function unregister( $name ) {
			parent::unregister( $name );
		}

		/**
		 * Returns a specific transporter.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Unique slug of the transporter.
		 * @return awsmug\APIAPI\Transporters\Transporter|null The transporter object, or null if it does not exist.
		 */
		public function get( $name ) {
			return parent::get( $name );
		}

		/**
		 * Checks whether a specific transporter is registered.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Unique slug of the transporter.
		 * @return bool True if the transporter is registered, false otherwise.
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
			return 'transporter';
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
			return 'awsmug\APIAPI\Transporters\Transporter';
		}
	}

}
