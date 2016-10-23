<?php
/**
 * API-API Container class
 *
 * @package APIAPI
 * @subpackage Core
 * @since 1.0.0
 */

namespace awsmug\APIAPI;

if ( ! class_exists( 'awsmug\APIAPI\Container' ) ) {

	/**
	 * Container class for the API-API.
	 *
	 * Manages modules of a certain class type.
	 *
	 * @since 1.0.0
	 */
	abstract class Container {
		/**
		 * The type of the modules in this container.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $type;

		/**
		 * The name of the class all modules must inherit.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $module_class_name;

		/**
		 * The registered modules.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $modules = array();

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public function __construct() {
			$this->type              = $this->get_type();
			$this->module_class_name = $this->get_module_class_name();
		}

		/**
		 * Registers a module.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string        $name   Unique slug for the module.
		 * @param object|string $module Module class instance or class name.
		 */
		public function register( $name, $module ) {
			if ( is_string( $module ) ) {
				if ( ! is_subclass_of( $module, $this->module_class_name ) ) {
					throw new Exception( sprintf( 'The %1$s %2$s must have a subclass of %3$s.', $this->type, $name, $this->module_class_name ) );
				}

				$module = new $module();
			} elseif ( ! is_a( $module, $this->module_class_name ) ) {
				throw new Exception( sprintf( 'The %1$s %2$s must have a subclass of %3$s.', $this->type, $name, $this->module_class_name ) );
			}

			$this->modules[ $name ] = $module;
		}

		/**
		 * Unregisters a module.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Unique slug of the module.
		 */
		public function unregister( $name ) {
			if ( ! isset( $this->modules[ $name ] ) ) {
				return;
			}

			unset( $this->modules[ $name ] );
		}

		/**
		 * Returns a specific module.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Unique slug of the module.
		 * @return object|null The module object, or null if it does not exist.
		 */
		public function get( $name ) {
			if ( ! isset( $this->modules[ $name ] ) ) {
				return null;
			}

			return $this->modules[ $name ];
		}

		/**
		 * Checks whether a specific module is registered.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $name Unique slug of the module.
		 * @return bool True if the module is registered, false otherwise.
		 */
		public function is_registered( $name ) {
			return isset( $this->modules[ $name ] );
		}

		/**
		 * Returns the type of the modules in this container.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @return string Type of the modules.
		 */
		protected abstract function get_type();

		/**
		 * Returns the name of the class all modules must inherit.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @return string Name of the base module class.
		 */
		protected abstract function get_module_class_name();
	}

}
