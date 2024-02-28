<?php
/**
 * WCAPF - WooCommerce Ajax Product Filter Form shortcode.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/shortcodes
 * @author     wptools.io
 */

/**
 * WCAPF_Filter_Form_Shortcode class.
 *
 * @since 4.0.0
 */
class WCAPF_Filter_Form_Shortcode {

	/**
	 * Constructor.
	 */
	private function __construct() {
	}

	/**
	 * Gets the instance of this class.
	 */
	public static function get_instance() {
		// Store the instance locally to avoid private static replication.
		static $instance = null;

		if ( null === $instance ) {
			$instance = new WCAPF_Filter_Form_Shortcode();
		}

		return $instance;
	}

	public function register_shortcode() {
		ob_start();

		$this->render_debug_messages();

		$form = new WCAPF_Form();
		$form->render_form();

		return ob_get_clean();
	}

	public function render_debug_messages() {
		if ( ! WCAPF_Helper::is_debug_mode_enabled() ) {
			return;
		}

		global $wcapf_form;

		if ( is_shop() || is_product_taxonomy() ) {
			if ( ! $wcapf_form ) {
				/** @noinspection HtmlUnknownTarget */
				echo WCAPF_Helper::get_debug_message(
					sprintf(
						__( 'No forms found. <a href="%s">Create a form here</a>.', 'wc-ajax-product-filter' ),
						esc_url( admin_url( 'admin.php?page=wcapf' ) )
					)
				);
			} elseif ( isset( $wcapf_form['rendered'] ) ) {
				$upgrade_link = add_query_arg(
					array(
						'utm_source'   => 'WCAPF+Free',
						'utm_medium'   => 'frontend+multiple+form',
						'utm_campaign' => 'WCAPF+Pro+Upgrade',
					),
					'https://wptools.io/wc-ajax-product-filter/'
				);

				/** @noinspection HtmlUnknownTarget */
				echo WCAPF_Helper::get_debug_message(
					sprintf(
						__( 'The form has already been displayed and cannot be shown again. Upgrade to the <a href="%s" target="_blank">Pro version</a> for the ability to use multiple forms on a page.', 'wc-ajax-product-filter' ),
						esc_url( $upgrade_link )
					)
				);
			}
		} else {
			$upgrade_link = add_query_arg(
				array(
					'utm_source'   => 'WCAPF+Free',
					'utm_medium'   => 'frontend+singular+page',
					'utm_campaign' => 'WCAPF+Pro+Upgrade',
				),
				'https://wptools.io/wc-ajax-product-filter/'
			);

			/** @noinspection HtmlUnknownTarget */
			echo WCAPF_Helper::get_debug_message(
				sprintf(
					__( 'The free version of the plugin does not support filtering products on singular pages. Upgrade to the <a href="%s" target="_blank">Pro version</a> to unlock this feature.', 'wc-ajax-product-filter' ),
					esc_url( $upgrade_link )
				)
			);
		}
	}

}

add_shortcode( 'wcapf_form', array( WCAPF_Filter_Form_Shortcode::get_instance(), 'register_shortcode' ) );
