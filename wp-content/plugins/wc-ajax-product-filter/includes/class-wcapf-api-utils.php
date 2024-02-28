<?php
/**
 * The api utility class.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes
 * @author     wptools.io
 */

/**
 * WCAPF_API_Utils class.
 *
 * @since 4.0.0
 */
class WCAPF_API_Utils {

	public static function get_global_filter_key( $filter_data ) {
		$filter_keys     = self::get_filter_keys();
		$filter_type     = isset( $filter_data['type'] ) ? $filter_data['type'] : '';
		$filter_taxonomy = isset( $filter_data['taxonomy'] ) ? $filter_data['taxonomy'] : '';
		$filter_meta_key = isset( $filter_data['meta_key'] ) ? $filter_data['meta_key'] : '';
		$filter_key      = '';

		foreach ( $filter_keys as $data ) {
			$type      = isset( $data['type'] ) ? $data['type'] : '';
			$taxonomy  = isset( $data['taxonomy'] ) ? $data['taxonomy'] : '';
			$meta_key  = isset( $data['meta_key'] ) ? $data['meta_key'] : '';
			$field_key = isset( $data['field_key'] ) ? $data['field_key'] : '';

			if ( $type === $filter_type ) {
				if ( 'taxonomy' === $type ) {
					if ( $filter_taxonomy === $taxonomy ) {
						$filter_key = $field_key;

						break;
					}
				} elseif ( 'post-meta' === $type ) {
					if ( $filter_meta_key === $meta_key ) {
						$filter_key = $field_key;

						break;
					}
				} else {
					$filter_key = $field_key;

					break;
				}
			}
		}

		return $filter_key;
	}

	/**
	 * @param $global
	 *
	 * @return array
	 */
	public static function get_filter_keys( $global = false ) {
		$filters = get_posts(
			array(
				'post_type'   => 'wcapf-filter',
				'post_status' => 'publish',
				'nopaging'    => true,
				'order'       => 'ASC',
			)
		);

		$filter_keys = array();

		$filter_types       = self::get_filter_types();
		$taxonomy_options   = self::get_available_taxonomies();
		$global_filter_keys = array();

		foreach ( $filters as $filter ) {
			$filter_id    = $filter->ID;
			$filter_key   = $filter->post_name;
			$post_excerpt = html_entity_decode( $filter->post_excerpt );
			$filter_data  = explode( '>', $post_excerpt );
			$type         = isset( $filter_data[0] ) ? $filter_data[0] : '';
			$filter_type  = $type;
			$property     = isset( $filter_data[1] ) ? $filter_data[1] : '';

			if ( 'component' === $type ) {
				continue;
			}

			$data = array(
				'id'        => $filter_id,
				'type'      => $filter_type,
				'field_key' => $filter_key,
			);

			if ( 'taxonomy' === $type ) {
				$data['taxonomy'] = $property;
			} else if ( 'post-meta' === $type ) {
				$data['meta_key'] = $property;
			}

			if ( $global ) {
				unset( $data['id'] );

				if ( 'taxonomy' === $type ) {
					$data_index    = array_search( $property, array_column( $taxonomy_options, 'value' ) );
					$taxonomy_data = $taxonomy_options[ $data_index ];
					$data['label'] = $taxonomy_data['label'];
				} else {
					$data_index   = array_search( $type, array_column( $filter_types, 'value' ) );
					$_filter_data = $filter_types[ $data_index ];

					$label = $_filter_data['label'];

					if ( 'post-meta' === $type ) {
						$label .= '[' . $property . ']';
					}

					$data['label'] = $label;
				}

				$data['secondary_type'] = $post_excerpt;
				$data['_field_key']     = $filter_key; // Keep a backup for updating purposes.

				if ( ! in_array( $post_excerpt, $global_filter_keys ) ) {
					$filter_keys[] = $data;

					$global_filter_keys[] = $post_excerpt;
				}
			} else {
				$filter_keys[] = $data;
			}
		}

		return apply_filters( 'wcapf_filter_keys', $filter_keys, $global );
	}

	/**
	 * Gets the filter types.
	 *
	 * @return array[]
	 */
	public static function get_filter_types() {
		return array(
			array(
				'label'   => __( 'Taxonomy', 'wc-ajax-product-filter' ),
				'value'   => 'taxonomy',
				'options' => self::get_available_taxonomies(),
			),
			array(
				'label' => __( 'Price', 'wc-ajax-product-filter' ),
				'value' => 'price',
				'key'   => 'price',
			),
			array(
				'label' => __( 'Rating', 'wc-ajax-product-filter' ),
				'value' => 'rating',
				'key'   => 'rating',
			),
			array(
				'label' => __( 'Product Status', 'wc-ajax-product-filter' ),
				'value' => 'product-status',
				'key'   => 'product-status',
			),
			array(
				'label' => __( 'Post Author', 'wc-ajax-product-filter' ),
				'value' => 'post-author',
				'key'   => 'post-author',
			),
			array(
				'label' => __( 'Post Meta', 'wc-ajax-product-filter' ),
				'value' => 'post-meta',
			),
			array(
				'label' => __( 'Keyword', 'wc-ajax-product-filter' ),
				'value' => 'keyword',
				'key'   => 'keyword',
			),
			array(
				'label' => __( 'Sort By', 'wc-ajax-product-filter' ),
				'value' => 'sort-by',
				'key'   => 'sort-by',
				'isPro' => true,
			),
			array(
				'label' => __( 'Per Page', 'wc-ajax-product-filter' ),
				'value' => 'per-page',
				'key'   => 'per-page',
				'isPro' => true,
			),
			array(
				'components' => true,
				'value'      => 'component',
				'options'    => array(
					array(
						'label' => __( 'Active Filters', 'wc-ajax-product-filter' ),
						'value' => 'active-filters',
						'type'  => 'component',
					),
					array(
						'label' => __( 'Reset Button', 'wc-ajax-product-filter' ),
						'value' => 'reset-button',
						'type'  => 'component',
					),
					array(
						'label' => __( 'Results Count', 'wc-ajax-product-filter' ),
						'value' => 'results-count',
						'type'  => 'component',
						'isPro' => true,
					),
					// array(
					// 	'label' => __( 'Apply Mode', 'wc-ajax-product-filter' ),
					// 	'value' => 'apply-mode',
					// 	'type'  => 'component',
					// 	'isPro' => true,
					// ),
					// array(
					// 	'label' => __( 'Submit Mode', 'wc-ajax-product-filter' ),
					// 	'value' => 'submit-mode',
					// 	'type'  => 'component',
					// 	'isPro' => true,
					// ),
				),
			),
		);
	}

	/**
	 * Gets the available taxonomies after sorting them.
	 *
	 * @param bool $only_with_archive Whether to return the taxonomies with archive enabled or not.
	 *
	 * @return array
	 */
	public static function get_available_taxonomies( $only_with_archive = false ) {
		$tax_data   = get_object_taxonomies( 'product', 'objects' );
		$taxonomies = array();

		$main_taxonomies     = array( 'product_cat', 'product_tag' );
		$optional_taxonomies = array( 'product_type', 'product_visibility', 'product_shipping_class' );
		$attributes          = wc_get_attribute_taxonomy_names();
		$array               = array_merge( $main_taxonomies, $optional_taxonomies, $attributes );
		$others              = array();

		foreach ( $tax_data as $taxonomy ) {
			$name = $taxonomy->name;

			if ( ! in_array( $name, $array ) ) {
				$others[] = $name;
			}
		}

		$final_array = array_merge( $main_taxonomies, $attributes, $others, $optional_taxonomies );

		foreach ( $final_array as $name ) {
			if ( $only_with_archive && ( ! is_taxonomy_viewable( $name ) || 'product_shipping_class' === $name ) ) {
				continue;
			}

			if ( in_array( $name, $main_taxonomies ) || in_array( $name, $optional_taxonomies ) ) {
				$default_filter_key = str_replace( '_', '-', $name );
			} elseif ( in_array( $name, $attributes ) ) {
				$default_filter_key = str_replace( 'pa_', '', $name );
			} else {
				// Check if pro version found and pretty url is enabled then don't add the underscore.
				$default_filter_key = '_' . $name;
			}

			$taxonomies[] = array(
				'label'           => $tax_data[ $name ]->label,
				'value'           => $name,
				'type'            => 'taxonomy',
				'taxHierarchical' => is_taxonomy_hierarchical( $name ),
				'key'             => $default_filter_key,
			);
		}

		return $taxonomies;
	}

	public static function display_date_formats() {
		return apply_filters(
			'wcapf_display_date_formats',
			array(
				array(
					'label' => current_time( 'd-m-Y' ) . ' (d-m-Y)',
					'value' => 'dd-mm-yy',
				),
				array(
					'label' => current_time( 'Y-m-d' ) . ' (Y-m-d)',
					'value' => 'yy-mm-dd',
				),
			)
		);
	}

	public static function product_status_options() {
		return apply_filters(
			'wcapf_product_status_options',
			array(
				array(
					'label' => __( 'Featured', 'wc-ajax-product-filter' ),
					'value' => 'featured',
				),
				array(
					'label' => __( 'On Sale', 'wc-ajax-product-filter' ),
					'value' => 'on_sale',
				),
			)
		);
	}

	/**
	 * @return array
	 */
	public static function time_period_options() {
		$_time_period_options = WCAPF_Helper::get_time_period_options();
		$time_period_options  = array();

		foreach ( $_time_period_options as $time_period_key => $time_period_label ) {
			$time_period_options[] = array(
				'label' => $time_period_label,
				'value' => $time_period_key,
			);
		}

		return $time_period_options;
	}

	/**
	 * @return array
	 */
	public static function sort_by_options() {
		return apply_filters(
			'wcapf_sort_by_options',
			array(
				array(
					'label' => __( 'Post ID', 'wc-ajax-product-filter' ),
					'value' => 'ID',
				),
				array(
					'label' => __( 'Author', 'wc-ajax-product-filter' ),
					'value' => 'author',
				),
				array(
					'label' => __( 'Title', 'wc-ajax-product-filter' ),
					'value' => 'title',
				),
				array(
					'label' => __( 'Name (Post Slug)', 'wc-ajax-product-filter' ),
					'value' => 'name',
				),
				array(
					'label' => __( 'Date', 'wc-ajax-product-filter' ),
					'value' => 'date',
				),
				array(
					'label' => __( 'Modified', 'wc-ajax-product-filter' ),
					'value' => 'modified',
				),
				array(
					'label' => __( 'Parent ID', 'wc-ajax-product-filter' ),
					'value' => 'parent',
				),
				array(
					'label' => __( 'Random Order', 'wc-ajax-product-filter' ),
					'value' => 'rand',
				),
				array(
					'label' => __( 'Review Count', 'wc-ajax-product-filter' ),
					'value' => 'review_count',
				),
				array(
					'label' => __( 'Menu Order', 'wc-ajax-product-filter' ),
					'value' => 'menu_order',
				),
				array(
					'label' => __( 'Total Sales', 'wc-ajax-product-filter' ),
					'value' => 'total_sales',
				),
				array(
					'label' => __( 'Average Rating', 'wc-ajax-product-filter' ),
					'value' => 'average_rating',
				),
				array(
					'label' => __( 'Price', 'wc-ajax-product-filter' ),
					'value' => 'price',
				),
				array(
					'label' => __( 'Meta Value', 'wc-ajax-product-filter' ),
					'value' => 'meta_value',
				),
			)
		);
	}

	/**
	 * Gets the meta types.
	 *
	 * @return array
	 */
	public static function meta_type_options() {
		return apply_filters(
			'wcapf_meta_type_options',
			array(
				array(
					'value' => 'alphabetic',
					'label' => __( 'Alphabetic', 'wc-ajax-product-filter' ),
					'cast'  => 'CHAR',
				),
				array(
					'value' => 'numeric',
					'label' => __( 'Numeric', 'wc-ajax-product-filter' ),
					'cast'  => 'NUMERIC',
				),
				array(
					'value' => 'date',
					'label' => __( 'Date', 'wc-ajax-product-filter' ),
					'cast'  => 'DATE',
				),
				array(
					'value' => 'datetime',
					'label' => __( 'DateTime', 'wc-ajax-product-filter' ),
					'cast'  => 'DATETIME',
				),
				array(
					'value' => 'decimal',
					'label' => __( 'Decimal', 'wc-ajax-product-filter' ),
					'cast'  => 'DECIMAL',
				),
			)
		);
	}

	public static function user_role_options() {
		$_user_roles = WCAPF_Product_Filter_Utils::get_user_roles();
		$user_roles  = array();

		foreach ( $_user_roles as $role_value => $role_label ) {
			$user_roles[] = array(
				'label' => $role_label,
				'value' => $role_value,
			);
		}

		return $user_roles;
	}

	public static function sanitize_manual_option_data( $data ) {
		$sanitized = array();

		foreach ( $data as $key => $_value ) {
			if ( in_array( $key, array( 'label', 'secondary_label', 'tooltip' ) ) ) {
				$value = wp_kses_post( $_value );
			} else {
				$value = sanitize_text_field( $_value );
			}

			$sanitized[ $key ] = $value;
		}

		return $sanitized;
	}

	/**
	 * Gets the plugin settings for our React UI.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = WCAPF_Helper::get_settings();

		// Send the author roles with labels.
		if ( ! empty ( $settings['author_roles'] ) ) {
			$array       = WCAPF_Product_Filter_Utils::get_user_roles();
			$with_labels = array();

			foreach ( $settings['author_roles'] as $role_name ) {
				$with_labels[] = array(
					'label' => $array[ $role_name ],
					'value' => $role_name,
				);
			}

			$settings['author_roles'] = $with_labels;
		}

		return apply_filters( 'wcapf_parse_settings', $settings );
	}

	/**
	 * Gets the forms for our React UI.
	 *
	 * @return array
	 */
	public static function get_forms() {
		$args = array(
			'post_type'   => 'wcapf-form',
			'nopaging'    => true,
			'post_status' => 'publish',
		);

		$posts = get_posts( $args );
		$forms = array();

		foreach ( $posts as $post ) {
			$forms[] = self::get_form_data( $post );
		}

		return $forms;
	}

	/**
	 * Gets the form data for given id.
	 *
	 * @param int|WP_Post $post Post ID or post object.
	 *
	 * @return array
	 */
	public static function get_form_data( $post ) {
		if ( $post instanceof WP_Post ) {
			$_post = $post;
		} else {
			$_post = get_post( $post );
		}

		$id = $_post->ID;

		$data = array(
			'id'    => $id,
			'title' => $_post->post_title,
		);

		/**
		 * Register a hook to send the form locations with the data.
		 */
		return apply_filters( 'wcapf_admin_form_data', $data, $id );
	}

}
