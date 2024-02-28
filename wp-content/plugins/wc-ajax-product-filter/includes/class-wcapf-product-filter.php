<?php
/**
 * The product filter class.
 *
 * @since      3.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes
 * @author     wptools.io
 */

/**
 * WCAPF_Product_Filter class.
 *
 * @since 3.0.0
 */
class WCAPF_Product_Filter {

	public function get_full_join_clause() {
		$post_clauses = $this->get_full_join_n_where_clauses();
		$joins        = $post_clauses['joins'];

		$full_join = implode( ' ', $joins );

		return $full_join ? ' ' . $full_join : '';
	}

	/**
	 * Gets the full join n where clauses for the main filter.
	 *
	 * @return array
	 */
	private function get_full_join_n_where_clauses() {
		$joins  = array();
		$wheres = array();

		$chosen_filters = WCAPF_Helper::get_chosen_filters();

		foreach ( $chosen_filters as $filter_type => $filter_type_filters ) {
			if ( 'filters_data' === $filter_type ) {
				continue;
			}

			foreach ( $filter_type_filters as $filters ) {
				$join  = $filters['join'];
				$where = $filters['where'];

				$joins[]  = $join;
				$wheres[] = $where;
			}
		}

		return compact( 'joins', 'wheres' );
	}

	/**
	 * Gets the chosen filters from url. Avoid calling this method using this class instance,
	 * rather use the helper class instance.
	 *
	 * @return array
	 *
	 * @see WCAPF_Helper::get_chosen_filters()
	 */
	public function get_chosen_filters() {
		// parse url
		$url = $_SERVER['QUERY_STRING'];
		parse_str( $url, $query );

		$chosen      = array();
		$taxonomies  = array();
		$price       = array();
		$statuses    = array();
		$post_author = array();
		$post_metas  = array();
		$keyword     = array();

		$filters_data = $this->filters_data( $query );

		foreach ( $filters_data as $filter_key => $field ) {
			$field_instance = new WCAPF_Field_Instance( $field );
			$filter_type    = $field_instance->filter_type;
			$filter_values  = sanitize_text_field( $query[ $filter_key ] ); // Sanitize the value that comes from the url.

			if ( 'taxonomy' === $filter_type ) {
				$taxonomy = $field_instance->taxonomy;

				if ( in_array( $taxonomy, wc_get_attribute_taxonomy_names() ) ) {
					$taxonomies[ $filter_key ] = $this->set_attribute_filter_data( $filter_values, $field_instance );
				} else {
					$taxonomies[ $filter_key ] = $this->set_taxonomy_filter_data( $filter_values, $field_instance );
				}
			} elseif ( 'price' === $filter_type ) {
				$price[ $filter_key ] = $this->set_price_filter_data( $filter_values, $field_instance );
			} elseif ( 'product-status' === $filter_type ) {
				$statuses[ $filter_key ] = $this->set_filter_by_product_status_data( $filter_values, $field_instance );
			} elseif ( 'post-author' === $filter_type ) {
				$post_author[ $filter_key ] = $this->set_filter_by_post_author_data( $filter_values, $field_instance );
			} elseif ( 'post-meta' === $filter_type ) {
				$post_metas[ $filter_key ] = $this->set_filter_by_post_meta_data( $filter_values, $field_instance );
			} elseif ( 'keyword' === $filter_type ) {
				$keyword = $this->set_keyword_filter_data( $filter_values, $field_instance );
			}
		}

		$chosen['taxonomy'] = $taxonomies;
		$chosen['price']    = $price;

		$chosen['product-status'] = $statuses;
		$chosen['post-author']    = $post_author;
		$chosen['post-meta']      = $post_metas;

		if ( $keyword ) {
			$chosen['filters_data']['keyword'] = $keyword;
		}

		$chosen = $this->set_default_sorting_data( $chosen );

		/**
		 * Register a hook to add the sort-by, per-page filters data.
		 */
		return apply_filters( 'wcapf_chosen_filters', $chosen, $filters_data, $query );
	}

	/**
	 * Gets the active filter's data.
	 *
	 * @param array $query The key, values array from the $_GET variable.
	 *
	 * @return array
	 */
	protected function filters_data( $query ) {
		$assigned = $this->get_relevant_form();

		if ( ! $assigned ) {
			return array();
		}

		$settings = WCAPF_Helper::get_settings();

		if ( ! empty( $settings['disable_wcapf'] ) ) {
			return array();
		}

		$global_keys = WCAPF_API_Utils::get_filter_keys( true );
		$global_keys = wp_list_pluck( $global_keys, 'field_key' );

		$settings['filter_keys'] = $global_keys;

		if ( is_shop() ) {
			$settings['base_url'] = get_permalink( wc_get_page_id( 'shop' ) );
		} else {
			$settings['base_url'] = get_term_link( get_queried_object_id() );
		}

		global $wcapf, $wcapf_form;

		$wcapf      = $settings;
		$form       = WCAPF_Product_Filter_Utils::get_form_filters( $assigned );
		$wcapf_form = $form;

		$filters_data = array();

		if ( $query ) {
			$filter_keys = array_keys( $query );
			$filter_keys = array_map( 'sanitize_title', $filter_keys );

			foreach ( $form['filters'] as $filter ) {
				$field_key = $filter['key'];

				if ( in_array( $field_key, $filter_keys ) ) {
					$filters_data[ $field_key ] = $filter['field'];
				}
			}
		}

		return $filters_data;
	}

	/**
	 * Gets the assigned form.
	 *
	 * @since 4.0.0
	 *
	 * @return array|int|WP_Post
	 */
	private function get_relevant_form() {
		$form_data = array();

		$forms = get_posts(
			array(
				'post_type'      => 'wcapf-form',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'orderby'        => 'ID',
				'order'          => 'ASC',
			)
		);

		if ( ! $forms ) {
			return $form_data;
		}

		return $forms[0];
	}

	/**
	 * @param string               $filter_value   The filter value.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	protected function set_attribute_filter_data( $filter_value, $field_instance ) {
		$use_attribute_table = WCAPF_Helper::filtering_via_lookup_table_is_active();

		if ( ! $use_attribute_table ) {
			return $this->set_taxonomy_filter_data( $filter_value, $field_instance );
		}

		global $wpdb;

		$query_type = $field_instance->query_type;
		$taxonomy   = $field_instance->taxonomy;
		$filter_id  = $field_instance->filter_id;

		$join = '';

		$utils = new WCAPF_Product_Filter_Utils;

		$filter_values = $utils::get_chosen_filter_values( $filter_value );

		$term_ids = $this->get_chosen_term_ids( $filter_values, $field_instance );

		$lookup_table_name = $wpdb->prefix . 'wc_product_attributes_lookup';

		// The extra derived table ("SELECT product_or_parent_id FROM") is needed for performance
		// (causes the filtering subquery to be executed only once).
		$clause_root = "$wpdb->posts.ID IN ( SELECT product_or_parent_id FROM (";
		$clause_end  = ') AS temp)';

		$hide_stock_out_items = WCAPF_Helper::hide_stock_out_items();

		if ( $hide_stock_out_items ) {
			$in_stock_clause = ' AND in_stock = 1';
		} else {
			$in_stock_clause = '';
		}

		if ( 'or' === $query_type ) {
			if ( $term_ids ) {
				$ids = $utils::get_ids_sql( $term_ids );

				$where = "
					$clause_root
					SELECT product_or_parent_id
					FROM $lookup_table_name lt
					WHERE term_id IN $ids
					$in_stock_clause
					$clause_end
				";
			} else {
				$where = '1=0';
			}
		} else {
			$clauses = array();

			foreach ( $term_ids as $term_id ) {
				$clauses[] = "
					$clause_root
					SELECT product_or_parent_id
					FROM $lookup_table_name lt
					WHERE term_id = $term_id
					$in_stock_clause
					$clause_end
				";
			}

			if ( $clauses ) {
				$sql = implode( ' AND ', $clauses );

				if ( 1 < count( $clauses ) ) {
					$where = '( ' . $sql . ' )';
				} else {
					$where = $sql;
				}
			} else {
				$where = '1=0';
			}
		}

		$active_filters = $this->active_terms_filter_data( $filter_values, $field_instance );

		return array(
			'taxonomy'       => $taxonomy,
			'query_type'     => $query_type,
			'values'         => $filter_values,
			'join'           => $join,
			'where'          => $where,
			'filter_id'      => $filter_id,
			'active_filters' => $active_filters,
		);
	}

	/**
	 * @param string               $filter_value   The filter value.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	protected function set_taxonomy_filter_data( $filter_value, $field_instance ) {
		global $wpdb;

		$filter_key = $field_instance->filter_key;
		$query_type = $field_instance->query_type;
		$taxonomy   = $field_instance->taxonomy;
		$filter_id  = $field_instance->filter_id;

		$utils = new WCAPF_Product_Filter_Utils;

		$filter_values = $utils::get_chosen_filter_values( $filter_value );

		$term_ids   = $this->get_chosen_term_ids( $filter_values, $field_instance );
		$_terms_ids = $term_ids;

		$is_hierarchical = is_taxonomy_hierarchical( $taxonomy );

		$join = '';

		if ( 'or' === $query_type ) {
			$join_alias = $utils::get_table_join_alias( $filter_key );

			$join .= "LEFT JOIN $wpdb->term_relationships AS $join_alias ON $wpdb->posts.ID = $join_alias.object_id";
		} else {
			foreach ( $term_ids as $index => $value ) {
				$join_alias = $utils::get_table_join_alias_for_query_type_and( $index, $filter_key );

				$join .= " LEFT JOIN $wpdb->term_relationships AS $join_alias ON $wpdb->posts.ID = $join_alias.object_id";
			}
		}

		if ( 'or' === $query_type ) {
			if ( $is_hierarchical ) {
				$with_children = array();

				foreach ( $term_ids as $term_id ) {
					$term_children = get_term_children( $term_id, $taxonomy );
					$with_children = array_merge( $with_children, $term_children );
				}

				$term_ids = array_merge( $term_ids, $with_children );
			}

			if ( $term_ids ) {
				$join_alias = $utils::get_table_join_alias( $filter_key );

				$ids   = $utils::get_tt_ids_sql( $term_ids, $taxonomy );
				$where = "$join_alias.term_taxonomy_id IN $ids";
			} else {
				$where = '1=0';
			}
		} else {
			$clauses = array();

			foreach ( $term_ids as $index => $term_id ) {
				if ( $is_hierarchical ) {
					$with_children = get_term_children( $term_id, $taxonomy );

					$and_term_id = array_merge( array( $term_id ), $with_children );
				} else {
					$and_term_id = array( $term_id );
				}

				$id_sql = $utils::get_tt_ids_sql( $and_term_id, $taxonomy );

				$join_alias = $utils::get_table_join_alias_for_query_type_and( $index, $filter_key );

				$clauses[] = "$join_alias.term_taxonomy_id IN $id_sql";
			}

			if ( $clauses ) {
				$sql = implode( ' AND ', $clauses );

				if ( 1 < count( $clauses ) ) {
					$where = '( ' . $sql . ' )';
				} else {
					$where = $sql;
				}
			} else {
				$where = '1=0';
			}
		}

		$active_ancestors = array();

		if ( $is_hierarchical ) {
			$active_ancestors = $this->get_ancestors_of_active_terms( $_terms_ids, $field_instance );
		}

		$active_filters = $this->active_terms_filter_data( $filter_values, $field_instance );

		return array(
			'taxonomy'         => $taxonomy,
			'query_type'       => $query_type,
			'values'           => $filter_values,
			'active_ancestors' => $active_ancestors,
			'join'             => $join,
			'where'            => $where,
			'filter_id'        => $filter_id,
			'active_filters'   => $active_filters,
		);
	}

	/**
	 * @param array                $filter_values  The filter values.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	private function get_chosen_term_ids( $filter_values, $field_instance ) {
		/**
		 * The hook to change the values for the rating filter and get the values from term slugs.
		 */
		return apply_filters( 'wcapf_taxonomy_filter_values', $filter_values, $field_instance );
	}

	/**
	 * @param array                $term_ids       The term ids.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	private function get_ancestors_of_active_terms( $term_ids, $field_instance ) {
		$taxonomy = $field_instance->taxonomy;

		$active_ancestors = array();

		foreach ( $term_ids as $term_id ) {
			$ancestors        = get_ancestors( $term_id, $taxonomy );
			$active_ancestors = array_merge( $ancestors, $active_ancestors );
		}

		return apply_filters( 'wcapf_ancestors_of_active_terms', $active_ancestors, $field_instance );
	}

	/**
	 * @param array                $filter_values  The filter values.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	protected function active_terms_filter_data( $filter_values, $field_instance ) {
		if ( ! $field_instance->get_sub_field_value( 'show_in_active_filters' ) ) {
			return array();
		}

		$taxonomy = $field_instance->taxonomy;

		$term_ids = array_map( 'absint', $filter_values );

		$filter_data = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'include'    => $term_ids,
				'orderby'    => 'include',
				'fields'     => 'id=>name',
			)
		);

		/**
		 * The hook to change the filter data for the rating filter.
		 */
		return apply_filters( 'wcapf_active_taxonomy_filter_data', $filter_data, $field_instance, $filter_values );
	}

	/**
	 * @param string               $filter_value   The filter value.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	protected function set_price_filter_data( $filter_value, $field_instance ) {
		$query_type = $field_instance->query_type;
		$filter_id  = $field_instance->filter_id;

		$active_filters = array();

		$utils = new WCAPF_Product_Filter_Utils;

		list( 'join' => $join, 'alias' => $alias ) = $utils::get_lookup_table_data();

		$_filter_values = $utils::get_chosen_filter_values( $filter_value );
		$_filter_values = $_filter_values ? $_filter_values[0] : array(); // Pick the first range only.

		$separator = WCAPF_Helper::range_values_separator();

		$filter_values = explode( $separator, $_filter_values );

		if ( ! $filter_values ) {
			return array();
		}

		if ( 2 > count( $filter_values ) ) {
			return array();
		}

		list( 'min' => $min, 'max' => $max ) = $utils::get_min_max_price_according_to_tax(
			$filter_values[0],
			$filter_values[1]
		);

		$where = "NOT ($max < $alias.min_price OR $min > $alias.max_price)";

		if ( $field_instance->get_sub_field_value( 'show_in_active_filters' ) ) {
			$range_id = implode( $separator, $filter_values );

			$active_filters[ $range_id ] = $this->get_label_for_number_range( $field_instance, $range_id );
		}

		return array(
			'query_type'     => $query_type,
			'values'         => $filter_values,
			'join'           => $join,
			'where'          => $where,
			'filter_id'      => $filter_id,
			'active_filters' => $active_filters,
		);
	}

	/**
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 * @param string               $value          The min, max range using separator.
	 *
	 * @return string
	 */
	protected function get_label_for_number_range( $field_instance, $value ) {
		$separator = WCAPF_Helper::range_values_separator();
		$range     = explode( $separator, $value );

		$range_min = isset( $range[0] ) ? $range[0] : '';
		$range_max = isset( $range[1] ) ? $range[1] : '';

		$value_prefix       = $field_instance->get_sub_field_value( 'value_prefix' );
		$value_postfix      = $field_instance->get_sub_field_value( 'value_postfix' );
		$values_separator   = $field_instance->get_sub_field_value( 'values_separator' );
		$format_numbers     = $field_instance->get_sub_field_value( 'format_numbers' );
		$decimal_places     = $field_instance->get_sub_field_value( 'decimal_places' );
		$thousand_separator = $field_instance->get_sub_field_value( 'thousand_separator' );
		$decimal_separator  = $field_instance->get_sub_field_value( 'decimal_separator' );

		if ( $format_numbers ) {
			$range_min = number_format( $range_min, $decimal_places, $decimal_separator, $thousand_separator );
			$range_max = number_format( $range_max, $decimal_places, $decimal_separator, $thousand_separator );
		}

		return sprintf(
			'%1$s%2$s%3$s%4$s%1$s%5$s%3$s',
			$value_prefix,
			$range_min,
			$value_postfix,
			$values_separator,
			$range_max
		);
	}

	/**
	 * @param string               $filter_value   The filter value.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	protected function set_filter_by_product_status_data( $filter_value, $field_instance ) {
		$query_type = $field_instance->query_type;
		$filter_id  = $field_instance->filter_id;

		$active_filters = array();

		$utils = new WCAPF_Product_Filter_Utils;

		$filter_values = $utils::get_chosen_filter_values( $filter_value );

		global $wpdb;

		$join   = '';
		$wheres = array();

		foreach ( $filter_values as $value ) {
			$condition = '';

			if ( 'featured' === $value ) {
				$featured_products = $utils::get_featured_product_ids_sql();

				$condition = "$wpdb->posts.ID IN $featured_products";
			} elseif ( 'on_sale' === $value ) {
				$on_sale_products = $utils::get_product_ids_on_sale_sql();

				$condition = "$wpdb->posts.ID IN $on_sale_products";
			}

			if ( $condition ) {
				$wheres[] = $condition;
			}
		}

		if ( $wheres ) {
			if ( 'or' === $query_type ) {
				$separator = ' OR ';
			} else {
				$separator = ' AND ';
			}

			$sql = implode( $separator, $wheres );

			if ( 1 < count( $wheres ) ) {
				$where = '( ' . $sql . ' )';
			} else {
				$where = $sql;
			}
		} else {
			$where = '1=0';
		}

		if ( $field_instance->get_sub_field_value( 'show_in_active_filters' ) ) {
			$active_filters = $this->active_product_status_filter_data( $filter_values, $field_instance );
		}

		return array(
			'query_type'     => $query_type,
			'values'         => $filter_values,
			'join'           => $join,
			'where'          => $where,
			'filter_id'      => $filter_id,
			'active_filters' => $active_filters,
		);
	}

	/**
	 * @param array                $filter_values  The filter values.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	private function active_product_status_filter_data( $filter_values, $field_instance ) {
		$options = wp_list_pluck( $field_instance->manual_options, 'label', 'value' );

		$filter_data = array();

		foreach ( $filter_values as $filter_value ) {
			$filter_data[ $filter_value ] = ! empty( $options[ $filter_value ] ) ? $options[ $filter_value ] : $filter_value;
		}

		return $filter_data;
	}

	/**
	 * @param string               $filter_value   The filter value.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	private function set_filter_by_post_author_data( $filter_value, $field_instance ) {
		$query_type = $field_instance->query_type;
		$property   = $field_instance->post_property;
		$filter_id  = $field_instance->filter_id;

		$active_filters = array();

		global $wpdb;

		$join             = '';
		$clauses          = array();
		$or_filter_values = array();

		$utils = new WCAPF_Product_Filter_Utils;

		$alias = $utils::get_table_join_alias( $property );

		$value_alias = "$wpdb->posts.$alias";

		$property_values = $utils::get_chosen_filter_values( $filter_value );

		foreach ( $property_values as $value ) {
			// Active filters data.
			if ( $field_instance->get_sub_field_value( 'show_in_active_filters' ) ) {
				$active_filters[ $value ] = $this->get_label_for_post_author( $value, $field_instance );
			}

			if ( 'or' === $query_type ) {
				$or_filter_values[] = $value;
			} else {
				$clauses[] = "$value_alias = '$value'";
			}
		}

		$where = $this->get_where_clauses( $query_type, $or_filter_values, $value_alias, $clauses );

		return array(
			'query_type'     => $query_type,
			'values'         => $property_values,
			'join'           => $join,
			'where'          => $where,
			'filter_id'      => $filter_id,
			'active_filters' => $active_filters,
		);
	}

	/**
	 * @param string               $value
	 * @param WCAPF_Field_Instance $field_instance
	 *
	 * @return string
	 */
	protected function get_label_for_post_author( $value, $field_instance ) {
		$args = array(
			'fields'  => array( 'ID', 'display_name' ),
			'include' => array( $value ),
			'number'  => 1,
		);

		$users = get_users( $args );

		/**
		 * @var WP_User $user
		 */
		$user = $users[0];

		return $user->display_name;
	}

	/**
	 * @param string $query_type
	 * @param array  $or_filter_values
	 * @param string $value_alias
	 * @param array  $clauses
	 *
	 * @return string
	 */
	protected function get_where_clauses( $query_type, $or_filter_values, $value_alias, $clauses ) {
		if ( 'or' === $query_type ) {
			if ( $or_filter_values ) {
				$or_clauses = "('" . implode( "','", $or_filter_values ) . "')";

				$where = "$value_alias IN $or_clauses";
			} elseif ( $clauses ) {
				if ( 1 < count( $clauses ) ) {
					$where = '( ' . implode( ' OR ', $clauses ) . ' )';
				} else {
					$where = implode( ' OR ', $clauses );
				}
			} else {
				$where = '1=0';
			}
		} else {
			if ( $clauses ) {
				if ( 1 < count( $clauses ) ) {
					$where = '( ' . implode( ' AND ', $clauses ) . ' )';
				} else {
					$where = implode( ' AND ', $clauses );
				}
			} else {
				$where = '1=0';
			}
		}

		return $where;
	}

	/**
	 * @param string               $filter_value
	 * @param WCAPF_Field_Instance $field_instance
	 *
	 * @return array
	 */
	protected function set_filter_by_post_meta_data( $filter_value, $field_instance ) {
		$filter_key = $field_instance->filter_key;
		$query_type = $field_instance->query_type;
		$filter_id  = $field_instance->filter_id;

		$active_filters = array();

		$utils = new WCAPF_Product_Filter_Utils;

		$filter_values = $utils::get_chosen_filter_values( $filter_value );

		$clauses          = array();
		$or_filter_values = array();

		$join = $this->get_post_meta_join_clause( $filter_values, $field_instance );

		$value_alias = $utils::get_table_join_alias( $filter_key ) . '.meta_value';

		foreach ( $filter_values as $index => $meta_value ) {
			// Active filters data.
			if ( $field_instance->get_sub_field_value( 'show_in_active_filters' ) ) {
				$active_filters[ $meta_value ] = $meta_value;
			}

			if ( 'or' === $query_type ) {
				$or_filter_values[] = $meta_value;
			} else {
				$join_alias = $utils::get_table_join_alias_for_query_type_and( $index, $filter_key );

				$value_alias = "$join_alias.meta_value";

				$clauses[] = "$value_alias = '$meta_value'";
			}
		}

		$where = $this->get_where_clauses( $query_type, $or_filter_values, $value_alias, $clauses );

		return array(
			'query_type'     => $query_type,
			'values'         => $filter_values,
			'join'           => $join,
			'where'          => $where,
			'filter_id'      => $filter_id,
			'active_filters' => $active_filters,
		);
	}

	/**
	 * @param array                $filter_values
	 * @param WCAPF_Field_Instance $field_instance
	 *
	 * @return string
	 */
	public function get_post_meta_join_clause( $filter_values, $field_instance ) {
		global $wpdb;

		$query_type = $field_instance->query_type;
		$filter_key = $field_instance->filter_key;
		$meta_key   = $field_instance->meta_key;

		$join = '';

		if ( 'and' === $query_type ) {
			foreach ( $filter_values as $index => $meta_value ) {
				$join_alias = WCAPF_Product_Filter_Utils::get_table_join_alias_for_query_type_and( $index, $filter_key );

				$join .= " LEFT JOIN $wpdb->postmeta AS $join_alias ON ($wpdb->posts.ID = $join_alias.post_id";
				$join .= " AND $join_alias.meta_key = '$meta_key')";
			}
		} else {
			$join_alias = WCAPF_Product_Filter_Utils::get_table_join_alias( $filter_key );

			$join .= "LEFT JOIN $wpdb->postmeta AS $join_alias ON ($wpdb->posts.ID = $join_alias.post_id";
			$join .= " AND $join_alias.meta_key = '$meta_key')";
		}

		return $join;
	}

	/**
	 * Sets the keyword filter data.
	 *
	 * @param string               $filter_value
	 * @param WCAPF_Field_Instance $field_instance
	 *
	 * @since 4.1.0
	 *
	 * @return array
	 */
	public function set_keyword_filter_data( $filter_value, $field_instance ) {
		$filter_key     = $field_instance->filter_key;
		$filter_id      = $field_instance->filter_id;
		$active_filters = array();

		// Here, it is possible to apply multiple keywords separated by comma or space.
		$filter_values = array( $filter_value );

		$prefix = WCAPF_Helper::keyword_filter_prefix();

		$active_filters[ $filter_value ] = $prefix . ' ' . $filter_value;

		return array(
			'values'         => $filter_values,
			'filter_key'     => $filter_key,
			'filter_id'      => $filter_id,
			'active_filters' => $active_filters,
		);
	}

	/**
	 * Sets data for the default sorting.
	 *
	 * @param array $chosen The chosen filters array.
	 *
	 * @return array
	 */
	private function set_default_sorting_data( $chosen ) {
		if ( empty( WCAPF_Helper::wcapf_option( 'sorting_data_in_active_filters' ) ) ) {
			return $chosen;
		}

		if ( isset( $_GET['orderby'] ) ) {
			$values = WCAPF_Product_Filter_Utils::get_chosen_filter_values( sanitize_key( $_GET['orderby'] ) );
			$prefix = WCAPF_Helper::sort_by_prefix();

			$active_filters = array();

			foreach ( $values as $value ) {
				$active_filters[ $value ] = $prefix . ' ' . $value;
			}

			$data = array(
				'values'         => $values,
				'filter_key'     => 'orderby',
				'active_filters' => $active_filters,
			);

			$chosen['filters_data']['orderby'] = $data;
		}

		return $chosen;
	}

	public function get_full_where_clause() {
		$post_clauses = $this->get_full_join_n_where_clauses();
		$wheres       = $post_clauses['wheres'];
		$query_type   = WCAPF_Helper::get_field_relations();

		return WCAPF_Product_Filter_Utils::combine_where_clauses( $wheres, $query_type );
	}

}
