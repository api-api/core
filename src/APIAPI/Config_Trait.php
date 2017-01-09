<?php
/**
 * API-API Config trait
 *
 * @package APIAPI
 * @subpackage Core
 * @since 1.0.0
 */

namespace awsmug\APIAPI;

if ( ! trait_exists( 'awsmug\APIAPI\Config_Trait' ) ) {

	/**
	 * Config trait for the API-API.
	 *
	 * @since 1.0.0
	 */
	trait Config_Trait {
		/**
		 * Configuration object.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var awsmug\APIAPI\Config
		 */
		protected $config;

		/**
		 * Returns or sets the configuration object.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param awsmug\APIAPI\Config|array|null $config Optional. Configuration object or associative array. Default null.
		 * @return awsmug\APIAPI\Config Configuration object for the manager.
		 */
		public function config( $config = null ) {
			if ( ! is_null( $config ) ) {
				if ( is_a( $config, 'awsmug\APIAPI\Config' ) ) {
					$this->config = $config;
				} else {
					$this->config = new Config( $config );
				}
			}

			return $this->config;
		}
	}

}
