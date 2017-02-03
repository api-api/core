<?php
/**
 * API-API Util class
 *
 * @package APIAPICore
 * @since 1.0.0
 */

namespace APIAPI\Core;

if ( ! class_exists( 'APIAPI\Core\Util' ) ) {

	/**
	 * Utility class with static methods.
	 *
	 * @since 1.0.0
	 */
	class Util {
		/**
		 * Parses an object or query string into an array of arguments, optionally filled with defaults.
		 *
		 * @since 1.0.0
		 * @access public
		 * @static
		 *
		 * @param array|string|object $args     Input to parse.
		 * @param array               $defaults Optional. Array of defaults to fill missing arguments. Default none.
		 * @param bool                $strict   Optional. Whether to only allow arguments contained in the defaults.
		 *                                      Default false.
		 * @return array Array of arguments.
		 */
		public static function parse_args( $args, $defaults = null, $strict = false ) {
			if ( is_object( $args ) ) {
				$result = get_object_vars( $args );
			} elseif ( is_string( $args ) ) {
				parse_str( $args, $defaults );
			} else {
				$result = $args;
			}

			if ( is_array( $defaults ) ) {
				$result = array_merge( $defaults, $result );

				if ( $strict ) {
					$result = array_intersect_key( $result, $defaults );
				}
			}

			return $result;
		}
	}

}
