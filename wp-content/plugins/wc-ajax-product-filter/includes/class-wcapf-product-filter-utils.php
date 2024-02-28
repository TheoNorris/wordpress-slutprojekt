<?php
/**
 * The product filter utility class.
 *
 * @since      3.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes
 * @author     wptools.io
 */

/**
 * WCAPF_Product_Filter_Utils class.
 *
 * @since 3.0.0
 */
class WCAPF_Product_Filter_Utils {

	/**
	 * Gets the chosen filter values.
	 *
	 * @param string $values The filter values.
	 *
	 * @return array
	 */
	public static function get_chosen_filter_values( $values ) {
		$value_separator = ',';

		// Check if we have any string(including 0) in the url.
		if ( ! strlen( $values ) ) {
			return array();
		}

		$values_array = explode( $value_separator, $values );

		return is_array( $values_array ) ? $values_array : array();
	}

	/**
	 * We are using the same join to avoid the sorting issues.
	 *
	 * @return array
	 *
	 * @see WC_Query::append_product_sorting_table_join()
	 */
	public static function get_lookup_table_data() {
		global $wpdb;

		$table = $wpdb->prefix . 'wc_product_meta_lookup';
		$alias = 'wc_product_meta_lookup';

		$join = "LEFT JOIN $table AS $alias ON $wpdb->posts.ID = $alias.product_id";

		return compact( 'join', 'alias' );
	}

	/**
	 * When filtering the products by price we set the price according to tax.
	 *
	 * @param string $min
	 * @param string $max
	 *
	 * @return array
	 */
	public static function get_min_max_price_according_to_tax( $min, $max ) {
		$min = floatval( $min );
		$max = floatval( $max );

		/**
		 * Adjust if the store taxes are not displayed how they are stored.
		 * Kicks in when prices excluding tax are displayed including tax.
		 */
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
			$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
			$tax_rates = WC_Tax::get_rates( $tax_class );

			if ( $tax_rates ) {
				$min -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $min, $tax_rates ) );
				$max -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $max, $tax_rates ) );
			}
		}

		return compact( 'min', 'max' );
	}

	/**
	 * @return string
	 */
	public static function get_join_clause() {
		$filter = new WCAPF_Product_Filter();

		$join = ' ' . $filter->get_full_join_clause();

		return apply_filters( 'wcapf_filter_join_clause', $join );
	}

	/**
	 * @return string
	 */
	public static function get_where_clause( $query_type, $filter_key ) {
		$main_query_type = WCAPF_Helper::get_field_relations();

		$where = '';

		if ( 'and' === $main_query_type ) {
			if ( 'and' === $query_type ) {
				$where = self::get_full_where_clause();
			} elseif ( 'or' === $query_type ) {
				$where = self::get_where_clauses_by_other_filters( $filter_key );
			}
		} elseif ( 'or' === $main_query_type ) {
			if ( 'and' === $query_type ) {
				$where = self::get_self_where_clause( $filter_key );
			} elseif ( 'or' === $query_type ) {
				$where = ' AND 1=1';
			}
		}

		return apply_filters( 'wcapf_filter_where_clause', $where, $query_type, $filter_key );
	}

	/**
	 * @return string
	 */
	public static function get_full_where_clause() {
		$filter = new WCAPF_Product_Filter();

		return $filter->get_full_where_clause();
	}

	/**
	 * @return string
	 */
	public static function get_where_clauses_by_other_filters( $filter_key ) {
		$chosen_filters = WCAPF_Helper::get_chosen_filters();

		$wheres = array();

		foreach ( $chosen_filters as $filter_type => $filter_type_filters ) {
			if ( 'filters_data' === $filter_type ) {
				continue;
			}

			foreach ( $filter_type_filters as $_filter_key => $filter ) {
				if ( $filter_key === $_filter_key ) {
					continue;
				}

				$wheres[] = $filter['where'];
			}
		}

		$query_type = WCAPF_Helper::get_field_relations();

		return WCAPF_Product_Filter_Utils::combine_where_clauses( $wheres, $query_type );
	}

	/**
	 * @param array  $wheres
	 * @param string $query_type
	 *
	 * @return string
	 */
	public static function combine_where_clauses( $wheres, $query_type ) {
		if ( 'or' === $query_type ) {
			$separator = ' OR ';
		} else {
			$separator = ' AND ';
		}

		if ( $wheres ) {
			if ( 1 < count( $wheres ) ) {
				$sql = ' AND ( ' . implode( $separator, $wheres ) . ' )';
			} else {
				$sql = ' AND ' . implode( $separator, $wheres );
			}
		} else {
			$sql = '';
		}

		return $sql;
	}

	/**
	 * @return string
	 */
	public static function get_self_where_clause( $filter_key ) {
		$chosen_filters = WCAPF_Helper::get_chosen_filters();

		$wheres = array();

		foreach ( $chosen_filters as $filter_type => $filter_type_filters ) {
			if ( 'filters_data' === $filter_type ) {
				continue;
			}

			foreach ( $filter_type_filters as $_filter_key => $filter ) {
				if ( $filter_key === $_filter_key ) {
					$wheres[] = $filter['where'];
					break;
				}
			}
		}

		$query_type = WCAPF_Helper::get_field_relations();

		return WCAPF_Product_Filter_Utils::combine_where_clauses( $wheres, $query_type );
	}

	/**
	 * @return string
	 */
	public static function get_featured_product_ids_sql() {
		$ids = wc_get_featured_product_ids();

		$ids = $ids ?: array( 0 );

		return self::get_ids_sql( $ids );
	}

	/**
	 * Formats a list of ids as "(id,id,id)".
	 *
	 * @param array $ids The list of ids to format.
	 *
	 * @return string The formatted list.
	 */
	public static function get_ids_sql( $ids ) {
		return '(' . implode( ',', array_map( 'absint', $ids ) ) . ')';
	}

	/**
	 * Formats a list of term_taxonomy_ids as "(id,id,id)" from term ids.
	 *
	 * Fix - https://wordpress.org/support/topic/selection-does-not-show/
	 *
	 * @param int[]  $term_ids The array of term ids.
	 * @param string $taxonomy The taxonomy name.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function get_tt_ids_sql( $term_ids, $taxonomy ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'include'    => $term_ids,
			)
		);

		if ( $terms ) {
			$tt_ids = wp_list_pluck( $terms, 'term_taxonomy_id' );
		} else {
			$tt_ids = array( 0 );
		}

		return self::get_ids_sql( $tt_ids );
	}

	/**
	 * @return string
	 */
	public static function get_product_ids_on_sale_sql() {
		$ids = wc_get_product_ids_on_sale();

		$ids = $ids ?: array( 0 );

		return self::get_ids_sql( $ids );
	}

	/**
	 * Create a unique alias when joining on MySQL table.
	 *
	 * @param string $filter_key The filter key.
	 *
	 * @return string
	 */
	public static function get_table_join_alias( $filter_key ) {
		return '`' . $filter_key . '`';
	}

	/**
	 * Create a unique alias when joining on MySQL table.
	 *
	 * @param int    $index      The index number.
	 * @param string $filter_key The filter key.
	 *
	 * @return string
	 */
	public static function get_table_join_alias_for_query_type_and( $index, $filter_key ) {
		$postfix = $index + 1;

		return '`' . $filter_key . $postfix . '`';
	}

	/**
	 * Gets the user roles.
	 *
	 * @since 3.3.0
	 *
	 * @return array
	 */
	public static function get_user_roles() {
		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$all_roles = $wp_roles->get_names();

		return apply_filters( 'wcapf_user_roles', $all_roles );
	}

	/**
	 * Adjust the parent term id for hierarchy terms.
	 *
	 * @param WCAPF_Field_Instance $field_instance
	 * @param array                $terms
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public static function adjust_parent_id_for_hierarchy_terms( $field_instance, $terms ) {
		$modified = array();
		$taxonomy = $field_instance->taxonomy;

		foreach ( $terms as $_id => $_data ) {
			if ( ! array_key_exists( $_data['parent_id'], $terms ) ) {
				$ancestors = get_ancestors( $_id, $taxonomy );
				$parent_id = 0;

				foreach ( $ancestors as $ancestor ) {
					if ( array_key_exists( $ancestor, $terms ) ) {
						$parent_id = $ancestor;
						break;
					}
				}

				$_data['parent_id'] = $parent_id;
			}

			$modified[ $_id ] = $_data;
		}

		return $modified;
	}

	/**
	 * Prepare the form filters and return them to be used on the frontend.
	 *
	 * @param WP_Post $form The form.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public static function get_form_filters( $form ) {
		$form_data     = array();
		$form_id       = $form->ID;
		$form_settings = maybe_unserialize( $form->post_content );

		$filters = get_posts(
			array(
				'post_type'   => 'wcapf-filter',
				'post_status' => 'publish',
				'post_parent' => $form_id,
				'nopaging'    => true,
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
			)
		);

		$form_data['form_id']    = $form_id;
		$form_data['form_title'] = $form->post_title;
		$form_data['settings']   = $form_settings;

		$form_filters = array();

		/**
		 * Register a hook to modify the form filter data.
		 */
		$filter_data = apply_filters( 'wcapf_form_filter_data', array(), $form_data );

		foreach ( $filters as $filter ) {
			$id    = $filter->ID;
			$field = maybe_unserialize( $filter->post_content );

			$field['form_id'] = $form_id;

			$field = wp_parse_args( $filter_data, $field );

			$form_filters[ $id ] = array(
				'id'    => $id,
				'title' => $filter->post_title,
				'key'   => $filter->post_name,
				'type'  => $filter->post_excerpt,
				'field' => $field,
			);
		}

		$form_data['filters'] = $form_filters;

		return $form_data;
	}

}
