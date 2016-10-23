<?php
/**
 * API-API Config class
 *
 * @package APIAPI
 * @subpackage Core
 * @since 1.0.0
 */

namespace awsmug\APIAPI;

if ( ! class_exists( 'awsmug\APIAPI\Config' ) ) :

/**
 * Config class for the API-API.
 *
 * @since 1.0.0
 */
class Config {
	/**
	 * Array of config parameters.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $params = array();

	/**
	 * Constructor.
	 *
	 * Allows to set the config parameters.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $params Optional. Associative array of config parameters with their values. Default empty.
	 */
	public function __construct( $params = null ) {
		if ( is_array( $params ) ) {
			$this->set_params( $params );
		}
	}

	/**
	 * Checks whether a specific parameter is set.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $param Name of the parameter.
	 * @return bool True if the parameter is set, false otherwise.
	 */
	public function isset( $param ) {
		return isset( $this->params[ $param ] );
	}

	/**
	 * Returns the value for a specific parameter.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $param Name of the parameter.
	 * @return mixed Value of the parameter, or null if it is not set.
	 */
	public function get( $param ) {
		if ( ! isset( $this->params[ $param ] ) ) {
			return null;
		}

		return $this->params[ $param ];
	}

	/**
	 * Sets a specific parameter to a given value.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $param Name of the parameter.
	 * @param mixed  $value New value for the parameter.
	 */
	public function set( $param, $value ) {
		$this->params[ $param ] = $value;
	}

	/**
	 * Unsets a specific parameter.
	 *
	 * It is not possible to unset default parameters.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $param Name of the parameter.
	 */
	public function unset( $param ) {
		if ( ! isset( $this->params[ $param ] ) ) {
			return;
		}

		// Do not allow removal of default parameters.
		if ( in_array( $param, array_keys( $this->get_defaults() ) ) ) {
			return;
		}

		unset( $this->params[ $param ] );
	}

	/**
	 * Sets multiple parameters with their values.
	 *
	 * If the parameters are set for the first time or if the $reset parameter is set to true,
	 * unprovided parameters will be filled with their default values.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $params Associative array of config parameters with their values.
	 * @param bool  $reset  Optional. Whether to reset all parameters to the specified ones. Default false.
	 */
	public function set_params( $params, $reset = false ) {
		if ( empty( $this->params ) || $reset ) {
			$this->params = Util::parse_args( $params, $this->get_defaults() );
		} else {
			$this->params = Util::parse_args( $this->params, $params );
		}
	}

	/**
	 * Returns all parameters with their values as an associative array.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Associative array of config parameters with their values.
	 */
	public function get_params() {
		return $this->params;
	}

	/**
	 * Returns the default parameters with their values.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return array Associative array of default config parameters with their values.
	 */
	private function get_defaults() {
		return array();
	}
}

endif;
