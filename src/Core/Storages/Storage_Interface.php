<?php
/**
 * API-API Storage interface
 *
 * @package APIAPICore
 * @subpackage Storages
 * @since 1.0.0
 */

namespace APIAPI\Core\Storages;

if ( ! interface_exists( 'APIAPI\Core\Storages\Storage_Interface' ) ) {

	/**
	 * Storage interface for the API-API.
	 *
	 * Represents a specific storage. Only scalar values can be stored.
	 *
	 * @since 1.0.0
	 */
	interface Storage_Interface {
		/**
		 * Stores a single value.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $basename The basename under which to store.
		 * @param string $group    The group identifier of the group in which to store.
		 * @param string $key      The key to store a value for.
		 * @param scalar $value    The value to store.
		 */
		public function set( $basename, $group, $key, $value );

		/**
		 * Stores multiple values.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $basename        The basename under which to store.
		 * @param string $group           The group identifier of the group in which to store.
		 * @param array  $keys_and_values Associative array of `$key => $value` pairs.
		 */
		public function set_multi( $basename, $group, $keys_and_values );

		/**
		 * Retrieves a single value.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $basename The basename under which to store.
		 * @param string $group    The group identifier of the group in which to store.
		 * @param string $key      The key to retrieve its value.
		 * @return scalar|null The value, or null if not stored.
		 */
		public function get( $basename, $group, $key );

		/**
		 * Retrieves multiple values.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $basename The basename under which to store.
		 * @param string $group    The group identifier of the group in which to store.
		 * @param array  $keys     Array of keys.
		 * @return array Associative array of `$key => $value`. The $value might is null, if
		 *               none is stored.
		 */
		public function get_multi( $basename, $group, $keys );

		/**
		 * Deletes a single value.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $basename The basename under which to store.
		 * @param string $group    The group identifier of the group in which to store.
		 * @param string $key      The key to delete its value.
		 */
		public function delete( $basename, $group, $key );

		/**
		 * Deletes multiple values.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $basename The basename under which to store.
		 * @param string $group    The group identifier of the group in which to store.
		 * @param array  $keys     Array of keys.
		 */
		public function delete_multi( $basename, $group, $keys );
	}

}