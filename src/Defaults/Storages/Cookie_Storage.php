<?php
/**
 * Cookie_Storage class
 *
 * @package APIAPIDefaults
 * @subpackage Storages
 * @since 1.0.0
 */

namespace APIAPI\Defaults\Storages;

use APIAPI\Core\Storages\Storage;

if ( ! class_exists( 'APIAPI\Defaults\Storages\Cookie_Storage' ) ) {

	/**
	 * Storage class using cookies.
	 *
	 * @since 1.0.0
	 */
	class Cookie_Storage extends Storage {
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
		public function store( $basename, $group, $key, $value ) {
			$cookie_name = $basename . '[' . $group . '][' . $key . ']';

			setcookie( $cookie_name, $value );
		}

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
		public function store_multi( $basename, $group, $keys_and_values ) {
			foreach ( $keys_and_values as $key => $value ) {
				$this->store( $basename, $group, $key, $value );
			}
		}

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
		public function retrieve( $basename, $group, $key ) {
			if ( ! isset( $_COOKIE[ $basename ] ) ) {
				return null;
			}

			$data = $_COOKIE[ $basename ];

			if ( ! isset( $data[ $group ] ) ) {
				return null;
			}

			if ( ! isset( $data[ $group ][ $key ] ) ) {
				return null;
			}

			return $data[ $group ][ $key ];
		}

		/**
		 * Retrieves multiple values.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $basename The basename under which to store.
		 * @param string $group    The group identifier of the group in which to store.
		 * @param array  $keys     The keys to retrieve their values.
		 * @return array Associative array of `$key => $value`. The $value might is null, if
		 *               none is stored.
		 */
		public function retrieve_multi( $basename, $group, $keys ) {
			if ( ! isset( $_COOKIE[ $basename ] ) ) {
				return array_fill_keys( $keys, null );
			}

			$data = $_COOKIE[ $basename ];

			if ( ! isset( $data[ $group ] ) ) {
				return array_fill_keys( $keys, null );
			}

			$values = array();
			foreach ( $keys as $key ) {
				if ( ! isset( $data[ $group ][ $key ] ) ) {
					$values[ $key ] = null;
					continue;
				}

				$values[ $key ] = $data[ $group ][ $key ];
			}

			return $values;
		}

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
		public function delete( $basename, $group, $key ) {
			$cookie_name = $basename . '[' . $group . '][' . $key . ']';

			setcookie( $cookie_name, '', time() - 3600 );
		}

		/**
		 * Deletes multiple values.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $basename The basename under which to store.
		 * @param string $group    The group identifier of the group in which to store.
		 * @param array  $keys     The keys to delete their values.
		 */
		public function delete_multi( $basename, $group, $keys ) {
			foreach ( $keys as $key ) {
				$this->delete( $basename, $group, $key );
			}
		}
	}

}
