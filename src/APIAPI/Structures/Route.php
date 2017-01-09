<?php
/**
 * API-API Route class
 *
 * @package APIAPI
 * @subpackage Structures
 * @since 1.0.0
 */

namespace awsmug\APIAPI\Structures;

use awsmug\APIAPI\Util;

if ( ! class_exists( 'awsmug\APIAPI\Structures\Route' ) ) {

	/**
	 * Route class for the API-API.
	 *
	 * Represents a specific route in an API structure.
	 *
	 * @since 1.0.0
	 */
	class Route {
		/**
		 * The route's base URI. May contain regular expressions.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var string
		 */
		private $uri;

		/**
		 * The API structure this route belongs to.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var awsmug\APIAPI\Structures\Structure
		 */
		private $structure;

		/**
		 * Array of primary parameters.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var array
		 */
		private $primary_params = array();

		/**
		 * Array of supported methods and their data.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var array
		 */
		private $data = array();

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string                             $uri       The route's base URI.
		 * @param array                              $data      Route data.
		 * @param awsmug\APIAPI\Structures\Structure $structure The parent API structure.
		 */
		public function __construct( $uri, $data, $structure ) {
			$this->uri = $uri;

			$this->data = $this->parse_data( $data );

			$this->structure = $structure;

			$this->set_primary_params();
		}

		/**
		 * Returns the base URI for this route.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string The base URI.
		 */
		public function get_base_uri() {
			return $this->uri;
		}

		/**
		 * Returns the description for what a specific method does.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $method Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 * @return string Description for what the method does at this route, or empty
		 *                string if method not supported.
		 */
		public function get_method_description( $method ) {
			if ( ! $this->is_method_supported( $method ) ) {
				return '';
			}

			return $this->data['methods'][ $method ]['description'];
		}

		/**
		 * Returns the available parameter information for a specific method.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $method Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 * @return array Array of method data, or empty array if method not supported.
		 */
		public function get_method_params( $method ) {
			if ( ! $this->is_method_supported( $method ) ) {
				return array();
			}

			return array_merge( $this->primary_params, $this->data['methods'][ $method ]['params'] );
		}

		/**
		 * Checks whether a specific method supports custom parameters.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $method Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 * @return bool Whether custom parameters are supported, or false if method not supported.
		 */
		public function method_supports_custom_params( $method ) {
			if ( ! $this->is_method_supported( $method ) ) {
				return false;
			}

			return $this->data['methods'][ $method ]['supports_custom_params'];
		}

		/**
		 * Checks whether a specific method is supported.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $method Either 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'.
		 * @return bool True if the method is supported, otherwise false.
		 */
		public function is_method_supported( $method ) {
			return isset( $this->data['methods'][ $method ] );
		}

		/**
		 * Sets the primary parameters depending on the route's base URI.
		 *
		 * Primary parameters are regular expression parts of the URI.
		 *
		 * @since 1.0.0
		 * @access private
		 */
		private function set_primary_params() {
			preg_match_all( '@\/\(\?P\<([A-Za-z_]+)\>\[(.+)\]\+\)@U', $this->uri, $matches );

			$this->primary_params = array();
			for ( $i = 0; $i < count( $matches[0] ); $i++ ) {
				$type = '\d' === $matches[2][ $i ] ? 'integer' : 'string';

				$description = '';
				$default     = null;
				if ( isset( $this->data['primary_params'][ $matches[1][ $i ] ] ) ) {
					$description = $this->data['primary_params'][ $matches[1][ $i ] ]['description'];
					$default     = $this->data['primary_params'][ $matches[1][ $i ] ]['default'];
				}

				$this->primary_params[ $matches[1][ $i ] ] = array(
					'required'    => true,
					'description' => $description,
					'type'        => $type,
					'enum'        => array(),
					'default'     => $default,
					'primary'     => true,
				);
			}
		}

		/**
		 * Parses route data.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param array $data Route data.
		 * @return array Parsed route data.
		 */
		private function parse_data( $data ) {
			$data = Util::parse_args( $data, array(
				'primary_params' => array(),
				'methods'        => array(),
			) );

			$data['primary_params'] = $this->parse_param_data( $data['primary_params'] );
			$data['methods']        = $this->parse_method_data( $data['methods'] );

			return $data;
		}

		/**
		 * Parses method data.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param array $method_data Method data.
		 * @return array Parsed method data.
		 */
		private function parse_method_data( $method_data ) {
			$method_data = array_intersect_key( $method_data, array_flip( array( 'GET', 'POST', 'PUT', 'PATCH', 'DELETE' ) ) );

			foreach ( $method_data as $method => &$data ) {
				$data = Util::parse_args( $data, array(
					'description'            => '',
					'params'                 => array(),
					'supports_custom_params' => false,
				), true );

				$data['params'] = $this->parse_param_data( $data['params'] );
			}

			return $method_data;
		}

		/**
		 * Parses param data.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param array $param_data Param data.
		 * @return array Parsed param data.
		 */
		private function parse_param_data( $param_data ) {
			foreach ( $param_data as $param => &$data ) {
				$data = Util::parse_args( $data, array(
					'required'    => false,
					'description' => '',
					'type'        => 'string',
					'enum'        => array(),
					'default'     => null,
				), true );
			}

			return $param_data;
		}
	}

}
