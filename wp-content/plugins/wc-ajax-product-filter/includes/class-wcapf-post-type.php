<?php
/**
 * The filter post type class.
 *
 * @since      3.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes
 * @author     wptools.io
 */

/**
 * WCAPF_Post_Type class.
 *
 * @since 3.0.0
 */
class WCAPF_Post_Type {

	/**
	 * Constructor.
	 */
	private function __construct() {
	}

	/**
	 * Returns an instance of this class.
	 *
	 * @return WCAPF_Post_Type
	 */
	public static function instance() {
		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance ) {
			$instance = new WCAPF_Post_Type();
			$instance->init_hooks();
		}

		return $instance;
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'register_post_types' ) );
	}

	/**
	 * Register a custom post type called "wcapf-filter".
	 */
	public function register_post_types() {
		$labels = array(
			'name'          => _x( 'Filters', 'Post type general name', 'wc-ajax-product-filter' ),
			'singular_name' => _x( 'Filter', 'Post type singular name', 'wc-ajax-product-filter' ),
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
		);

		register_post_type( 'wcapf-filter', $args );

		$labels = array(
			'name'          => _x( 'Filter Forms', 'Post type general name', 'wc-ajax-product-filter' ),
			'singular_name' => _x( 'Filter Form', 'Post type singular name', 'wc-ajax-product-filter' ),
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
		);

		register_post_type( 'wcapf-form', $args );
	}

}

WCAPF_Post_Type::instance();
