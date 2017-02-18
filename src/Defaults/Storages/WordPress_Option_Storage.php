<?php
/**
 * WordPress_Option_Storage class
 *
 * @package APIAPIDefaults
 * @subpackage Storages
 * @since 1.0.0
 */

namespace APIAPI\Defaults\Storages;

use APIAPI\Core\Storages\Array_Storage;

if ( ! class_exists( 'APIAPI\Defaults\Storages\WordPress_Option_Storage' ) ) {

	/**
	 * Storage class using WordPress options.
	 *
	 * @since 1.0.0
	 */
	class WordPress_Option_Storage extends Array_Storage {
		/**
		 * Gets the array values are stored in.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $basename The basename under which to store.
		 * @return array Array with stored data.
		 */
		protected function get_array( $basename ) {
			return get_option( $basename, array() );
		}

		/**
		 * Updates the array values are stored in.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $basename The basename under which to store.
		 * @param array  $data     Array with updated data.
		 */
		protected function update_array( $basename, $data ) {
			update_option( $basename, $data );
		}

		/**
		 * Deletes the array values are stored in.
		 *
		 * @since 1.0.0
		 * @access protected
		 *
		 * @param string $basename The basename under which to store.
		 */
		protected function delete_array( $basename ) {
			delete_option( $basename );
		}
	}

}
