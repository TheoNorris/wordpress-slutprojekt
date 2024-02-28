<?php
/**
 * The default data class.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes
 * @author     wptools.io
 */

/**
 * WCAPF_Default_Data class.
 *
 * @since 4.0.0
 */
class WCAPF_Default_Data {

	public static function form_default_data() {
		return array(
			'form_locations' => '',
			'priority'       => '0',
			// 'form_layout' => 'vertical',
			// 'columns_per_row' => '4',
			// 'show_form_on_top_of_products' => '1',
			// 'filter_mode' => 'immediate',
			// 'form_visibility' => 'always_display',
			'show_clear_btn' => '',
		);
	}

	public static function default_settings() {
		return array(
			// General
			'filter_relationships'             => 'and',
			'update_count'                     => '1',
			'remove_empty'                     => 'show',
			'remove_empty_filters'             => '',
			'disable_ajax'                     => '',
			// Integration
			'active_filters_on_top'            => '1',
			'enable_pagination_via_ajax'       => '1',
			'sorting_control'                  => '1',
			'sorting_data_in_active_filters'   => '1',
			'use_term_slug'                    => '1',
			'child_terms_only'                 => '',
			'vendor_support'                   => '',
			// Appearance
			'primary_color'                    => '#1c5da1',
			'primary_text_color'               => '#ffffff',
			'stylish_checkbox_radio'           => '1',
			'use_combobox'                     => '1',
			'attach_combobox_on_sorting'       => '',
			'improve_native_select'            => '1',
			'improve_scrollbar'                => '1',
			'improve_input_type_text_number'   => '1',
			'hierarchy_toggle_at_end'          => '',
			'number_range_slider_style'        => 'style-1',
			'star_icon_color'                  => '#fda256',
			'rating_star_use_fontawesome'      => '',
			'remove_focus_style'               => '',
			'primary_btn_class'                => '',
			'secondary_btn_class'              => '',
			'slide_out_panel_position'         => 'left',
			// Loader & Scroll To
			'loading_animation'                => 'overlay-with-icon',
			'loading_icon'                     => 'Dual-Ring',
			'loading_image'                    => '',
			'loading_image_src'                => '',
			'loading_image_size'               => '60',
			'loading_overlay_color'            => '#FFFFFFCC',
			'wait_cursor'                      => '',
			'scroll_window'                    => 'none',
			'scroll_window_for'                => 'desktop',
			'scroll_window_when'               => 'after',
			'scroll_window_custom_element'     => '',
			'scroll_on'                        => 'all',
			'scroll_to_top_offset'             => '',
			'disable_scroll_animation'         => '',
			// Others
			'keyword_filter_placeholder'       => '',
			'keyword_filter_prefix'            => '',
			'search_field_default_placeholder' => '',
			'no_results_text'                  => '',
			'combobox_no_options_text'         => '',
			'empty_filter_text'                => '',
			'show_more_btn_label'              => '',
			'show_less_btn_label'              => '',
			'opening_btn_label'                => '',
			'slide_out_panel_label'            => '',
			'clear_button_label'               => '',
			'clear_all_button_label'           => '',
			'reset_button_label'               => '',
			'submit_btn_label'                 => '',
			'apply_btn_label'                  => '',
			'results_count_markup'             => '',
			'sort_by_prefix'                   => '',
			'per_page_prefix'                  => '',
			'input_delay'                      => '300',
			'pagination_container'             => '.woocommerce-pagination',
			'more_selectors'                   => '',
			'custom_scripts'                   => '',
			'author_roles'                     => array( 'administrator', 'shop_manager' ),
			'multiple_form_locations'          => '',
			// Miscellaneous
			'debug_mode'                       => '1',
			'disable_wcapf'                    => '',
			'send_anonymous_data'              => '',
			'remove_data'                      => '',
		);
	}

	public static function get_sample_filters() {
		$filters_basic_data = array();

		// Filter by product category.
		$filters_basic_data[] = array(
			'title'                      => __( 'Category', 'wc-ajax-product-filter' ),
			'type'                       => 'taxonomy',
			'taxonomy'                   => 'product_cat',
			'taxHierarchical'            => '1',
			'hierarchical'               => '1',
			'enable_hierarchy_accordion' => '1',
		);

		// Filter by price.
		$price_filter_data = array(
			'title'            => __( 'Price', 'wc-ajax-product-filter' ),
			'type'             => 'price',
			'values_separator' => '&nbsp;–&nbsp;',
		);

		$code   = get_woocommerce_currency();
		$symbol = html_entity_decode( get_woocommerce_currency_symbol( $code ) );

		if ( 'left' === get_option( 'woocommerce_currency_pos' ) ) {
			$price_filter_data['value_prefix'] = $symbol;
		} else {
			$price_filter_data['value_postfix'] = $symbol;
		}

		$filters_basic_data[] = $price_filter_data;

		// Filter by attribute, we pick the first one which has some values for single dropdown filter.
		$attributes = wc_get_attribute_taxonomy_names();
		$used       = array();

		foreach ( $attributes as $attribute ) {
			$attribute_terms = get_terms(
				array(
					'taxonomy'   => $attribute,
					'hide_empty' => false,
				)
			);

			if ( $attribute_terms ) {
				$taxonomy_data = get_taxonomy( $attribute );

				$filters_basic_data[] = array(
					'title'           => $taxonomy_data->labels->singular_name,
					'type'            => 'taxonomy',
					'taxonomy'        => $attribute,
					'display_type'    => 'select',
					'all_items_label' => __( 'Any', 'wc-ajax-product-filter' ),
				);

				$used[] = $attribute;

				break;
			}
		}

		// Filter by attribute, we pick the second attribute which has some values for multi-select dropdown filter.
		$found_second_attribute = false;

		foreach ( $attributes as $attribute ) {
			if ( in_array( $attribute, $used ) ) {
				continue;
			}

			$attribute_terms = get_terms(
				array(
					'taxonomy'   => $attribute,
					'hide_empty' => false,
				)
			);

			if ( $attribute_terms ) {
				$taxonomy_data = get_taxonomy( $attribute );

				$filters_basic_data[] = array(
					'title'        => $taxonomy_data->labels->singular_name,
					'type'         => 'taxonomy',
					'taxonomy'     => $attribute,
					'display_type' => 'multi-select',
				);

				$found_second_attribute = true;

				break;
			}
		}

		// Filter by product tag if tags found and secondary attribute filter not found.
		if ( ! $found_second_attribute ) {
			$tags = get_terms(
				array(
					'taxonomy'   => 'product_tag',
					'hide_empty' => false,
				)
			);

			if ( $tags ) {
				$filters_basic_data[] = array(
					'title'    => __( 'Tag', 'wc-ajax-product-filter' ),
					'type'     => 'taxonomy',
					'taxonomy' => 'product_tag',
					'layout'   => 'multi-select',
				);
			}
		}

		// Filter by Product Status.
		$filters_basic_data[] = array(
			'title'                  => __( 'Status', 'wc-ajax-product-filter' ),
			'type'                   => 'product-status',
			'display_type'           => 'label',
			'product_status_options' => array(
				array(
					'value' => 'featured',
					'label' => __( 'Featured', 'wc-ajax-product-filter' ),
				),
				array(
					'value' => 'on_sale',
					'label' => __( 'On Sale', 'wc-ajax-product-filter' ),
				),
			),
		);

		// Filter by Rating.
		$filters_basic_data[] = array(
			'type' => 'rating',
		);

		// Reset button.
		$filters_basic_data[] = array(
			'type'      => 'component',
			'component' => 'reset-button',
		);

		$filters = array();

		foreach ( $filters_basic_data as $filter_basic_data ) {
			$filter_data = wp_parse_args( $filter_basic_data, self::filter_default_data() );

			$filters[] = $filter_data;
		}

		return $filters;
	}

	public static function filter_default_data() {
		return array(
			'id'                                    => '',
			'title'                                 => '',
			'type'                                  => '',
			'taxonomy'                              => '',
			'taxHierarchical'                       => '',
			'meta_key'                              => '',
			'component'                             => '',
			'value_type'                            => 'text',
			'field_key'                             => '',
			// Taxonomy
			'display_type'                          => 'checkbox',
			'native_display_type_layout'            => 'list',
			'custom_display_type_layout'            => 'inline',
			'grid_columns'                          => '2',
			'query_type'                            => 'or',
			'all_items_label'                       => '',
			'enable_multiple_filter'                => '1',
			'hierarchical'                          => '',
			'enable_hierarchy_accordion'            => '',
			'show_count'                            => '1',
			'enable_tooltip'                        => '',
			'show_count_in_tooltip'                 => '',
			'tooltip_position'                      => 'top',
			'get_options'                           => 'automatically',
			'manual_options'                        => array(),
			'order_terms_by'                        => 'default',
			'order_terms_dir'                       => 'asc',
			'limit_options'                         => 'off',
			'include_terms'                         => array(),
			'include_child'                         => '',
			'exclude_terms'                         => array(),
			'exclude_child'                         => '',
			'parent_term'                           => '',
			'direct_child_only'                     => '',
			// Swatch Data
			'enable_swatch'                         => '',
			'swatch_type'                           => 'color',
			'swatch_with_label'                     => '1',
			// Price Filter
			'number_display_type'                   => 'range_slider',
			'number_range_slider_display_values_as' => 'input_field',
			'alignment'                             => 'default',
			'input_type_number'                     => '',
			'number_range_enable_multiple_filter'   => '1',
			'number_range_query_type'               => 'or',
			'number_range_select_all_items_label'   => '',
			'number_range_show_count'               => '1',
			'number_get_options'                    => 'automatically',
			'number_manual_options'                 => array(),
			'auto_detect_min_max'                   => '1',
			'min_value'                             => '0',
			'max_value'                             => '100',
			'step'                                  => '1',
			'gap'                                   => '0',
			'value_prefix'                          => '',
			'value_postfix'                         => '',
			'values_separator'                      => '–',
			'text_before_min_value'                 => '',
			'text_before_max_value'                 => '',
			'format_numbers'                        => '',
			'decimal_places'                        => '2',
			'thousand_separator'                    => '',
			'decimal_separator'                     => '.',
			// Product Status
			'product_status_options'                => array(),
			// Post Meta
			'value_decimal'                         => '',
			'value_decimal_places'                  => '2',
			'date_input_format'                     => 'timestamp',
			'options_order_by'                      => 'none',
			'options_order_dir'                     => 'asc',
			'options_order_type'                    => 'alphabetical',
			// Post Meta - value type Date
			'date_display_type'                     => 'input_date',
			'date_format'                           => 'dd-mm-yy',
			'time_period_enable_multiple_filter'    => '1',
			'time_period_query_type'                => 'or',
			'time_period_select_all_items_label'    => '',
			'show_date_inputs_inline'               => '',
			'time_period_show_count'                => '1',
			'date_picker_month_dropdown'            => '',
			'date_picker_year_dropdown'             => '',
			'date_from_prefix'                      => '',
			'date_from_postfix'                     => '',
			'date_from_placeholder'                 => '',
			'date_to_prefix'                        => '',
			'date_to_postfix'                       => '',
			'date_to_placeholder'                   => '',
			'time_period_options'                   => array(),
			// Post Author
			'post_author_order_by'                  => 'default',
			'post_author_order_dir'                 => 'asc',
			'include_authors'                       => array(),
			'exclude_authors'                       => array(),
			'include_user_roles'                    => array(),
			// Sort By
			'sort_by_options'                       => array(),
			// Per Page
			'per_page_options'                      => array(),
			// Advanced Settings
			'show_title'                            => '1',
			'enable_accordion'                      => '',
			'accordion_default_state'               => 'expanded',
			'help_text'                             => '',
			'enable_search_field'                   => '',
			'search_field_placeholder'              => '',
			'enable_reduce_height'                  => 'no',
			'soft_limit'                            => '5',
			'max_height'                            => '200',
			'show_in_active_filters'                => '1',
			'enable_visibility_rules'               => '',
			'visibility_rules'                      => array(),
			// Active filters
			'active_filters_layout'                 => 'simple',
			'empty_filter_message'                  => '',
			// Reset Button
			'show_if_empty'                         => '',
			// Error
			'type_error'                            => '',
			'meta_key_error'                        => '',
			'field_key_error'                       => '',
			'field_key_error_'                      => '', // Comes from server side.
		);
	}

}
