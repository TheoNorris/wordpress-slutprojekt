<?php
/**
 * The filter url builder class.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes
 * @author     wptools.io
 */

/**
 * WCAPF_URL_Builder class.
 *
 * @since 4.0.0
 */
class WCAPF_URL_Builder {

	/**
	 * Registered filter keys.
	 *
	 * @var array
	 */
	private $filter_keys;

	/**
	 * The filter key that for building the URL.
	 *
	 * @var string
	 */
	private $filter_key;

	/**
	 * Determines if the URL should allow multiple values for the filter key.
	 *
	 * @var bool
	 */
	private $enable_multiple;

	/**
	 * The base URL.
	 *
	 * @var string
	 */
	private $base_url;

	/**
	 * The keys that will be excluded from the url parameters.
	 *
	 * @var array
	 */
	private $excluded_keys;

	/**
	 * The query variables.
	 *
	 * @var array
	 */
	private $query_vars;

	public function __construct( $filter_key = '', $enable_multiple = false ) {
		$_filter_keys = WCAPF_Helper::wcapf_option( 'filter_keys' );

		if ( ! is_array( $_filter_keys ) ) {
			$_filter_keys = array();
		}

		$this->filter_keys     = $_filter_keys;
		$this->filter_key      = $filter_key;
		$this->enable_multiple = $enable_multiple;
		$this->base_url        = WCAPF_Helper::wcapf_option( 'base_url' );
		$this->excluded_keys   = apply_filters( 'wcapf_url_vars_to_exclude', array( 'product-page' ) );

		$this->query_vars = $this->set_query();
	}

	/**
	 * Sets the query vars after sorting.
	 *
	 * @return array
	 */
	private function set_query() {
		$vars = $_GET;

		if ( $this->excluded_keys ) {
			// @source https://stackoverflow.com/a/28836669
			$vars = array_diff_key( $vars, array_flip( $this->excluded_keys ) );
		}

		// We need to preserve the order of active filter key.
		if ( $this->filter_key && ! isset( $vars[ $this->filter_key ] ) ) {
			$vars[ $this->filter_key ] = array();
		}

		// Sort the filter keys in query.
		$sorted = array();

		foreach ( $this->filter_keys as $filter_key ) {
			if ( ! isset( $vars[ $filter_key ] ) ) {
				continue;
			}

			$sorted[ $filter_key ] = $vars[ $filter_key ];
			unset( $vars[ $filter_key ] );
		}

		return array_merge( $sorted, $vars );
	}

	/**
	 * Gets the filter url.
	 *
	 * @param $value
	 * @param $is_active
	 *
	 * @return string
	 */
	public function get_filter_url( $value, $is_active ) {
		// If no value is provided then we need to remove the filter key from url.
		if ( ! strlen( $value ) ) {
			return $this->url_without_current_filter();
		}

		$values = $this->active_values();

		if ( $this->enable_multiple ) {
			if ( $is_active ) {
				// Remove.
				$values = array_diff( $values, array( $value ) );

				if ( ! $values ) {
					return $this->url_without_current_filter();
				}
			} else {
				// Add.
				$values[] = $value;
			}
		} else {
			if ( $is_active ) {
				return $this->url_without_current_filter();
			}

			$values = array( $value );
		}

		$vars = $this->query_vars;

		// Sort values in ascending order.
		sort( $values );

		$vars[ $this->filter_key ] = implode( ',', $values );

		return add_query_arg( $vars, $this->base_url );
	}

	/**
	 * @return string
	 */
	private function url_without_current_filter() {
		$vars = $this->query_vars;

		if ( isset( $vars[ $this->filter_key ] ) ) {
			unset( $vars[ $this->filter_key ] );
		}

		return add_query_arg( $vars, $this->base_url );
	}

	private function active_values() {
		$value = isset( $_GET[ $this->filter_key ] ) ? $_GET[ $this->filter_key ] : '';

		return strlen( $value ) ? explode( ',', $value ) : array();
	}

	/**
	 * @return string
	 */
	public function get_filter_url_with_placeholder() {
		$vars = $this->query_vars;

		$vars[ $this->filter_key ] = '%s';

		return add_query_arg( $vars, $this->base_url );
	}

	/**
	 * @return string
	 */
	public function get_range_url() {
		$vars = $this->query_vars;

		$vars[ $this->filter_key ] = '%1s~%2s';

		return add_query_arg( $vars, $this->base_url );
	}

	/**
	 * URL for clearing the current filter only.
	 *
	 * @return string
	 */
	public function get_clear_filter_url() {
		$vars = $this->query_vars;

		unset( $vars[ $this->filter_key ] );

		return add_query_arg( $vars, $this->base_url );
	}

	/**
	 * Reset filters url.
	 *
	 * @return string
	 */
	public function get_reset_url() {
		$vars = $this->query_vars;

		$filter_keys = array_keys( WCAPF_Helper::get_active_filters_data() );

		foreach ( $filter_keys as $filter_key ) {
			if ( array_key_exists( $filter_key, $vars ) ) {
				unset( $vars[ $filter_key ] );
			}
		}

		return add_query_arg( $vars, $this->base_url );
	}

}
