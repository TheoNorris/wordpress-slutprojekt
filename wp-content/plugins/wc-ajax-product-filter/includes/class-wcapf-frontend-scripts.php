<?php
/**
 * Frontend scripts class.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes
 * @author     wptools.io
 */

/**
 * WCAPF_Frontend_Scripts class.
 *
 * @since 4.0.0
 */
class WCAPF_Frontend_Scripts {

	/**
	 * Constructor.
	 */
	private function __construct() {
	}

	/**
	 * Returns an instance of this class.
	 *
	 * @return WCAPF_Frontend_Scripts
	 */
	public static function instance() {
		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance ) {
			$instance = new WCAPF_Frontend_Scripts();
			$instance->init_hooks();
		}

		return $instance;
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		// Loads after the pro version scripts are loaded.
		add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_scripts' ), 99 );
	}

	/**
	 * Loads the frontend scripts.
	 *
	 * @return void
	 */
	public function load_frontend_scripts() {
		if ( ! empty( WCAPF_Helper::wcapf_option( 'disable_wcapf' ) ) ) {
			return;
		}

		if ( WCAPF_Helper::load_scripts_conditionally() && ! WCAPF_Helper::found_wcapf() ) {
			return;
		}

		wp_enqueue_style(
			'wcapf-icons',
			WCAPF_PLUGIN_URL . 'public/icons/icons.css',
			array(),
			filemtime( WCAPF_PLUGIN_DIR . '/public/icons/icons.css' )
		);

		$ext = function_exists( 'wp_get_environment_type' ) && 'production' === wp_get_environment_type()
			? '.min.css'
			: '.css';

		wp_enqueue_style(
			'wcapf-chosen',
			WCAPF_PLUGIN_URL . 'public/lib/chosen/chosen' . $ext,
			array(),
			filemtime( WCAPF_PLUGIN_DIR . '/public/lib/chosen/chosen' . $ext )
		);

		wp_enqueue_style(
			'wcapf-nouislider',
			WCAPF_PLUGIN_URL . 'public/lib/nouislider/nouislider' . $ext,
			array(),
			filemtime( WCAPF_PLUGIN_DIR . '/public/lib/nouislider/nouislider' . $ext )
		);

		wp_enqueue_style(
			'wc-ajax-product-filter-styles',
			WCAPF_PLUGIN_URL . 'public/css/wc-ajax-product-filter-styles' . $ext,
			array(),
			filemtime( WCAPF_PLUGIN_DIR . '/public/css/wc-ajax-product-filter-styles' . $ext )
		);

		// Add css variables.
		$primary_color      = WCAPF_Helper::wcapf_option( 'primary_color', '#345DBB' );
		$primary_text_color = WCAPF_Helper::wcapf_option( 'primary_text_color', '#ffffff' );
		$star_icon_color    = WCAPF_Helper::wcapf_option( 'star_icon_color', 'rgb(240, 201, 48)' );

		list( $r, $g, $b ) = WCAPF_Helper::get_rgb_from_hex( $primary_color );
		list( $r2, $g2, $b2 ) = WCAPF_Helper::get_rgb_from_hex( $primary_text_color );

		$variables = ":root {
			--wcapf-primary-color-rgb: $r, $g, $b;
			--wcapf-primary-text-color-rgb: $r2, $g2, $b2;
			--wcapf-star-icon-color: $star_icon_color;
		}";

		wp_add_inline_style( 'wc-ajax-product-filter-styles', $variables );

		$ext = function_exists( 'wp_get_environment_type' ) && 'production' === wp_get_environment_type()
			? '.min.js'
			: '.js';

		wp_enqueue_script(
			'wcapf-chosen',
			WCAPF_PLUGIN_URL . 'public/lib/chosen/chosen.jquery' . $ext,
			array( 'jquery' ),
			filemtime( WCAPF_PLUGIN_DIR . '/public/lib/chosen/chosen.jquery' . $ext ),
			true
		);

		wp_enqueue_script(
			'wcapf-nouislider',
			WCAPF_PLUGIN_URL . 'public/lib/nouislider/nouislider' . $ext,
			array( 'jquery' ),
			filemtime( WCAPF_PLUGIN_DIR . '/public/lib/nouislider/nouislider' . $ext ),
			true
		);

		if ( WCAPF_Helper::use_tippyjs_for_tooltip() ) {
			wp_enqueue_script(
				'wcapf-popper',
				WCAPF_PLUGIN_URL . 'public/lib/tippyjs/popper.min.js',
				array(),
				false,
				true
			);

			wp_enqueue_script(
				'wcapf-tippy',
				WCAPF_PLUGIN_URL . 'public/lib/tippyjs/tippy-bundle.umd.min.js',
				array(),
				false,
				true
			);
		}

		wp_register_script( 'wcapf-params', false, array(), false, true );
		wp_localize_script( 'wcapf-params', 'wcapf_params', $this->get_js_params() );

		$deps = array( 'jquery', 'wcapf-params' );

		/**
		 * Required for the scrollTo, slideToggle animations.
		 *
		 * TODO: Maybe use a filter or decision comes from the admin via plugin settings.
		 *
		 * @source https://stackoverflow.com/a/27598883
		 */
		$deps[] = 'jquery-effects-core';

		wp_enqueue_script(
			'wc-ajax-product-filter-scripts',
			WCAPF_PLUGIN_URL . 'public/js/wc-ajax-product-filter-scripts' . $ext,
			$deps,
			filemtime( WCAPF_PLUGIN_DIR . '/public/js/wc-ajax-product-filter-scripts' . $ext ),
			true
		);
	}

	/**
	 * Frontend js params.
	 *
	 * @return array
	 */
	private function get_js_params() {
		$js_data = array(
			'disable_ajax',
			'enable_pagination_via_ajax',
			'sorting_control',
			'attach_combobox_on_sorting',
			'loading_animation',
			'scroll_window',
			'scroll_window_for',
			'scroll_window_when',
			'scroll_window_custom_element',
			'scroll_to_top_offset',
			'custom_scripts',
		);

		$shop_loop_identifier = '.' . WCAPF_Helper::shop_loop_container_identifier();
		$pagination_container = WCAPF_Helper::wcapf_option( 'pagination_container', '.woocommerce-pagination' );

		$settings = array(
			'shop_loop_container'  => $shop_loop_identifier,
			'not_found_container'  => $shop_loop_identifier,
			'pagination_container' => $pagination_container,
			'orderby_form'         => '.woocommerce-ordering',
			'orderby_element'      => 'select.orderby',
		);

		foreach ( $js_data as $key ) {
			$settings[ $key ] = WCAPF_Helper::wcapf_option( $key );
		}

		// Prevent attaching combobox on default orderby if combobox is disabled globally.
		if ( empty( WCAPF_Helper::wcapf_option( 'use_combobox' ) ) ) {
			$settings['attach_combobox_on_sorting'] = '';
		}

		$combobox_no_results_text   = WCAPF_Helper::no_results_text();
		$combobox_options_none_text = WCAPF_Helper::wcapf_option(
			'combobox_no_options_text',
			__( 'No options to choose', 'wc-ajax-product-filter' )
		);

		$params = array(
			'is_rtl'                                   => is_rtl(),
			'filter_input_delay'                       => WCAPF_Helper::wcapf_option( 'input_delay' ),
			'keyword_filter_delay'                     => 100,
			'combobox_display_selected_options'        => false,
			'combobox_no_results_text'                 => $combobox_no_results_text,
			'combobox_options_none_text'               => $combobox_options_none_text,
			'search_box_in_default_orderby'            => false,
			'preserve_hierarchy_accordion_state'       => true,
			'preserve_soft_limit_state'                => true,
			'enable_animation_for_filter_accordion'    => false,
			'filter_accordion_animation_speed'         => 400,
			'filter_accordion_animation_easing'        => 'swing',
			'enable_animation_for_hierarchy_accordion' => false,
			'hierarchy_accordion_animation_speed'      => 400,
			'hierarchy_accordion_animation_easing'     => 'swing',
			'scroll_to_top_speed'                      => 400,
			'scroll_to_top_easing'                     => 'easeOutQuad',
			'is_mobile'                                => wp_is_mobile(),
			'reload_on_back'                           => true,
			'found_wcapf'                              => WCAPF_Helper::found_wcapf(),
			'wcapf_pro'                                => WCAPF_Helper::found_pro_version(),
			'update_document_title'                    => true,
			'use_tippyjs'                              => WCAPF_Helper::use_tippyjs_for_tooltip(),
		);

		$params = array_merge( $params, $settings );

		return apply_filters( 'wcapf_js_params', $params );
	}

}

WCAPF_Frontend_Scripts::instance();
