<?php
/**
 * WordPress_User_Meta_Storage class
 *
 * @package APIAPIDefaults
 * @subpackage Storages
 * @since 1.0.0
 */

namespace APIAPI\Defaults\Storages;

use APIAPI\Core\Storages\Array_Storage;

if ( ! class_exists( 'APIAPI\Defaults\Storages\WordPress_User_Meta_Storage' ) ) {

	/**
	 * Storage class using WordPress user metadata.
	 *
	 * @since 1.0.0
	 */
	class WordPress_User_Meta_Storage extends Array_Storage {
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
			$data = get_user_meta( get_current_user_id(), $basename, true );
			if ( empty( $data ) ) {
				return array();
			}

			return $data;
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
			update_user_meta( get_current_user_id(), $basename, $data );
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
			delete_user_meta( get_current_user_id(), $basename );
		}
	}

}
