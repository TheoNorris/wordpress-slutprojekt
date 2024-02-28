<?php
/**
 * WCAPF_Walker class.
 *
 * @since      3.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes
 * @author     wptools.io
 */

/**
 * WCAPF_Walker class.
 *
 * @since 3.0.0
 */
class WCAPF_Walker {

	/**
	 * Display type.
	 *
	 * @var string
	 */
	public $display_type;

	/**
	 * Layout.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	public $layout;

	/**
	 * Taxonomy.
	 *
	 * @var string
	 */
	public $taxonomy;

	/**
	 * Is Hierarchical.
	 *
	 * @var string
	 */
	public $hierarchical;

	/**
	 * Determines if we use term slug instead of id in the url.
	 *
	 * @var string
	 */
	public $use_term_slug;

	/**
	 * Enable Hierarchy Accordion.
	 *
	 * @var string
	 */
	public $enable_hierarchy_accordion;

	/**
	 * All items label.
	 *
	 * @var string
	 */
	public $all_items_label;

	/**
	 * Use combobox.
	 *
	 * @var string
	 */
	public $use_combobox;

	/**
	 * Enable multiple filter.
	 *
	 * @var string
	 */
	public $enable_multiple_filter;

	/**
	 * Show count.
	 *
	 * @var string
	 */
	public $show_count;

	/**
	 * Filter key.
	 *
	 * @var string
	 */
	public $filter_key;

	/**
	 * Filter key.
	 *
	 * @var string
	 */
	public $filter_type;

	/**
	 * Field type.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Hide empty.
	 *
	 * @var string
	 */
	public $hide_empty;

	/**
	 * Filter id.
	 *
	 * @var string
	 */
	public $filter_id;

	/**
	 * Get options method.
	 *
	 * @var string
	 */
	public $get_options;

	/**
	 * Determines if filter option tooltip is enabled.
	 *
	 * @var string
	 */
	public $enable_tooltip;

	/**
	 * Determines if we show the product count in filter option tooltip.
	 *
	 * @var string
	 */
	public $show_count_in_tooltip;

	/**
	 * Determines the tooltip position.
	 *
	 * @var string
	 */
	public $tooltip_position;

	/**
	 * Determines if we show a search box to narrow down the filter options.
	 *
	 * @var string
	 */
	public $enable_search_field;

	/**
	 * Search field placeholder.
	 *
	 * @var string
	 */
	public $search_field_placeholder;

	/**
	 * Determines if we activate the soft limit.
	 *
	 * @var string
	 */
	public $enable_soft_limit;

	/**
	 * Determines if we activate the max height.
	 *
	 * @var string
	 */
	public $enable_max_height;

	/**
	 * Determines the no of visible items for soft limit.
	 *
	 * @var string
	 */
	public $soft_limit;

	/**
	 * Determines the filter max height.
	 *
	 * @var string
	 */
	public $max_height;

	/**
	 * The walker items.
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * The css classes to add to the list wrapper element.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	protected $wrapper_classes;

	/**
	 * Determines the soft limit status.
	 *
	 * @var string
	 */
	private $soft_limit_status;

	/**
	 * The active filters.
	 *
	 * @var array
	 */
	private $active_filters;

	/**
	 * The active items.
	 *
	 * @var array
	 */
	private $active_items;

	/**
	 * The active ancestors.
	 *
	 * @var array
	 */
	private $active_ancestors;

	/**
	 * The current queried object data.
	 *
	 * @var array
	 */
	private $queried_object;

	/**
	 * @var WCAPF_URL_Builder
	 */
	private $url_builder;

	/**
	 * The constructor.
	 *
	 * @param WCAPF_Field_Instance $field The field instance.
	 */
	public function __construct( $field ) {
		$this->set_properties( $field );

		$this->active_filters   = $this->active_filters();
		$this->active_items     = $this->active_items();
		$this->active_ancestors = $this->active_ancestors();
		$this->queried_object   = $this->set_queried_object();
		$this->url_builder      = new WCAPF_URL_Builder( $this->filter_key, $this->enable_multiple_filter );
	}

	/**
	 * @param WCAPF_Field_Instance $field The field instance.
	 *
	 * @return void
	 */
	private function set_properties( $field ) {
		$default_properties = array(
			'display_type'               => '',
			'layout'                     => '',
			'taxonomy'                   => '',
			'hierarchical'               => '',
			'use_term_slug'              => '',
			'enable_hierarchy_accordion' => '',
			'all_items_label'            => '',
			'use_combobox'               => '',
			'no_results_message'         => '',
			'enable_multiple_filter'     => '',
			'show_count'                 => '',
			'filter_key'                 => '',
			'filter_type'                => '',
			'type'                       => '',
			'hide_empty'                 => '',
			'filter_id'                  => '',
			'get_options'                => '',
			'custom_appearance_options'  => array(),
			'enable_tooltip'             => '',
			'show_count_in_tooltip'      => '',
			'tooltip_position'           => '',
			'enable_search_field'        => '',
			'search_field_placeholder'   => '',
			'enable_soft_limit'          => '',
			'enable_max_height'          => '',
			'soft_limit'                 => '',
			'max_height'                 => '',
		);

		foreach ( $default_properties as $key => $value ) {
			if ( isset( $field->$key ) ) {
				$property_value = $field->$key;
			} else {
				$property_value = $field->get_sub_field_value( $key );
			}

			$this->$key = $property_value;
		}

		$this->items = $this->set_items( $field );
	}

	/**
	 * @param WCAPF_Field_Instance $field_instance
	 *
	 * @return array
	 */
	protected function set_items( $field_instance ) {
		$type  = $this->filter_type;
		$items = array();

		switch ( $type ) {
			case 'taxonomy':
				$field = new WCAPF_Filter_Type_Taxonomy( $field_instance );
				$items = $field->get_items();

				break;

			case 'product-status':
				$filter = new WCAPF_Filter_Type_Product_Status( $field_instance );
				$items  = $filter->get_items();

				break;

			case 'post-author':
				$filter = new WCAPF_Filter_Type_Post_Author( $field_instance );
				$items  = $filter->get_items();

				break;

			case 'post-meta':
				$filter = new WCAPF_Filter_Type_Post_Meta( $field_instance );
				$items  = $filter->get_items();

				break;
		}

		return $items;
	}

	/**
	 * The active filters.
	 *
	 * @return array
	 */
	private function active_filters() {
		$filter_key  = $this->filter_key;
		$filter_type = $this->filter_type;

		$chosen_filters = WCAPF_Helper::get_chosen_filters();

		// We have grouped the filters based on filter types.
		$filters = isset( $chosen_filters[ $filter_type ] ) ? $chosen_filters[ $filter_type ] : array();

		$filter_data = isset( $filters[ $filter_key ] ) ? $filters[ $filter_key ] : array();

		/**
		 * Register a hook to add the sort-by, per-page filter data.
		 */
		return apply_filters( 'wcapf_walker_filter_data', $filter_data, $filter_type );
	}

	/**
	 * The active items.
	 *
	 * @return array|string[]
	 */
	private function active_items() {
		$active_filters = $this->active_filters;

		return isset( $active_filters['values'] ) ? $active_filters['values'] : array();
	}

	/**
	 * The active ancestors.
	 *
	 * @return array
	 */
	private function active_ancestors() {
		$active_filters = $this->active_filters;

		return isset( $active_filters['active_ancestors'] ) ? $active_filters['active_ancestors'] : array();
	}

	/**
	 * The queried object that will be used to disable the current term and parent terms.
	 *
	 * @since 4.0.0
	 *
	 * @return array|null
	 */
	private function set_queried_object() {
		if ( is_tax() ) {
			$object   = get_queried_object();
			$taxonomy = $this->taxonomy;

			if ( 'taxonomy' === $this->filter_type && $taxonomy === $object->taxonomy ) {
				$term_id   = $object->term_id;
				$ancestors = get_ancestors( $term_id, $taxonomy );

				return array(
					'term_id'   => $term_id,
					'ancestors' => $ancestors,
				);
			}
		}

		return null;
	}

	/**
	 * Build the menu.
	 */
	public function build_menu() {
		$items        = $this->items;
		$display_type = $this->display_type;
		$show_count   = $this->show_count;

		if ( ! $items ) {
			return '<div>' . esc_html( WCAPF_Helper::empty_filter_text() ) . '</div>';
		}

		if ( 'radio' === $display_type || 'select' === $display_type ) {
			$all_items = array(
				array(
					'id'    => '',
					'name'  => $this->all_items_label,
					'count' => '-1',
					'depth' => 1,
				),
			);

			$items = array_merge( $all_items, $items );
		}

		$items = apply_filters( 'wcapf_menu_items', $items, $this );

		if ( $this->enable_soft_limit ) {
			$this->soft_limit_status = $this->get_soft_limit_status( $items );
		}

		$dropdown_types    = array( 'select', 'multiselect' );
		$native_list_types = array( 'checkbox', 'radio' );
		$wrapper_classes   = array();

		if ( in_array( $display_type, $dropdown_types ) ) {
			$wrapper_classes[] = 'wcapf-dropdown-wrapper';
		} else {
			$wrapper_classes[] = 'wcapf-list-wrapper';

			if ( in_array( $display_type, $native_list_types ) ) {
				$wrapper_classes[] = 'list-type-native';
				$wrapper_classes[] = 'display-type-' . $display_type;

				// Stylish checkbox and radio.
				if ( ! empty( WCAPF_Helper::wcapf_option( 'stylish_checkbox_radio' ) ) ) {
					$wrapper_classes[] = 'stylish-checkbox-radio';
				}

				// Hierarchy List.
				if ( $this->hierarchical && $this->enable_hierarchy_accordion ) {
					$wrapper_classes[] = 'hierarchy-list';

					if ( WCAPF_Helper::wcapf_option( 'hierarchy_toggle_at_end' ) ) {
						$wrapper_classes[] = 'hierarchy-toggle-at-end';
					}
				}
			} else {
				$input_type = $this->enable_multiple_filter ? 'checkbox' : 'radio';

				$wrapper_classes[] = 'list-type-custom-' . $input_type;
				$wrapper_classes[] = 'display-type-' . $display_type;

				if ( 'label' === $display_type ) {
					$wrapper_classes[] = 'default-primary-style';
				}

				if ( $show_count ) {
					$wrapper_classes[] = 'show-count';
				}
			}

			$wrapper_classes[] = 'layout-' . $this->layout;
		}

		if ( in_array( $display_type, $native_list_types ) ) {
			if ( $this->hierarchical ) {
				$html = $this->get_max_height_wrapper_start_markup();
				$html .= $this->build_hierarchical_menu( $items );
				$html .= $this->get_max_height_wrapper_end_markup();
			} else {
				$html = $this->build_non_hierarchical_menu( $items );
			}
		} elseif ( in_array( $display_type, $dropdown_types ) ) {
			$html = $this->build_dropdown_menu( $items );
		} else {
			$html = $this->build_non_hierarchical_menu( $items );
		}

		if ( 'visible' === $this->soft_limit_status ) {
			$wrapper_classes[] = 'show-hidden-options';
		}

		if ( $this->wrapper_classes ) {
			$wrapper_classes[] = $this->wrapper_classes;
		}

		$wrapper_classes = apply_filters( 'wcapf_walker_wrapper_class', $wrapper_classes, $this );
		$wrapper_classes = implode( ' ', $wrapper_classes );

		$menu = '<div class="' . esc_attr( $wrapper_classes ) . '">';
		$menu .= $html;
		$menu .= '</div>';

		return $menu;
	}

	private function get_soft_limit_status( $items ) {
		$status              = 'hidden';
		$no_of_visible_items = $this->soft_limit;
		$active_items        = $this->active_items;

		if ( ! $active_items ) {
			return $status;
		}

		$index = 0;

		foreach ( $items as $item ) {
			$index ++;

			if ( in_array( $item['id'], $active_items ) ) {
				if ( $index > $no_of_visible_items ) {
					$status = 'visible';
					break;
				}
			}
		}

		return $status;
	}

	/**
	 * @since 4.0.0
	 *
	 * @return string
	 */
	private function get_max_height_wrapper_start_markup() {
		$html = '';

		if ( $this->enable_max_height ) {
			$html .= '<div class="wcapf-enable-scrollbar" style="max-height: ' . $this->max_height . 'px;">';
		}

		return $html;
	}

	/**
	 * Build the hierarchical menu.
	 *
	 * @param array $items  The array of items.
	 * @param array $parent The parent item data.
	 *
	 * @return string
	 */
	private function build_hierarchical_menu( $items, $parent = array() ) {
		$html = '';

		if ( $items ) {
			$size      = count( $items );
			$increment = 0;

			foreach ( $items as $item ) {
				$increment ++;

				if ( 1 === $increment ) {
					$html .= $this->hierarchical_menu_start_markup( $parent );
				}

				$has_children = isset( $item['children'] );

				$html .= '<li class="wcapf-filter-option">';
				$html .= $this->menu_item( $item, $has_children );

				if ( $has_children ) {
					$html .= $this->build_hierarchical_menu( $item['children'], $item );
				}

				$html .= '</li>';

				if ( $increment === $size ) {
					$html .= '</ul>';
				}
			}
		}

		return $html;
	}

	/**
	 * Hierarchical menu's start markup.
	 *
	 * @param array $parent The parent item.
	 *
	 * @return string
	 */
	private function hierarchical_menu_start_markup( $parent ) {
		$classes = 'wcapf-filter-options';
		$attrs   = '';

		if ( $parent ) {
			if ( $this->hierarchical && $this->enable_hierarchy_accordion ) {
				if ( $this->item_active_as_ancestor( $parent ) ) {
					$attrs .= ' style="display: block;"';
				} else {
					$attrs .= ' style="display: none;"';
				}
			}
		}

		return '<ul class="' . $classes . '"' . $attrs . '>';
	}

	/**
	 * Checks if the given item is active as ancestor.
	 *
	 * @param array $item The item data.
	 *
	 * @return bool
	 */
	private function item_active_as_ancestor( $item ) {
		$item_value = $this->item_value( $item );

		if ( in_array( $item_value, $this->active_ancestors ) ) {
			return true;
		}

		if ( $this->queried_object && in_array( $item['id'], $this->queried_object['ancestors'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Gets the item value, either id or slug.
	 *
	 * @param array $item The item data.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	private function item_value( $item ) {
		$item_id   = $item['id'];
		$item_slug = isset( $item['slug'] ) ? $item['slug'] : '';

		if ( 'taxonomy' !== $this->type ) {
			return $item_id;
		}

		if ( ! $this->use_term_slug ) {
			return $item_id;
		}

		if ( ! $item_slug ) {
			return $item_id;
		}

		return $item_slug;
	}

	/**
	 * Menu item.
	 *
	 * @param array $item         The item array.
	 * @param bool  $has_children Determines if we insert the accordion toggle.
	 *
	 * @return string
	 */
	private function menu_item( $item, $has_children = false ) {
		$html         = '';
		$attrs        = '';
		$input_markup = '';
		$display_type = $this->display_type;
		$filter_key   = $this->filter_key;
		$item_id      = $item['id'];
		$item_value   = $this->item_value( $item );
		$item_active  = $this->item_active( $item );
		$input_name   = $filter_key;

		$count_allowed = $this->count_allowed();

		$item_active_as_current_query = $this->queried_object && $item_id == $this->queried_object['term_id'];

		if ( $item_active || $item_active_as_current_query ) {
			$attrs .= ' checked="checked"';
		}

		$item_active_as_ancestors = $this->queried_object && in_array( $item_id, $this->queried_object['ancestors'] );

		if ( $item_active_as_current_query || $item_active_as_ancestors ) {
			$attrs .= ' disabled="disabled"';

			$input_name .= '-disabled';
		} elseif ( 'disable' === $this->hide_empty && 0 == $item['count'] && $count_allowed ) {
			$attrs .= ' disabled="disabled"';
		}

		$unique_id  = $filter_key . '-' . $item_id . '-' . $this->filter_id;
		$filter_url = $this->url_builder->get_filter_url( $item_value, $item_active );

		if ( 'checkbox' === $display_type || 'radio' === $display_type ) {
			$input_type = $display_type;
		} else {
			$input_type = 'checkbox';
		}

		$input_markup .= '<input type="' . esc_attr( $input_type ) . '"';
		$input_markup .= ' id="' . esc_attr( $unique_id ) . '"';
		$input_markup .= ' name="' . esc_attr( $input_name ) . '"';
		$input_markup .= ' value="' . esc_attr( $item_value ) . '"';
		$input_markup .= ' data-url="' . esc_url( $filter_url ) . '"';
		$input_markup .= ' aria-invalid="false"';

		/**
		 * Why does the checkbox stay checked when reloading the page?
		 *
		 * @source https://stackoverflow.com/a/471140
		 */
		$input_markup .= ' autocomplete="off"';

		$input_markup .= $attrs . '>';

		$tooltip_data = '';

		if ( $this->enable_tooltip ) {
			$tooltip_content = ! empty( $item['tooltip'] ) ? $item['tooltip'] : $item['name'];

			if ( $this->show_count_in_tooltip && '-1' !== $item['count'] && $count_allowed ) {
				$tooltip_content .= ' (' . $item['count'] . ')';
			}

			$tooltip_data = ' data-wcapf-tooltip-' . $this->tooltip_position . '="' . esc_attr( $tooltip_content ) . '"';
		}

		$item_classes = 'wcapf-filter-item';

		if ( 0 == $item['count'] && $count_allowed ) {
			$item_classes .= ' empty-item';
		}

		if ( $item_active_as_current_query ) {
			$item_classes .= ' current-tax-item';
		}

		if ( $item_active_as_ancestors ) {
			$item_classes .= ' active-as-ancestor';
		}

		if ( $item_active ) {
			$item_classes .= ' item-active';
		}

		$html .= '<div class="' . esc_attr( $item_classes ) . '">';
		$html .= '<label for="' . esc_attr( $unique_id ) . '"' . $tooltip_data . '>';
		$html .= $input_markup;
		$html .= $this->menu_item_inner( $item );
		$html .= '</label>';

		if ( $has_children ) {
			$html .= $this->get_hierarchy_accordion_html( $item, $unique_id );
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Checks if the given item is active.
	 *
	 * @param array $item The item data.
	 *
	 * @return bool
	 */
	private function item_active( $item ) {
		$active_items = $this->active_items;
		$item_value   = $this->item_value( $item );

		if ( $active_items ) {
			if ( in_array( $item_value, $active_items ) ) {
				return true;
			}
		} else {
			if ( '-1' === $item['count'] ) {
				return true;
			}
		}

		return false;
	}

	private function count_allowed() {
		return ! in_array( $this->filter_type, array( 'sort-by', 'per-page' ) );
	}

	/**
	 * Menu item inner content.
	 *
	 * @param array $item The item data.
	 *
	 * @return string
	 */
	protected function menu_item_inner( $item ) {
		$item['show_count']    = $this->show_count && '-1' !== $item['count'] && $this->count_allowed();
		$item['enable_search'] = $this->enable_search_field;

		return WCAPF_Template_Loader::get_instance()->load( 'menu-item', array( 'item' => $item ), false );
	}

	/**
	 * @param array  $item      The item data.
	 * @param string $unique_id The unique identifier.
	 *
	 * @return string
	 */
	private function get_hierarchy_accordion_html( $item, $unique_id ) {
		$html = '';

		if ( $this->hierarchical && $this->enable_hierarchy_accordion ) {
			$is_active = 'false';

			if ( $this->item_active_as_ancestor( $item ) ) {
				$is_active = 'true';
			}

			$aria_label = sprintf( __( 'Expand/Collapse %s', 'wc-ajax-product-filter' ), $item['name'] );

			return WCAPF_Template_Loader::get_instance()->load(
				'hierarchy-accordion',
				array(
					'is_active'  => $is_active,
					'aria_label' => $aria_label,
					'unique_id'  => $unique_id,
				),
				false
			);
		}

		return $html;
	}

	/**
	 * @since 4.0.0
	 *
	 * @return string
	 */
	private function get_max_height_wrapper_end_markup() {
		$html = '';

		if ( $this->enable_max_height ) {
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Build non-hierarchical menu.
	 *
	 * @param array $items The array of items.
	 */
	private function build_non_hierarchical_menu( $items ) {
		$html  = '';
		$index = 0;

		if ( ! $items ) {
			return $html;
		}

		$visible_items = $this->soft_limit;

		// Show the search field before the filter options.
		$html .= $this->get_search_field();

		$html .= $this->get_max_height_wrapper_start_markup();

		$html .= '<ul class="wcapf-filter-options">';

		foreach ( $items as $item ) {
			$index ++;

			$item_classes = 'wcapf-filter-option';

			if ( $this->enable_soft_limit && $index > $visible_items ) {
				$item_classes .= ' wcapf-filter-option-hidden';
			}

			$html .= '<li class="' . $item_classes . '">';
			$html .= $this->menu_item( $item );
			$html .= '</li>';
		}

		$html .= '</ul>';

		$html .= $this->get_max_height_wrapper_end_markup();

		if ( count( $items ) > $visible_items ) {
			$html .= $this->get_soft_limit_toggle();
		}

		if ( $this->enable_search_field ) {
			$html .= '<div class="wcapf-no-results-text" style="display: none;">';
			$html .= esc_html( WCAPF_Helper::no_results_text() ) . ' <span></span>';
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Gets the search field html markup.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	private function get_search_field() {
		if ( ! $this->enable_search_field ) {
			return '';
		}

		if ( $this->search_field_placeholder ) {
			$placeholder = $this->search_field_placeholder;
		} else {
			$placeholder = WCAPF_Helper::wcapf_option(
				'search_field_default_placeholder',
				__( 'Search', 'wc-ajax-product-filter' )
			);
		}

		return WCAPF_Template_Loader::get_instance()->load(
			'search-box',
			array(
				'placeholder'   => $placeholder,
				'value'         => '',
				'icon_position' => 'right',
				'with_cross'    => true,
			),
			false
		);
	}

	/**
	 * Gets the html markup of soft limit toggle.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	private function get_soft_limit_toggle() {
		$html = '';

		if ( $this->enable_soft_limit ) {
			$is_active = 'false';

			if ( 'visible' === $this->soft_limit_status ) {
				$is_active = 'true';
			}

			$show_more = WCAPF_Helper::wcapf_option(
				'show_more_btn_label',
				__( '+ Show more', 'wc-ajax-product-filter' )
			);

			$show_less = WCAPF_Helper::wcapf_option(
				'show_less_btn_label',
				__( '− Show less', 'wc-ajax-product-filter' )
			);

			$html .= '<div class="wcapf-soft-limit-wrapper">';
			$html .= '<span';
			$html .= ' class="wcapf-soft-limit-trigger"';
			$html .= ' role="button" aria-pressed="' . $is_active . '" tabindex="0"';
			$html .= ' aria-label="' . esc_attr__( 'Show/hide all options', 'wc-ajax-product-filter' ) . '"';
			$html .= '>';
			$html .= '<span aria-hidden="true" class="wcapf-show-more">' . esc_html( $show_more ) . '</span>';
			$html .= '<span aria-hidden="true" class="wcapf-show-less">' . esc_html( $show_less ) . '</span>';
			$html .= '</span>';
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Build the dropdown menu.
	 *
	 * @param array $items The array of items.
	 */
	private function build_dropdown_menu( $items ) {
		$display_type   = $this->display_type;
		$input_name     = $this->filter_key;
		$input_multiple = '';
		$input_attrs    = '';

		if ( 'multiselect' === $display_type ) {
			$input_multiple = ' multiple="multiple"';
		}

		$use_combobox = $this->use_combobox;

		if ( $use_combobox ) {
			$input_classes = 'wcapf-chosen';
		} else {
			$input_classes = 'wcapf-select';

			if ( ! empty( WCAPF_Helper::wcapf_option( 'improve_native_select' ) ) ) {
				$input_classes .= ' wcapf-select-improved';

				if ( 'multiselect' === $display_type ) {
					$input_classes .= ' wcapf-select-multiple';
				}
			}
		}

		$all_items_label = $this->all_items_label;

		if ( 'multiselect' === $display_type && $use_combobox ) {
			$input_classes .= ' default-primary-style';
		}

		if ( 'multiselect' === $display_type ) {
			$input_attrs .= ' data-placeholder="' . esc_attr( $all_items_label ) . '"';

			if ( $use_combobox && $this->hierarchical ) {
				$input_classes .= ' has-hierarchy';
			}
		}

		if ( 'select' === $display_type && $use_combobox && $this->enable_search_field ) {
			$input_attrs .= ' data-enable-search="1"';
		}

		if ( $use_combobox && $this->show_count && $this->count_allowed() ) {
			$input_classes .= ' with-count';
		}

		$filter_url       = $this->url_builder->get_filter_url_with_placeholder();
		$clear_filter_url = $this->url_builder->get_clear_filter_url();

		$input_attrs .= ' data-url="' . esc_url( $filter_url ) . '"';
		$input_attrs .= ' data-clear-filter-url="' . esc_url( $clear_filter_url ) . '"';

		$html = '<select class="' . $input_classes . '"';
		$html .= ' name="' . esc_attr( $input_name ) . '"';
		$html .= $input_attrs;
		$html .= $input_multiple;
		$html .= '>';

		foreach ( $items as $item ) {
			$html .= $this->dropdown_item( $item );
		}

		$html .= '</select>';

		return $html;
	}

	/**
	 * Dropdown item.
	 *
	 * @param array $item The item array.
	 *
	 * @return string
	 */
	private function dropdown_item( $item ) {
		$count_allowed = $this->count_allowed();
		$use_combobox  = $this->use_combobox;

		$attrs = $this->get_dropdown_item_attributes( $item );

		$option = '<option' . $attrs . '>';

		if ( $this->hierarchical && ! $use_combobox ) {
			$option .= $this->dropdown_item_indent( $item );
		}

		$option .= $item['name'];

		$count      = $item['count'];
		$item_count = ' (' . $count . ')';

		if ( ! $use_combobox && $this->show_count && '-1' !== $count && $count_allowed ) {
			$option .= $item_count;
		}

		$option .= '</option>';

		$inner = apply_filters( 'wcapf_dropdown_item', $option, $item, $this );

		$children = isset( $item['children'] ) ? $item['children'] : array();

		if ( $children ) {
			foreach ( $children as $child_item ) {
				$inner .= $this->dropdown_item( $child_item );
			}
		}

		return $inner;
	}

	/**
	 * @param array $item
	 *
	 * @return string
	 */
	protected function get_dropdown_item_attributes( $item ) {
		$item_id       = $item['id'];
		$item_value    = $this->item_value( $item );
		$item_active   = $this->item_active( $item );
		$count_allowed = $this->count_allowed();

		$attrs   = '';
		$classes = array();

		if ( $item_active ) {
			$attrs .= ' selected="selected"';
		}

		$item_active_as_current_query = $this->queried_object && $item_id == $this->queried_object['term_id'];
		$item_active_as_ancestors     = $this->queried_object && in_array( $item_id, $this->queried_object['ancestors'] );

		if ( $item_active_as_current_query || $item_active_as_ancestors ) {
			$attrs .= ' disabled="disabled"';
		} elseif ( 'disable' === $this->hide_empty && 0 == $item['count'] && $count_allowed ) {
			$attrs .= ' disabled="disabled"';
		}

		$attrs .= ' value="' . esc_attr( $item_value ) . '"';

		if ( $this->hierarchical ) {
			$classes[] = 'depth-' . $item['depth'];
		}

		if ( 0 == $item['count'] && $count_allowed ) {
			$classes[] = 'empty-item';
		}

		if ( $item_active_as_current_query ) {
			$classes[] = 'current-tax-item';
		}

		if ( $item_active_as_ancestors ) {
			$classes[] = 'active-as-ancestor';
		}

		if ( $classes ) {
			$attrs .= ' class="' . implode( ' ', $classes ) . '"';
		}

		$count = $item['count'];

		$use_combobox = $this->use_combobox;

		$item_count = ' (' . $count . ')';

		if ( $use_combobox && $this->show_count && $count_allowed ) {
			// Add the empty attribute to avoid the undefined issue in js.
			if ( '-1' === $count ) {
				$item_count = '';
			}

			$attrs .= ' data-count="' . $count . '"';
			$attrs .= ' data-count-markup="' . $item_count . '"';
		}

		return $attrs;
	}

	/**
	 * Indent for hierarchy in dropdown.
	 *
	 * @param array $item The item array.
	 *
	 * @return string
	 */
	private function dropdown_item_indent( $item ) {
		$indent  = '';
		$_indent = apply_filters( 'wcapf_dropdown_item_indent_content', '&nbsp;&nbsp;&nbsp;&nbsp;' );
		$depth   = $item['depth'];

		while ( $depth > 1 ) {
			$indent .= $_indent;
			$depth --;
		}

		return $indent;
	}

}
