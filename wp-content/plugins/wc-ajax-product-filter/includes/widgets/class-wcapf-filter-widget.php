<?php
/**
 * WCAPF - WooCommerce Ajax Product Filter widget.
 *
 * @since      3.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes
 * @author     wptools.io
 */

/**
 * WCAPF_Filter_Widget class.
 *
 * @since 3.0.0
 */
class WCAPF_Filter_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'description'           => __( 'Display a form to filter products in your store.', 'wc-ajax-product-filter' ),
			'show_instance_in_rest' => true,
		);

		parent::__construct(
			false, // Base ID
			'WCAPF - Product Filter Form', // Name of Widget
			$widget_ops // args
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance The widget options.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		echo do_shortcode( '[wcapf_form]' );
	}

	/**
	 * Back-end widget form.
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		?>
		<p>
			<?php printf( __( 'No settings are required! The form will be displayed according to the <b>Available on</b> setting.', 'wc-ajax-product-filter' ) ); ?>
		</p>
		<?php
	}

}

/**
 * Register the widget.
 *
 * @return void
 */
function wcapf_register_filter_widget() {
	register_widget( 'WCAPF_Filter_Widget' );
}

add_action( 'widgets_init', 'wcapf_register_filter_widget' );
