<?php
/**
 * Reset button shortcode.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/shortcodes
 * @author     wptools.io
 */

/**
 * WCAPF_Reset_Button_Shortcode class.
 *
 * @since 4.0.0
 */
class WCAPF_Reset_Button_Shortcode {

	/**
	 * Constructor.
	 */
	private function __construct() {
	}

	public function shortcode_output( $attrs = array() ) {
		$a = shortcode_atts( array(
			'btn_label'   => WCAPF_Helper::reset_button_label(),
			'show_always' => '',
		), $attrs );

		return WCAPF_Template_Loader::get_instance()->load( 'reset-button', $a, false );
	}

	/**
	 * Gets the instance of this class.
	 */
	public static function get_instance() {
		// Store the instance locally to avoid private static replication.
		static $instance = null;

		if ( null === $instance ) {
			$instance = new WCAPF_Reset_Button_Shortcode();
		}

		return $instance;
	}

}

add_shortcode( 'wcapf_reset_button', array( WCAPF_Reset_Button_Shortcode::get_instance(), 'shortcode_output' ) );
