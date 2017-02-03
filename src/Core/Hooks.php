<?php
/**
 * API-API Hooks class
 *
 * @package APIAPICore
 * @since 1.0.0
 */

namespace APIAPI\Core;

if ( ! class_exists( 'APIAPI\Core\Hooks' ) ) {

	/**
	 * Hooks class for the API-API.
	 *
	 * @since 1.0.0
	 */
	class Hooks {
		/**
		 * Registered hook callbacks.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $hooks = array();

		/**
		 * Currently triggered hook names.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var array
		 */
		protected $triggered_hook_names = array();

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public function __construct() {
			// Empty constructor.
		}

		/**
		 * Attaches a hook callback.
		 *
		 * The returned hook object can be passed to `APIAPI\Core\Hooks::off()` to
		 * remove it again.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string   $hook_name Hook name.
		 * @param callable $callback  Hook callback.
		 * @param int      $priority  Optional. Hook priority. Default 10.
		 * @return APIAPI\Core\Hook Hook object.
		 */
		public function on( $hook_name, $callback, $priority = 10 ) {
			if ( ! isset( $this->hooks[ $hook_name ] ) ) {
				$this->hooks[ $hook_name ] = array();
			}

			$hook = new Hook( $this, $hook_name, $callback, $priority );

			$this->hooks[ $hook_name ][] = $hook;

			return $hook;
		}

		/**
		 * Removes a previously attached hook callback.
		 *
		 * The object to pass to this method must have been returned by `APIAPI\Core\Hooks::on()`.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param APIAPI\Core\Hook $hook Hook object.
		 */
		public function off( $hook ) {
			$name = $hook->get_name();

			if ( ! isset( $this->hooks[ $name ] ) ) {
				throw new Exception( sprintf( 'Invalid usage of hook object with hook %s.', $name ) );
			}

			$key = array_search( $hook, $this->hooks[ $name ], true );
			if ( false === $key ) {
				throw new Exception( sprintf( 'Invalid usage of hook object with hook %s.', $name ) );
			}

			array_splice( $this->hooks[ $name ], $key, 1 );

			if ( empty( $this->hooks[ $name ] ) ) {
				unset( $this->hooks[ $name ] );
			}
		}

		/**
		 * Triggers a hook.
		 *
		 * Any additional parameters passed to the method are passed to each hook callback.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $hook_name Hook name.
		 */
		public function trigger( $hook_name ) {
			if ( $this->is_hook_triggered( $hook_name ) ) {
				return;
			}

			if ( ! isset( $this->hooks[ $hook_name ] ) ) {
				return;
			}

			$hooks = $this->hooks[ $hook_name ];

			usort( $hooks, array( $this, 'sort_callback' ) );

			$args = array_slice( func_get_args(), 1 );

			array_push( $this->triggered_hook_names, $hook_name );

			foreach ( $hooks as $hook ) {
				$hook->execute( $args );
			}

			array_pop( $this->triggered_hook_names );
		}

		/**
		 * Checks whether a hook is currently triggered.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $hook_name Hook name.
		 * @return bool True if the hook is triggered, false otherwise.
		 */
		public function is_hook_triggered( $hook_name ) {
			return in_array( $hook_name, $this->triggered_hook_names, true );
		}

		/**
		 * Sort callback to sort hooks by priority.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param APIAPI\Core\Hook $hook1 First hook object.
		 * @param APIAPI\Core\Hook $hook2 Second hook object.
		 * @return int Comparator value.
		 */
		protected function sort_callback( $hook1, $hook2 ) {
			$prio1 = $hook1->get_priority();
			$prio2 = $hook2->get_priority();

			if ( $prio1 === $prio2 ) {
				return 0;
			}

			return $prio1 < $prio2 ? -1 : 1;
		}
	}

}
