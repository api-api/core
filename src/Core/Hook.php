<?php
/**
 * API-API Hook class
 *
 * @package APIAPICore
 * @since 1.0.0
 */

namespace APIAPI\Core;

if ( ! class_exists( 'APIAPI\Core\Hook' ) ) {

	/**
	 * Hook class for the API-API.
	 *
	 * Represents a single hook callback.
	 *
	 * @since 1.0.0
	 */
	class Hook {
		/**
		 * Hook name.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $name = '';

		/**
		 * Hook callback.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var callable
		 */
		protected $callback = null;

		/**
		 * Hook priority.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var int
		 */
		protected $priority = 10;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param \APIAPI\Core\Hooks $manager  Hooks manager instance.
		 * @param string              $name     Hook name.
		 * @param callable            $callback Hook callback.
		 * @param int                 $priority Optional. Hook priority. Default 10.
		 */
		public function __construct( $manager, $name, $callback, $priority = 10 ) {
			$this->manager = $manager;

			$this->name     = $name;
			$this->callback = $callback;
			$this->priority = $priority;
		}

		/**
		 * Executes the hook callback.
		 *
		 * The hook object itself and any hook parameters are passed to the callback.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param array Parameters to pass to the callback.
		 */
		public function execute( $args ) {
			if ( ! $this->manager->is_hook_triggered( $this->name ) ) {
				$callback = $this->get_callback_string();

				throw new Exception( sprintf( 'Invalid manual execution of hook %s.', $callback ) );
			}

			array_unshift( $args, $this );

			call_user_func_array( $this->callback, $args );
		}

		/**
		 * Returns the hook name.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string Hook name.
		 */
		public function get_name() {
			return $this->name;
		}

		/**
		 * Returns the hook priority.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return int Hook priority.
		 */
		public function get_priority() {
			return $this->priority;
		}

		/**
		 * Removes this callback so that it is not executed again.
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public function remove() {
			$this->manager->off( $this );
		}

		/**
		 * Returns a string representation of the hook callback.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @return string Representation of the callback.
		 */
		protected function get_callback_string() {
			$callback = $this->callback;
			if ( is_array( $callback ) ) {
				if ( is_object( $callback[0] ) ) {
					$callback = get_class( $callback[0] ) . '::' . $callback[1] . '()';
				} else {
					$callback = $callback[0] . '::' . $callback[1] . '()';
				}
			} else {
				$callback .= '()';
			}

			return $callback;
		}
	}

}
