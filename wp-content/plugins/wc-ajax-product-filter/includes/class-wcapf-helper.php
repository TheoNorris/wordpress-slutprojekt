<?php
/**
 * The helper class.
 *
 * @since      3.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes
 * @author     wptools.io
 */

/**
 * WCAPF_Helper class.
 *
 * @since 3.0.0
 */
class WCAPF_Helper {

	/**
	 * @return bool
	 */
	public static function found_pro_version() {
		return defined( 'WCAPF_PRO_VERSION' );
	}

	/**
	 * Gets the form edit page url.
	 *
	 * @param int $form_id The form id.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function form_edit_url( $form_id ) {
		$forms_page_url = admin_url( 'admin.php?page=wcapf' );

		return add_query_arg( 'id', $form_id, $forms_page_url );
	}

	/**
	 * The forms page url.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function forms_page_url() {
		return menu_page_url( 'wcapf', false );
	}

	/**
	 * The seo rules page url.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function seo_rules_page_url() {
		return menu_page_url( 'wcapf-seo-rules', false );
	}

	/**
	 * The settings page url.
	 *
	 * @return string
	 */
	public static function settings_page_url() {
		return menu_page_url( 'wcapf-settings', false );
	}

	/**
	 * The upgrade page url.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function upgrade_page_url() {
		return menu_page_url( 'wcapf-upgrade', false );
	}

	/**
	 * Gets the meta keys for post type product.
	 *
	 * @noinspection SqlNoDataSourceInspection
	 * @noinspection SqlDialectInspection
	 *
	 * @source https://stackoverflow.com/a/54017483
	 *
	 * @return array
	 */
	public static function get_available_meta_keys() {
		global $wpdb;

		$post_type = 'product';

		$query = $wpdb->prepare(
			"
				SELECT DISTINCT($wpdb->postmeta.meta_key)
		        FROM $wpdb->posts
		        LEFT JOIN $wpdb->postmeta
		        ON $wpdb->posts.ID = $wpdb->postmeta.post_id
		        WHERE $wpdb->posts.post_type = %s
				AND $wpdb->postmeta.meta_key IS NOT NULL
				ORDER BY $wpdb->postmeta.meta_key
				",
			$post_type
		);

		$results   = $wpdb->get_col( $query );
		$meta_keys = array();

		foreach ( $results as $result ) {
			$meta_keys[] = array(
				'value' => $result,
				'label' => $result,
			);
		}

		return $meta_keys;
	}

	/**
	 * Gets the available meta values for the given meta key.
	 *
	 * @noinspection SqlNoDataSourceInspection
	 * @noinspection SqlDialectInspection
	 *
	 * @source https://wordpress.stackexchange.com/q/9394
	 *
	 * @return array
	 */
	public static function get_available_meta_values( $meta_key ) {
		global $wpdb;

		$post_type     = 'product';
		$post_statuses = WCAPF_Helper::filterable_post_statuses();

		$query = $wpdb->prepare(
			"
				SELECT DISTINCT $wpdb->postmeta.meta_value
				FROM $wpdb->postmeta
				INNER JOIN $wpdb->posts
				ON $wpdb->postmeta.post_id = $wpdb->posts.ID
		        WHERE $wpdb->posts.post_type = %s
		        AND $wpdb->posts.post_status IN ('" . implode( "','", $post_statuses ) . "')
				AND $wpdb->postmeta.meta_key = %s
				AND $wpdb->postmeta.meta_value <> ''
				GROUP BY $wpdb->postmeta.meta_value
				ORDER BY $wpdb->postmeta.meta_value
				",
			$post_type,
			$meta_key
		);

		$query = apply_filters( 'wcapf_available_meta_values_sql_query', $query, $meta_key );

		$results = $wpdb->get_col( $query );

		return apply_filters( 'wcapf_product_meta_values', $results, $meta_key );
	}

	/**
	 * The filtering works for the products with these post statuses.
	 *
	 * @return array
	 */
	public static function filterable_post_statuses() {
		$post_statuses = array( 'publish' );

		// Shop managers can see the private products, the filtering should work there.
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$post_statuses[] = 'private';
		}

		return apply_filters( 'wcapf_filterable_post_statuses', $post_statuses );
	}

	/**
	 * Gets the time period options.
	 *
	 * @return array
	 */
	public static function get_time_period_options( $with_ranges = false ) {
		$options = array();

		$ranges = array(
			'today'        => __( 'Today', 'wc-ajax-product-filter' ),
			'yesterday'    => __( 'Yesterday', 'wc-ajax-product-filter' ),
			'this-week'    => __( 'This week', 'wc-ajax-product-filter' ),
			'last-week'    => __( 'Last week', 'wc-ajax-product-filter' ),
			'this-month'   => __( 'This month', 'wc-ajax-product-filter' ),
			'last-month'   => __( 'Last month', 'wc-ajax-product-filter' ),
			'last-14-days' => __( 'Last 14 days', 'wc-ajax-product-filter' ),
			'last-30-days' => __( 'Last 30 days', 'wc-ajax-product-filter' ),
			'last-90-days' => __( 'Last 90 days', 'wc-ajax-product-filter' ),
			'this-year'    => __( 'This year', 'wc-ajax-product-filter' ),
			'last-year'    => __( 'Last year', 'wc-ajax-product-filter' ),
		);

		$range_separator = WCAPF_Helper::range_values_separator();

		$timestamp = current_time( 'timestamp' );
		$format    = 'Y-m-d';
		$today     = date( $format, $timestamp );

		foreach ( $ranges as $value => $label ) {
			$range = '';

			switch ( $value ) {
				case 'today':
					$start = date( $format, strtotime( $today ) );
					$range = $start . $range_separator . $start;

					break;

				case 'yesterday':
					$start = date( $format, strtotime( $today . ' -1 day' ) );
					$range = $start . $range_separator . $start;

					break;

				case 'this-week':
					$date = new DateTime();

					$current_year = date( 'Y' );
					$current_week = $date->format( 'W' );

					$dto = new DateTime();
					$dto->setISODate( $current_year, $current_week );
					$start = $dto->format( $format );
					$dto->modify( '+6 days' );
					$end = $dto->format( $format );

					$range = $start . $range_separator . $end;

					break;

				case 'last-week':
					$previous_week = strtotime( '-1 week +1 day' );

					$start_week = strtotime( 'last sunday midnight', $previous_week );
					$end_week   = strtotime( 'next saturday', $start_week );

					$start_week = date( $format, $start_week );
					$end_week   = date( $format, $end_week );

					$range = $start_week . $range_separator . $end_week;

					break;

				case 'this-month':
					$start = date( 'Y-m-01' );
					$end   = date( 'Y-m-t' );

					$range = $start . $range_separator . $end;

					break;

				case 'last-month':
					$start = date( $format, strtotime( 'first day of previous month' ) );
					$end   = date( $format, strtotime( 'last day of previous month' ) );

					$range = $start . $range_separator . $end;

					break;

				case 'last-14-days':
					$start = date( $format, strtotime( $today . ' -13 days' ) );
					$range = $start . $range_separator . $today;

					break;

				case 'last-30-days':
					$start = date( $format, strtotime( $today . ' -29 days' ) );
					$range = $start . $range_separator . $today;

					break;

				case 'last-90-days':
					$start = date( $format, strtotime( $today . ' -89 days' ) );
					$range = $start . $range_separator . $today;

					break;

				case 'this-year':
					$start = date( $format, strtotime( 'first day of January 1st' ) );
					$end   = date( $format, strtotime( 'last day of December 31st' ) );

					$range = $start . $range_separator . $end;

					break;

				case 'last-year':
					$start = date( $format, strtotime( 'last year January 1st' ) );
					$end   = date( $format, strtotime( 'last year December 31st' ) );

					$range = $start . $range_separator . $end;

					break;
			}

			if ( ! $range ) {
				continue;
			}

			$options[] = array(
				'value' => $value,
				'label' => $label,
				'range' => $range,
			);
		}

		$time_period_options = apply_filters( 'wcapf_time_period_options', $options );

		if ( $with_ranges ) {
			return $time_period_options;
		}

		return wp_list_pluck( $time_period_options, 'label', 'value' );
	}

	/**
	 * @return string
	 */
	public static function range_values_separator() {
		return '~';
	}

	/**
	 * @return array
	 */
	public static function number_input_display_types() {
		return array( 'range_slider', 'range_number' );
	}

	/**
	 * @param WCAPF_Field_Instance $instance The field instance.
	 *
	 * @return bool
	 */
	public static function round_range_min_max_values( $instance ) {
		return apply_filters( 'wcapf_round_range_min_max_values', true, $instance );
	}

	/**
	 * Gets the field relations.
	 *
	 * @return string
	 */
	public static function get_field_relations() {
		return self::wcapf_option( 'filter_relationships', 'and' );
	}

	/**
	 * Gets the option.
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @since 4.0.0
	 *
	 * @return mixed
	 */
	public static function wcapf_option( $key, $default = '' ) {
		global $wcapf;

		$value = isset( $wcapf[ $key ] ) ? $wcapf[ $key ] : '';

		if ( ! $value && $default ) {
			return $default;
		}

		return $value;
	}

	/**
	 * Determines if wcapf is configured for the page.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public static function found_wcapf() {
		global $wcapf;

		return (bool) $wcapf;
	}

	/**
	 * Determines if we load the js, css scripts conditionally(only when in the pages where filters found).
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public static function load_scripts_conditionally() {
		return apply_filters( 'wcapf_load_scripts_conditionally', true );
	}

	/**
	 * @param int $rating The rating.
	 *
	 * @return string
	 */
	public static function get_rating_entities( $rating ) {
		$rating_entities = '';

		while ( $rating > 0 ) {
			// @source https://www.htmlsymbols.xyz/unicode/U+2B50
			$rating_entities .= '&#11088;';
			$rating --;
		}

		return $rating_entities;
	}

	/**
	 * @param int $rating The rating.
	 *
	 * @return string
	 */
	public static function get_rating_svg_icons( $rating ) {
		$rating_html = '';
		$star_icons  = self::get_rating_star_icons();

		$remaining = 5 - $rating;

		while ( $rating > 0 ) {
			$rating_html .= $star_icons['star_full'];
			$rating --;
		}

		$show_empty_stars = apply_filters( 'wcapf_show_empty_star_in_rating', false );

		if ( $show_empty_stars ) {
			while ( $remaining > 0 ) {
				$rating_html .= $star_icons['star_empty'];
				$remaining --;
			}
		}

		return $rating_html;
	}

	/**
	 * Gets the html markup of rating star icon.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public static function get_rating_star_icons() {
		if ( self::wcapf_option( 'rating_star_use_fontawesome' ) ) {
			$star_full  = '<i class="fa-solid fa-star wcapf-star-icon"></i>';
			$star_empty = '<i class="fa-regular fa-star wcapf-star-icon"></i>';
			$star_half  = '<i class="fa-solid fa-star-half-stroke wcapf-star-icon"></i>';
		} else {
			$star_full  = '<i class="wcapf-icon-star-full wcapf-star-icon"></i>';
			$star_empty = '<i class="wcapf-icon-star-empty wcapf-star-icon"></i>';
			$star_half  = '<i class="wcapf-icon-star-half wcapf-star-icon"></i>';
		}

		return compact( 'star_full', 'star_empty', 'star_half' );
	}

	/**
	 * Shop loop container class.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function shop_loop_container_identifier() {
		return apply_filters( 'wcapf_shop_loop_container_identifier', 'wcapf-before-products' );
	}

	/**
	 * Checks if the product attribute filtering via lookup table feature is enabled.
	 *
	 * @return bool
	 */
	public static function filtering_via_lookup_table_is_active() {
		return apply_filters(
			'wcapf_attribute_lookup_enabled',
			'yes' === get_option( 'woocommerce_attribute_lookup_enabled' )
		);
	}

	/**
	 * @return bool
	 */
	public static function hide_stock_out_items() {
		return apply_filters(
			'wcapf_hide_out_of_stock_items',
			'yes' === get_option( 'woocommerce_hide_out_of_stock_items' )
		);
	}

	/**
	 * @param array  $filter_data Active filter data.
	 * @param string $extra_class Markup extra class.
	 *
	 * @return string
	 */
	public static function get_active_filters_markup( $filter_data, $extra_class = '' ) {
		$active_filters = isset( $filter_data['active_filters'] ) ? $filter_data['active_filters'] : array();
		$filter_key     = isset( $filter_data['filter_key'] ) ? $filter_data['filter_key'] : '';
		$filter_type    = isset( $filter_data['filter_type'] ) ? $filter_data['filter_type'] : '';

		$classes = 'wcapf-filter-clear-btn wcapf-active-filter-item';
		$classes .= $extra_class ? ' ' . $extra_class : '';

		$html = '';

		foreach ( $active_filters as $value => $label ) {
			$url_builder = new WCAPF_URL_Builder( $filter_key, true );

			if ( 'keyword' === $filter_type ) {
				// Here, it is possible to apply multiple keywords, so clear all applied keywords at once.
				$clear_filter_url = $url_builder->get_clear_filter_url();
			} else {
				$clear_filter_url = $url_builder->get_filter_url( $value, true );
			}

			$attrs = 'class="' . esc_attr( $classes ) . '"';
			$attrs .= ' data-clear-filter-url="' . esc_url( $clear_filter_url ) . '"';

			$html .= '<button ' . $attrs . '>';
			$html .= '<span class="wcapf-nav-item-text">'; // To avoid the flex wrap issue.
			$html .= wp_kses_post( $label );
			$html .= '</span>';
			$html .= '<span class="wcapf-cross-sign">&#215;</span>';
			$html .= '</button>';
		}

		return $html;
	}

	/**
	 * @param string $button_label   The label for button.
	 * @param string $override_class Whether to use custom style using a class.
	 * @param string $style          Button style, primary or secondary.
	 *
	 * @return string
	 */
	public static function get_reset_button_markup( $button_label, $override_class = '', $style = 'secondary' ) {
		$active_filters = self::get_active_filters_data();
		$filter_keys    = array_keys( $active_filters );

		if ( $override_class ) {
			$classes = 'wcapf-filter-clear-btn ' . $override_class;
		} else {
			if ( 'primary' === $style ) {
				$classes = 'wcapf-filter-clear-btn wcapf-btn wcapf-btn-primary';
			} else {
				$classes = 'wcapf-filter-clear-btn wcapf-btn wcapf-btn-secondary';
			}
		}

		$url_builder = new WCAPF_URL_Builder();
		$reset_url   = $url_builder->get_reset_url();

		$attrs = 'data-clear-filter-url="' . esc_url( $reset_url ) . '"';

		if ( ! $filter_keys ) {
			$attrs .= 'disabled="disabled"';
		}

		$html = '<button class="' . esc_attr( $classes ) . '" ' . $attrs . '>';
		$html .= $button_label;
		$html .= '</button>';

		return $html;
	}

	/**
	 * @return array
	 */
	public static function get_active_filters_data() {
		$chosen_filters = WCAPF_Helper::get_chosen_filters();
		$active_filters = array();

		foreach ( $chosen_filters as $filter_type_filters ) {
			foreach ( $filter_type_filters as $filter_type => $filter ) {
				$_active_filters = isset( $filter['active_filters'] ) ? $filter['active_filters'] : array();
				$filter_id       = isset( $filter['filter_id'] ) ? $filter['filter_id'] : '';
				$filter_key      = ! empty( $filter['filter_key'] ) ? $filter['filter_key'] : $filter_type;

				$active_filters[ $filter_key ] = array(
					'filter_key'     => $filter_key,
					'filter_type'    => $filter_type,
					'filter_id'      => $filter_id,
					'active_filters' => $_active_filters,
				);
			}
		}

		return $active_filters;
	}

	/**
	 * @return array
	 */
	public static function get_chosen_filters() {
		global $wcapf_chosen_filters;

		return $wcapf_chosen_filters ?: array();
	}

	/**
	 * Gets the active filter items for the active filters widget.
	 *
	 * @param string $layout Determines the active filters layout, possible values are simple, extended.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public static function get_active_filter_items( $layout = 'simple' ) {
		$active_filters = self::get_active_filters_data();

		// Sort the data according to the order in $_GET variable.
		$sorted = array();

		foreach ( $_GET as $_key => $_value ) {
			if ( array_key_exists( $_key, $active_filters ) ) {
				$sorted[ $_key ] = $active_filters[ $_key ];
			}
		}

		if ( 'extended' === $layout ) {
			$grouped = array();

			foreach ( $sorted as $filter_data ) {
				$filter_key      = isset( $filter_data['filter_key'] ) ? $filter_data['filter_key'] : '';
				$_active_filters = isset( $filter_data['active_filters'] ) ? $filter_data['active_filters'] : array();

				if ( $_active_filters ) {
					if ( array_key_exists( $filter_key, $grouped ) ) {
						$old = $grouped[ $filter_key ]['active_filters'];
						$new = $old + $_active_filters;

						$grouped[ $filter_key ]['active_filters'] = $new;
					} else {
						$grouped[ $filter_key ] = $filter_data;
					}
				}
			}

			return $grouped;
		}

		$filters_data = array();

		foreach ( $sorted as $filter_data ) {
			$active_filters = isset( $filter_data['active_filters'] ) ? $filter_data['active_filters'] : array();

			foreach ( $active_filters as $value => $label ) {
				$filter_data['active_filters'] = array( $value => $label );

				$filters_data[] = $filter_data;
			}
		}

		return $filters_data;
	}

	/**
	 * Checks if filter is active.
	 *
	 * @param string $filter_key The filter key.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public static function is_filter_active( $filter_key ) {
		$active_filters = self::get_active_filters_data();

		return array_key_exists( $filter_key, $active_filters );
	}

	/**
	 * Checks if vendor plugin found.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public static function is_vendor_plugin_found() {
		return apply_filters(
			'wcapf_vendor_plugin_found',
			class_exists( 'WCFMmp' ) || class_exists( 'WC_Vendors' )
		);
	}

	/**
	 * Gets the applied keyword.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	public static function get_applied_keyword() {
		$chosen = WCAPF_Helper::get_chosen_filters();

		$filters_data = isset( $chosen['filters_data'] ) ? $chosen['filters_data'] : array();
		$keyword_data = isset( $filters_data['keyword'] ) ? $filters_data['keyword'] : array();

		return isset( $keyword_data['values'][0] ) ? $keyword_data['values'][0] : '';
	}

	/**
	 * Gets the rgb array from hex color.
	 *
	 * @param string $hex The hex color code.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public static function get_rgb_from_hex( $hex ) {
		list( $r, $g, $b ) = sscanf( $hex, "#%02x%02x%02x" );

		return array( $r, $g, $b );
	}

	/**
	 * @return bool
	 */
	public static function animation_enabled_in_accordion() {
		return apply_filters( 'wcapf_enable_animation_in_accordion', false );
	}

	/**
	 * Determines if we keep the filter accordion as opened when the filter is active.
	 *
	 * TODO: Maybe move to inside the method.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public static function keep_accordion_opened_when_filter_active() {
		return apply_filters( 'wcapf_keep_accordion_opened_when_filter_active', false );
	}

	/**
	 * Determines if we show the soft limit options when the filter is active.
	 *
	 * TODO: This one is required.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public static function show_soft_limit_options_when_filter_active() {
		return apply_filters( 'wcapf_show_soft_limit_options_when_filter_active', true );
	}

	/**
	 * @return bool
	 */
	public static function use_tippyjs_for_tooltip() {
		return apply_filters( 'wcapf_use_tippyjs_for_tooltip', true );
	}

	/**
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function no_results_text() {
		return self::wcapf_option(
			'no_results_text',
			__( 'No results for:', 'wc-ajax-product-filter' )
		);
	}

	/**
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function empty_filter_text() {
		return self::wcapf_option( 'empty_filter_text', __( 'N/A', 'wc-ajax-product-filter' ) );
	}

	/**
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function sort_by_prefix() {
		return self::wcapf_option( 'sort_by_prefix', __( 'Sort by:', 'wc-ajax-product-filter' ) );
	}

	/**
	 * @since 4.1.0
	 *
	 * @return string
	 */
	public static function keyword_filter_prefix() {
		return self::wcapf_option( 'keyword_filter_prefix', __( 'Keyword:', 'wc-ajax-product-filter' ) );
	}

	public static function opening_btn_label() {
		$settings = self::get_settings();

		if ( ! empty( $settings['opening_btn_label'] ) ) {
			return $settings['opening_btn_label'];
		}

		return __( 'Filters', 'wc-ajax-product-filter' );
	}

	/**
	 * Gets the wcapf settings.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$option_name = self::settings_option_key();
		$db_options  = get_option( $option_name );
		$db_options  = $db_options ?: array();

		if ( has_filter( $option_name ) ) {
			$settings = wp_parse_args( apply_filters( $option_name, $db_options ), $db_options );
		} else {
			$settings = $db_options;
		}

		return $settings;
	}

	/**
	 * The option key that contains the plugin settings.
	 *
	 * @return string
	 */
	public static function settings_option_key() {
		return 'wcapf_settings';
	}

	public static function slide_out_panel_label() {
		$settings = self::get_settings();

		if ( ! empty( $settings['slide_out_panel_label'] ) ) {
			return $settings['slide_out_panel_label'];
		}

		return __( 'Filters', 'wc-ajax-product-filter' );
	}

	public static function clear_all_button_label() {
		return self::wcapf_option(
			'clear_all_button_label',
			__( 'Clear All', 'wc-ajax-product-filter' )
		);
	}

	public static function reset_button_label() {
		return self::wcapf_option( 'reset_button_label', __( 'Reset', 'wc-ajax-product-filter' ) );
	}

	public static function submit_btn_label() {
		global $wcapf;

		if ( ! empty( $wcapf['submit_btn_label'] ) ) {
			return $wcapf['submit_btn_label'];
		}

		return __( 'Submit', 'wc-ajax-product-filter' );
	}

	public static function apply_btn_label() {
		global $wcapf;

		if ( ! empty( $wcapf['apply_btn_label'] ) ) {
			return $wcapf['apply_btn_label'];
		}

		return __( 'Apply', 'wc-ajax-product-filter' );
	}

	/**
	 * Determines if debug mode is enabled or not.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public static function is_debug_mode_enabled() {
		if ( ! current_user_can( 'administrator' ) ) {
			return false;
		}

		$settings = self::get_settings();

		if ( ! empty( $settings['disable_wcapf'] ) ) {
			return false;
		}

		return ! empty( $settings['debug_mode'] );
	}

	/**
	 * Get the debug message with html markup.
	 *
	 * @param string $message The debug message.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function get_debug_message( $message ) {
		$styles = 'border: 1px dashed #b9b9b9;font-size: 14px;padding: 10px;margin: 0 0 15px;';

		return '<div class="wcapf-debug-message" style="' . $styles . '">' . $message . '</div>';
	}

	/**
	 * Determines if the review notice for milestone achieved should be shown.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public static function review_notice_for_milestone_achieved_can_be_shown() {
		if ( ! self::review_notices_can_be_shown() ) {
			return false;
		}

		$user_id  = get_current_user_id();
		$meta_key = 'wcapf_review_notice_for_milestone_achieved_dismissed';

		if ( get_user_meta( $user_id, $meta_key, true ) ) {
			return false;
		}

		$form_updates_count = get_user_meta( $user_id, 'wcapf_form_updates_count', true );
		$form_updates_count = intval( $form_updates_count );

		// Check if the form updates count is at least 5
		if ( $form_updates_count >= 5 ) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the review notices can be shown.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public static function review_notices_can_be_shown() {
		// Check if the user has the capability to manage options
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		// Check if we are showing the v4 migration notice.
		if ( self::v4_migration_notice_can_be_shown() ) {
			return false;
		}

		// Check if we are showing the v4 review filters notice.
		if ( self::v4_review_filters_notice_can_be_shown() ) {
			return false;
		}

		// Check if we are showing the pro version update required notice.
		if ( self::pro_update_notice_can_be_shown() ) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the v4 migration notice should be shown.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public static function v4_migration_notice_can_be_shown() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		if ( '1' !== get_option( 'wcapf_v4_migration_notice_status' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the v4 migration notice should be shown.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public static function v4_review_filters_notice_can_be_shown() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		if ( '1' !== get_option( 'wcapf_v4_review_filters_notice_status' ) ) {
			return false;
		}

		global $current_screen;

		$screen_id = isset( $current_screen->id ) ? $current_screen->id : '';

		if ( 'toplevel_page_wcapf' !== $screen_id ) {
			return false;
		}

		$form_id = ! empty( $_GET['id'] ) ? $_GET['id'] : '';

		if ( ! $form_id ) {
			return false;
		}

		if ( get_option( 'wcapf_migrated_filters_form_id' ) != $form_id ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if the update notice for the Pro version can be shown.
	 *
	 * TODO: Check this thoroughly.
	 *
	 * @since        4.0.0
	 *
	 * @return array An array of update notices, if any, based on the version requirements.
	 *               Each notice provides information on the required versions and a link to proceed with the update.
	 *               Returns an empty array if no update notices need to be shown.
	 */
	public static function pro_update_notice_can_be_shown() {
		$notices = array();

		// Check if the current user has the capability to manage options
		if ( ! current_user_can( 'manage_options' ) ) {
			return $notices;
		}

		// Check if the Pro version plugin is not active
		if ( ! is_plugin_active( 'wc-ajax-product-filter-pro/wc-ajax-product-filter-pro.php' ) ) {
			return $notices;
		}

		// Check if pro version found but the version number could not be obtained using the constant.
		if ( ! defined( 'WCAPF_PRO_VERSION' ) ) {
			$notices[] = self::get_pro_update_notice( '2.1.0' );

			return $notices;
		}

		// Check if the free version is 4.0.0 and pro version is less than 2.0.0
		if ( defined( 'WCAPF_VERSION' ) && version_compare( WCAPF_VERSION, '4.0.0', '=' ) && version_compare( WCAPF_PRO_VERSION, '2.0.0', '<' ) ) {
			$notices[] = self::get_pro_update_notice( '2.0.0' );
		}

		// Check if the free version is 4.1.0 and pro version is less than or equal to 2.0.0
		if ( defined( 'WCAPF_VERSION' ) && version_compare( WCAPF_VERSION, '4.1.0', '=' ) && version_compare( WCAPF_PRO_VERSION, '2.0.0', '<=' ) ) {
			$notices[] = self::get_pro_update_notice( '2.1.0' );
		}

		return $notices;
	}

	/**
	 * Get the pro updated notice.
	 *
	 * @param string $required_pro_version The required pro version.
	 *
	 * @since        4.1.0
	 *
	 * @return string
	 */
	private static function get_pro_update_notice( $required_pro_version ) {
		$update_plugin_doc_url = add_query_arg(
			array(
				'utm_source'   => 'WP+Admin',
				'utm_medium'   => 'update_pro_notice',
				'utm_campaign' => 'WCAPF+Pro+Update',
			),
			'https://wptools.io/docs/wc-ajax-product-filter/getting-started/update-plugin/'
		);

		if ( defined( 'WCAPF_PRO_VERSION' ) ) {
			$pro_version = WCAPF_PRO_VERSION;
		} else {
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/wc-ajax-product-filter-pro/wc-ajax-product-filter-pro.php' );
			$pro_version = $plugin_data['Version'];
		}

		/** @noinspection HtmlUnknownTarget */
		return sprintf(
			__( 'WCAPF - WooCommerce Ajax Product Filter version %s requires WCAPF - WooCommerce Ajax Product Filter Pro version %s or higher, but you are using %s. The Pro version is currently NOT RUNNING. <a href="%s" target="_blank">Please proceed with the update</a>.', 'wc-ajax-product-filter' ),
			WCAPF_VERSION,
			$required_pro_version,
			$pro_version,
			$update_plugin_doc_url
		);
	}

	/**
	 * Determines if the review notice for time since should be shown.
	 *
	 * @since 4.0.0
	 *
	 * @return bool|string Return false when the review notice should not be shown otherwise return the time since.
	 */
	public static function review_notice_for_time_since_can_be_shown() {
		if ( ! WCAPF_Helper::review_notices_can_be_shown() ) {
			return false;
		}

		$user_id      = get_current_user_id();
		$current_time = time();

		// Check if the milestone achieved notice is dismissed within the past 24 hours.
		$meta_key       = 'wcapf_review_notice_for_milestone_achieved_dismissed_at';
		$dismissal_time = get_user_meta( $user_id, $meta_key, true );

		if ( $dismissal_time && ( $current_time - $dismissal_time ) < 24 * 60 * 60 ) {
			return false;
		}

		// Check if the time since notice is permanently disabled for this user.
		$meta_key = 'wcapf_review_notice_time_since_hide_permanently';

		if ( get_user_meta( $user_id, $meta_key, true ) ) {
			return false;
		}

		$activation_time = get_option( 'wcapf_activation_time' );

		if ( ! $activation_time ) {
			return false;
		}

		// Intervals to show the review notice (in seconds) with corresponding human-readable strings.
		$intervals = array(
			31536000 => '1 year',
			15552000 => '6 months',
			7776000  => '3 months',
			2592000  => '1 month',
			1209600  => '2 weeks',
			604800   => '1 week',
		);

		$dismissal_key      = 'wcapf_review_notice_time_since_dismissed_at';
		$dismissal_time     = get_user_meta( $user_id, $dismissal_key, true );
		$dismissed_interval = null;

		if ( $dismissal_time ) {
			$elapsed_time = $dismissal_time - $activation_time;

			// Find the dismissed interval.
			foreach ( array_keys( $intervals ) as $interval ) {
				if ( $interval >= $elapsed_time ) {
					$dismissed_interval = $interval;
				}
			}
		}

		$timestamp    = null;
		$show         = null;
		$elapsed_time = $current_time - $activation_time;

		// Find the current interval based on elapsed time.
		foreach ( $intervals as $interval => $human_readable_interval ) {
			if ( $elapsed_time >= $interval ) {
				$timestamp = $interval;
				$show      = $human_readable_interval;

				break;
			}
		}

		if ( $dismissed_interval && $timestamp < $dismissed_interval ) {
			return false;
		}

		return $show;
	}

}
