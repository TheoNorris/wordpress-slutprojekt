<?php
/**
 * Active filters shortcode.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/shortcodes
 * @author     wptools.io
 */

/**
 * WCAPF_Active_Filters_Shortcode class.
 *
 * @since 4.0.0
 */
class WCAPF_Active_Filters_Shortcode {

	/**
	 * Constructor.
	 */
	private function __construct() {
	}

	public function shortcode_output( $attrs = array() ) {
		$a = shortcode_atts( array(
			'title'                => __( 'Active Filters', 'wc-ajax-product-filter' ),
			'layout'               => 'simple',
			'empty_message'        => '',
			'clear_all_btn_label'  => WCAPF_Helper::clear_all_button_label(),
			'clear_all_btn_layout' => 'block',
		), $attrs );

		return WCAPF_Template_Loader::get_instance()->load( 'active-filters', $a, false );
	}

	/**
	 * Gets the instance of this class.
	 */
	public static function get_instance() {
		// Store the instance locally to avoid private static replication.
		static $instance = null;

		if ( null === $instance ) {
			$instance = new WCAPF_Active_Filters_Shortcode();
		}

		return $instance;
	}

}

add_shortcode( 'wcapf_active_filters', array( WCAPF_Active_Filters_Shortcode::get_instance(), 'shortcode_output' ) );
