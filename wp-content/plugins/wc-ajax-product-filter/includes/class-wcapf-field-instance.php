<?php
/**
 * WCAPF_Field_Instance class.
 *
 * @since      3.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes
 * @author     wptools.io
 */

/**
 * WCAPF_Field_Instance class.
 *
 * @since 3.0.0
 */
class WCAPF_Field_Instance {

	public $display_type;
	public $layout;
	public $query_type;
	public $all_items_label;
	public $use_combobox;
	public $enable_multiple_filter;
	public $show_count;
	public $hide_empty;
	public $get_options;
	public $manual_options;
	public $type;
	public $filter_id;
	public $filter_key;
	public $filter_type;
	public $taxonomy;
	public $hierarchical;
	public $enable_hierarchy_accordion;
	public $value_type;
	public $meta_key;
	public $post_property;
	public $use_store_name;
	public $use_term_slug;
	public $number_data_type;
	public $form_id;
	public $enable_search_field;
	public $enable_reduce_height;
	public $max_height;
	public $soft_limit;
	public $enable_soft_limit;
	public $enable_max_height;

	/**
	 * The raw field instance.
	 *
	 * @var array
	 */
	private $instance;

	/**
	 * The constructor.
	 *
	 * @param array $instance The raw field instance.
	 */
	public function __construct( $instance ) {
		$this->instance = $instance;

		$this->set_default_properties();
	}

	/**
	 * Sets the default properties.
	 *
	 * @return void
	 */
	private function set_default_properties() {
		$field_type = $this->get_field_type();
		$this->type = $field_type;

		$value_type = $this->field_default_value_type();

		if ( 'number' === $value_type ) {
			$display_type           = $this->get_sub_field_value( 'number_display_type' );
			$query_type             = $this->get_sub_field_value( 'number_range_query_type' );
			$all_items_label        = $this->get_sub_field_value( 'number_range_select_all_items_label' );
			$enable_multiple_filter = $this->get_sub_field_value( 'number_range_enable_multiple_filter' );
			$show_count             = $this->get_sub_field_value( 'number_range_show_count' );
			$get_options            = $this->get_sub_field_value( 'number_get_options' );
			$manual_options         = $this->get_sub_field_value( 'number_manual_options' );
		} elseif ( 'date' === $value_type ) {
			$display_type           = $this->get_sub_field_value( 'date_display_type' );
			$query_type             = $this->get_sub_field_value( 'time_period_query_type' );
			$all_items_label        = $this->get_sub_field_value( 'time_period_select_all_items_label' );
			$enable_multiple_filter = $this->get_sub_field_value( 'time_period_enable_multiple_filter' );
			$show_count             = $this->get_sub_field_value( 'time_period_show_count' );
			$get_options            = 'manual_entry';
			$manual_options         = $this->get_sub_field_value( 'time_period_options' );
		} else {
			$display_type           = $this->get_sub_field_value( 'display_type' );
			$query_type             = $this->get_sub_field_value( 'query_type' );
			$all_items_label        = $this->get_sub_field_value( 'all_items_label' );
			$enable_multiple_filter = $this->get_sub_field_value( 'enable_multiple_filter' );
			$show_count             = $this->get_sub_field_value( 'show_count' );
			$get_options            = $this->get_sub_field_value( 'get_options' );
			$manual_options         = $this->get_sub_field_value( 'manual_options' );
		}

		if ( 'product-status' === $field_type ) {
			$get_options    = 'manual_entry';
			$manual_options = $this->get_sub_field_value( 'product_status_options' );
		} elseif ( 'sort-by' === $field_type ) {
			$get_options    = 'manual_entry';
			$manual_options = $this->get_sub_field_value( 'sort_by_options' );
		} elseif ( 'per-page' === $field_type ) {
			$get_options    = 'manual_entry';
			$manual_options = $this->get_sub_field_value( 'per_page_options' );
		} elseif ( 'rating' === $field_type ) {
			$get_options    = $this->get_sub_field_value( 'number_get_options' );
			$manual_options = $this->get_sub_field_value( 'number_manual_options' );
		}

		// Default is 'automatically'.
		$get_options = 'manual_entry' === $get_options ? 'manual_entry' : 'automatically';

		// Default is an array.
		$manual_options = is_array( $manual_options ) ? $manual_options : array();

		$this->get_options    = $get_options;
		$this->manual_options = $manual_options;

		$_display_type    = $this->parse_display_type( $display_type );
		$_all_items_label = $this->parse_all_items_label( $all_items_label );

		$this->display_type    = $_display_type;
		$this->layout          = $this->get_layout();
		$this->all_items_label = $_all_items_label;
		$this->use_combobox    = $this->get_sub_field_value( 'use_combobox' );
		$this->show_count      = $show_count;
		$this->hide_empty      = $this->get_sub_field_value( 'hide_empty' );

		switch ( $_display_type ) {
			case 'multiselect':
			case 'checkbox':
				$enable_multiple_filter = true;
				break;

			case 'select':
			case 'radio':
			case 'range_number':
			case 'range_slider':
				$enable_multiple_filter = false;
				$query_type             = 'or';
				break;
		}

		if ( ! $enable_multiple_filter ) {
			$query_type = 'or';
		}

		$query_type = apply_filters( 'wcapf_field_instance_query_type', $query_type, $this->instance );

		$enable_multiple_filter = apply_filters(
			'wcapf_field_instance_enable_multiple_filter',
			$enable_multiple_filter,
			$this->instance
		);

		$this->query_type             = $query_type;
		$this->enable_multiple_filter = $enable_multiple_filter;

		$this->filter_id  = $this->get_sub_field_value( 'id' );
		$this->filter_key = $this->get_sub_field_value( 'field_key' );

		$this->filter_type  = $this->get_filter_type();
		$this->taxonomy     = $this->get_taxonomy();
		$this->hierarchical = $this->taxonomy_is_hierarchical();

		$this->enable_hierarchy_accordion = $this->is_hierarchy_accordion_enabled();

		$this->meta_key         = $this->get_meta_key();
		$this->value_type       = $this->get_value_type();
		$this->number_data_type = $this->get_number_data_type();

		$this->post_property = $this->get_post_property();

		$this->use_store_name = $this->is_store_name_enabled();
		$this->use_term_slug  = $this->get_sub_field_value( 'use_term_slug' );

		$this->form_id = $this->get_sub_field_value( 'form_id' );

		$this->enable_search_field  = $this->is_search_field_enabled();
		$this->enable_reduce_height = $this->is_reduce_height_enabled();
		$this->max_height           = $this->filter_options_max_height();
		$this->soft_limit           = $this->no_of_visible_options();
		$this->enable_soft_limit    = $this->is_soft_limit_enabled();
		$this->enable_max_height    = $this->is_max_height_enabled();
	}

	/**
	 * @return string
	 */
	private function get_field_type() {
		return $this->get_sub_field_value( 'type' );
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function get_sub_field_value( $name ) {
		if ( isset( $this->instance[ $name ] ) ) {
			return $this->instance[ $name ];
		}

		return '';
	}

	/**
	 * @return string
	 */
	private function field_default_value_type() {
		$field_type = $this->get_field_type();
		$value_type = $this->get_sub_field_value( 'value_type' );

		if ( 'price' === $field_type ) {
			$value_type = 'number';
		}

		return apply_filters( 'wcapf_field_default_value_type', $value_type, $field_type, $this->instance );
	}

	/**
	 * @param string $display_type
	 *
	 * @return string
	 */
	private function parse_display_type( $display_type ) {
		$available_display_types = array(
			'checkbox'     => array( 'checkbox' ),
			'radio'        => array( 'radio' ),
			'select'       => array( 'select' ),
			'multiselect'  => array( 'multi-select' ),
			'label'        => array( 'label' ),
			'range_slider' => array( 'range_slider' ),
			'range_number' => array( 'range_number' ),
		);

		$available_display_types = apply_filters( 'wcapf_field_display_types', $available_display_types );

		// Default display type.
		if ( 'price' === $this->get_field_type() ) {
			$_display_type = 'range_slider';
		} else {
			$_display_type = 'checkbox';
		}

		foreach ( $available_display_types as $key => $display_types ) {
			if ( in_array( $display_type, $display_types ) ) {
				$_display_type = $key;
				break;
			}
		}

		return $_display_type;
	}

	/**
	 * @param string $all_items_label
	 *
	 * @return string
	 */
	private function parse_all_items_label( $all_items_label ) {
		$type = $this->get_sub_field_value( 'type' );

		if ( ! $all_items_label ) {
			switch ( $type ) {
				case 'sort-by':
				case 'per-page':
					$all_items_label = __( 'Default', 'wc-ajax-product-filter' );

					break;
			}
		}

		return $all_items_label ?: __( 'All Items', 'wc-ajax-product-filter' );
	}

	/**
	 * @since 4.0.0
	 *
	 * @return string
	 */
	private function get_layout() {
		$native_layouts = apply_filters( 'wcapf_native_layouts', array( 'list', 'inline' ) );
		$custom_layouts = apply_filters( 'wcapf_custom_layouts', array( 'inline' ) );

		if ( in_array( $this->display_type, array( 'checkbox', 'radio' ) ) ) {
			$value = $this->get_sub_field_value( 'native_display_type_layout' );

			return in_array( $value, $native_layouts ) ? $value : 'list';
		} else {
			$value = $this->get_sub_field_value( 'custom_display_type_layout' );

			return in_array( $value, $custom_layouts ) ? $value : 'list';
		}
	}

	/**
	 * @return string
	 */
	private function get_filter_type() {
		$field_type  = $this->get_field_type();
		$filter_type = $field_type;

		return apply_filters(
			'wcapf_field_filter_type',
			$filter_type,
			$field_type,
			$this->get_options,
			$this->instance
		);
	}

	/**
	 * @return string
	 */
	private function get_taxonomy() {
		$taxonomy = $this->get_sub_field_value( 'taxonomy' );

		if ( 'rating' === $this->type && 'automatically' == $this->get_options ) {
			$taxonomy = 'product_visibility';
		}

		return $taxonomy;
	}

	/**
	 * @return bool
	 */
	private function taxonomy_is_hierarchical() {
		$taxonomy = $this->get_taxonomy();

		if ( ! $taxonomy ) {
			return false;
		}

		if ( ! is_taxonomy_hierarchical( $taxonomy ) ) {
			return false;
		}

		$non_hierarchical_display_types = array( 'label', 'color', 'image' );

		if ( in_array( $this->display_type, $non_hierarchical_display_types ) ) {
			return false;
		}

		// Disable hierarchy for inline and grid layout.
		if ( in_array( $this->display_type, array( 'checkbox', 'radio' ) ) && 'list' !== $this->layout ) {
			return false;
		}

		return boolval( $this->get_sub_field_value( 'hierarchical' ) );
	}

	/**
	 * @return bool
	 */
	private function is_hierarchy_accordion_enabled() {
		if ( ! $this->taxonomy_is_hierarchical() ) {
			return false;
		}

		$non_hierarchy_accordion_display_types = array( 'select', 'multiselect' );

		if ( in_array( $this->display_type, $non_hierarchy_accordion_display_types ) ) {
			return false;
		}

		return boolval( $this->get_sub_field_value( 'enable_hierarchy_accordion' ) );
	}

	/**
	 * @return string
	 */
	private function get_meta_key() {
		$meta_key = $this->get_sub_field_value( 'meta_key' );

		return apply_filters( 'wcapf_field_meta_key', $meta_key, $this->instance );
	}

	/**
	 * @return string
	 */
	private function get_value_type() {
		$default_value_type = $this->field_default_value_type();

		return apply_filters( 'wcapf_field_value_type', $default_value_type, $this->instance );
	}

	/**
	 * @return string
	 */
	private function get_number_data_type() {
		$data_type = 'SIGNED';

		$field_type = $this->get_field_type();
		$value_type = $this->get_value_type();

		if ( 'price' === $field_type ) {
			$dec_places = absint( wc_get_price_decimals() );

			$data_type = 'DECIMAL(10,' . $dec_places . ')';
		} elseif ( 'number' === $value_type ) {
			if ( '1' === $this->get_sub_field_value( 'value_decimal' ) ) {
				$dec_places = $this->get_sub_field_value( 'value_decimal_places' );

				$data_type = 'DECIMAL(10,' . $dec_places . ')';
			}
		}

		return apply_filters( 'wcapf_number_data_type', $data_type, $this->instance );
	}

	private function get_post_property() {
		$type     = $this->get_field_type();
		$property = '';

		if ( 'post-author' === $type ) {
			$property = 'post_author';
		}

		return $property;
	}

	/**
	 * @since 3.3.0
	 *
	 * @return string
	 */
	private function is_store_name_enabled() {
		if ( ! WCAPF_Helper::is_vendor_plugin_found() ) {
			return '';
		}

		return $this->get_sub_field_value( 'use_store_name' );
	}

	/**
	 * Determines if search field is enabled.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	private function is_search_field_enabled() {
		if ( ! $this->is_search_options_possible() ) {
			return false;
		}

		return $this->get_sub_field_value( 'enable_search_field' );
	}

	/**
	 * Determines if search options is possible according to the display type.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	private function is_search_options_possible() {
		return $this->is_reduce_height_possible( 'search' );
	}

	/**
	 * Determines if reduce height is possible according to the display type.
	 *
	 * @param string $field
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	private function is_reduce_height_possible( $field = 'reduce-height' ) {
		if ( $this->taxonomy_is_hierarchical() && 'search' === $field ) {
			return false;
		}

		if ( 'reduce-height' === $field ) {
			$not_allowed_display_types = array(
				'select',
				'multiselect',
				'range_slider',
				'range_number',
				'range_select',
				'range_multiselect',
				'input_date',
				'input_date_range',
				'time_period_select',
				'time_period_multiselect',
			);
		} else {
			$not_allowed_display_types = array(
				'range_slider',
				'range_number',
				'input_date',
				'input_date_range',
			);
		}

		if ( in_array( $this->display_type, $not_allowed_display_types ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if reduce height enabled.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	private function is_reduce_height_enabled() {
		if ( ! $this->is_reduce_height_possible() ) {
			return false;
		}

		return $this->get_sub_field_value( 'enable_reduce_height' );
	}

	/**
	 * Filter options max height to appear the scrollbar.
	 *
	 * @since 4.0.0
	 *
	 * @return float
	 */
	private function filter_options_max_height() {
		return floatval( $this->get_sub_field_value( 'max_height' ) );
	}

	/**
	 * No of visible options for soft limit.
	 *
	 * @since 4.0.0
	 *
	 * @return int
	 */
	private function no_of_visible_options() {
		return absint( $this->get_sub_field_value( 'soft_limit' ) );
	}

	/**
	 * Determines if soft limit is enabled.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	private function is_soft_limit_enabled() {
		return 'soft_limit' === $this->enable_reduce_height && 1 <= $this->soft_limit;
	}

	/**
	 * Determines if max height is enabled.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	private function is_max_height_enabled() {
		return 'max_height' === $this->enable_reduce_height && 1 <= $this->max_height;
	}

}
