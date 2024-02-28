<?php
/**
 * WCAPF - WooCommerce Ajax Product Filter hooks class.
 *
 * @since      3.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/hooks
 * @author     wptools.io
 */

/**
 * WCAPF_Hooks class.
 *
 * @since 3.0.0
 */
class WCAPF_Hooks {

	/**
	 * Constructor.
	 */
	private function __construct() {
	}

	/**
	 * Returns an instance of this class.
	 *
	 * @return WCAPF_Hooks
	 */
	public static function instance() {
		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance ) {
			$instance = new WCAPF_Hooks();
			$instance->init_hooks();
		}

		return $instance;
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		add_filter( 'body_class', array( $this, 'add_body_classes' ) );
		add_action( 'wp_footer', array( $this, 'insert_loader' ) );
		add_filter( 'redirect_canonical', array( $this, 'suppress_canonical_redirect' ) );
		add_filter( 'paginate_links', array( $this, 'modify_paginated_link' ) );
		add_filter( 'woocommerce_redirect_single_search_result', array( $this, 'single_search_redirect' ) );
		add_action( 'woocommerce_before_shop_loop', array( $this, 'insert_before_shop_loop' ), 0 );
		add_action( 'woocommerce_after_shop_loop', array( $this, 'insert_after_shop_loop' ), 200 );
		add_action( 'woocommerce_before_template_part', array( $this, 'insert_before_no_products' ), 0 );
		add_action( 'woocommerce_after_template_part', array( $this, 'insert_after_no_products' ), 200 );
		add_action( 'woocommerce_before_shop_loop', array( $this, 'active_filters_before_shop_loop' ), - 10 );
		add_action( 'woocommerce_before_template_part', array( $this, 'active_filters_before_no_products' ), - 10 );
		add_filter( 'wcapf_form_filter_data', array( $this, 'set_form_filter_data' ) );
		add_action( 'woocommerce_product_query', array( $this, 'set_query' ) );
	}

	/**
	 * Adds the classes into the body element.
	 *
	 * @param array $classes The classes.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function add_body_classes( $classes ) {
		if ( ! $this->should_we_proceed() ) {
			return $classes;
		}

		$improve_scrollbar   = WCAPF_Helper::wcapf_option( 'improve_scrollbar' );
		$improve_text_inputs = WCAPF_Helper::wcapf_option( 'improve_input_type_text_number' );
		$remove_focus_style  = WCAPF_Helper::wcapf_option( 'remove_focus_style' );
		$use_wait_cursor     = WCAPF_Helper::wcapf_option( 'wait_cursor' );

		if ( $improve_scrollbar ) {
			$classes[] = 'wcapf-pretty-scroll';
		}

		if ( $improve_text_inputs ) {
			$classes[] = 'wcapf-pretty-text-inputs';
		}

		if ( ! $remove_focus_style ) {
			$classes[] = 'wcapf-use-focus';
		}

		if ( $use_wait_cursor ) {
			$classes[] = 'wcapf-use-wait-cursor';
		}

		return $classes;
	}

	/**
	 * Determines if we proceed or not.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	private function should_we_proceed() {
		return WCAPF_Helper::found_wcapf();
	}

	/**
	 * Insert the loader.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function insert_loader() {
		if ( ! $this->should_we_proceed() ) {
			return;
		}

		$html = '<div class="wcapf-loader">';

		$loading_animation = WCAPF_Helper::wcapf_option( 'loading_animation', 'overlay-with-icon' );

		if ( 'overlay-with-icon' === $loading_animation ) {
			$loading_image = WCAPF_Helper::wcapf_option( 'loading_icon', 'Dual-Ring' );

			$image_file = WCAPF_PLUGIN_DIR . '/public/loaders/' . $loading_image . '.svg';

			// Default image.
			if ( ! file_exists( $image_file ) ) {
				$image_file = WCAPF_PLUGIN_DIR . '/public/loaders/Dual-Ring.svg';
			}

			$image_size = WCAPF_Helper::wcapf_option( 'loading_image_size', '60' ) . 'px';
			$image      = file_get_contents( $image_file );

			$html .= '<div class="wcapf-loader-image" style="width: ' . $image_size . '">' . $image . '</div>';
		}

		$html .= '</div>';

		echo $html;
	}

	/**
	 * Return false to cancel the redirect.
	 *
	 * @param string $redirect The redirect url.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function suppress_canonical_redirect( $redirect ) {
		if ( ! $this->should_we_proceed() ) {
			return $redirect;
		}

		return false;
	}

	/**
	 * Remove the paginated part for the first page only.
	 *
	 * @param string $link The paginated link URL.
	 *
	 * @source https://weusewp.com/tutorial/pagination-remove-page-1/
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function modify_paginated_link( $link ) {
		if ( ! $this->should_we_proceed() ) {
			return $link;
		}

		if ( is_paged() ) {
			$link = str_replace( 'page/1/', '', $link );
		}

		return str_replace( '%2C', ',', $link );
	}

	/**
	 * Prevent redirecting to the product page if a single result is found while filtering on the search page.
	 *
	 * @since 3.3.2
	 *
	 * @return bool
	 */
	public function single_search_redirect( $redirect ) {
		if ( ! $this->should_we_proceed() ) {
			return $redirect;
		}

		return false;
	}

	/**
	 * HTML wrapper to insert before the shop loop.
	 *
	 * @return void
	 */
	public function insert_before_shop_loop() {
		if ( ! $this->should_we_proceed() ) {
			return;
		}

		echo '<div class="' . esc_attr( WCAPF_Helper::shop_loop_container_identifier() ) . '">';
	}

	/**
	 * HTML wrapper to insert after the shop loop.
	 *
	 * @return void
	 */
	public function insert_after_shop_loop() {
		if ( ! $this->should_we_proceed() ) {
			return;
		}

		echo '</div>';
	}

	/**
	 * HTML wrapper to insert before the not found product loops.
	 *
	 * @param string $template_name The template name.
	 *
	 * @return void
	 */
	public function insert_before_no_products( $template_name ) {
		if ( ! $this->should_we_proceed() ) {
			return;
		}

		if ( 'loop/no-products-found.php' === $template_name ) {
			echo '<div class="' . esc_attr( WCAPF_Helper::shop_loop_container_identifier() ) . '">';
		}
	}

	/**
	 * HTML wrapper to insert after the not found product loops.
	 *
	 * @param string $template_name The template name.
	 *
	 * @return void
	 */
	public function insert_after_no_products( $template_name ) {
		if ( ! $this->should_we_proceed() ) {
			return;
		}

		if ( 'loop/no-products-found.php' === $template_name ) {
			echo '</div>';
		}
	}

	/**
	 * Renders the active filters before shop loop.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function active_filters_before_shop_loop() {
		if ( ! $this->should_we_proceed() ) {
			return;
		}

		$this->render_active_filters();
	}

	/**
	 * Renders the active filters before shop loop.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function render_active_filters() {
		if ( ! empty( WCAPF_Helper::wcapf_option( 'active_filters_on_top' ) ) ) {
			$active_filters_on_top_args = apply_filters(
				'wcapf_active_filters_on_top_args',
				array(
					'clear_all_btn_label'  => WCAPF_Helper::clear_all_button_label(),
					'clear_all_btn_layout' => 'inline',
				)
			);

			echo '<div class="wcapf-active-filters-before-shop-loop">';
			WCAPF_Template_Loader::get_instance()->load( 'active-filters', $active_filters_on_top_args );
			echo '</div>';
		}
	}

	/**
	 * Renders the active filters before no products found template.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function active_filters_before_no_products( $template_name ) {
		if ( ! $this->should_we_proceed() ) {
			return;
		}

		if ( 'loop/no-products-found.php' === $template_name ) {
			$this->render_active_filters();
		}
	}

	/**
	 * Sets the filter data according to the form and wcapf settings.
	 *
	 * @param array $data The filter data.
	 *
	 * @since 4.0.0
	 *
	 * @return array The filter data that will be merged with filter settings.
	 */
	public function set_form_filter_data( $data ) {
		$empty_options = array( 'show', 'remove' );
		$remove_empty  = WCAPF_Helper::wcapf_option( 'remove_empty' );

		if ( ! in_array( $remove_empty, $empty_options ) ) {
			$remove_empty = 'show';
		}

		return wp_parse_args(
			array(
				'hide_empty'    => $remove_empty,
				'update_count'  => ! empty( WCAPF_Helper::wcapf_option( 'update_count' ) ),
				'use_combobox'  => ! empty( WCAPF_Helper::wcapf_option( 'use_combobox' ) ),
				'use_term_slug' => ! empty( WCAPF_Helper::wcapf_option( 'use_term_slug' ) ),
			),
			$data
		);
	}

	/**
	 * Query the products, applying the filters. This applies to the main WordPress loop.
	 *
	 * @param WP_Query $q Query instance.
	 *
	 * @return void
	 */
	public function set_query( $q ) {
		if ( ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}

		WCAPF_V4_Migration()->try_to_run_v4_migration();

		$filter = new WCAPF_Product_Filter();

		global $wcapf_chosen_filters;

		$chosen_filters = $filter->get_chosen_filters();

		$wcapf_chosen_filters = $chosen_filters;

		/**
		 * We must hook the filter early to avoid the sorting issues.
		 */
		add_filter( 'posts_clauses', array( $this, 'posts_clauses' ), 5, 2 );

		/**
		 * Apply the keyword filter.
		 */
		if ( $q->is_main_query() ) {
			$keyword = WCAPF_Helper::get_applied_keyword();

			if ( $keyword ) {
				$q->set( 's', $keyword );
			}
		}
	}

	/**
	 * @param array    $args     The query clauses.
	 * @param WP_Query $wp_query Query instance.
	 *
	 * @return array The modified query clauses.
	 */
	public function posts_clauses( $args, $wp_query ) {
		if ( ! $wp_query->is_main_query() ) {
			return $args;
		}

		$filter = new WCAPF_Product_Filter();

		$args['join']  .= $filter->get_full_join_clause();
		$args['where'] .= $filter->get_full_where_clause();

		return $args;
	}

}

add_action( 'plugins_loaded', array( 'WCAPF_Hooks', 'instance' ) );
