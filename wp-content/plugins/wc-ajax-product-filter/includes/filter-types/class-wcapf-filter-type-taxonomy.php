<?php
/**
 * WCAPF_Filter_Type_Taxonomy class.
 *
 * @since      3.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/filter-types
 * @author     wptools.io
 */

/**
 * WCAPF_Filter_Type_Taxonomy class.
 *
 * @since 3.0.0
 */
class WCAPF_Filter_Type_Taxonomy extends WCAPF_Filter_Type {

	/**
	 * Taxonomy
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Is Hierarchical
	 *
	 * @var bool
	 */
	protected $hierarchical;

	/**
	 * Sets the properties.
	 *
	 * @return void
	 */
	protected function set_properties() {
		$field = $this->field;

		$this->taxonomy     = $field->taxonomy;
		$this->hierarchical = $field->hierarchical;
	}

	/**
	 * Prepare the terms for the taxonomy.
	 *
	 * @return array
	 */
	protected function prepare_items() {
		$default = array(
			'taxonomy'               => $this->taxonomy,
			'hide_empty'             => false,
			'count'                  => true,
			'update_term_meta_cache' => false,
		);

		$args   = apply_filters( 'wcapf_get_terms_args', $default, $this->field );
		$_terms = get_terms( $args );
		$terms  = array();

		// If there was an error fetching the terms, return empty array.
		if ( is_wp_error( $_terms ) ) {
			return $terms;
		}

		foreach ( $_terms as $_term ) {
			/**
			 * @var WP_Term $_term
			 */
			$term_id   = $_term->term_id;
			$count     = $_term->count;
			$parent_id = $_term->parent;
			$name      = $_term->name;
			$slug      = rawurldecode( $_term->slug );

			$_term = array(
				'id'        => $term_id,
				'name'      => $name,
				'slug'      => $slug,
				'count'     => $count,
				'parent_id' => $parent_id,
			);

			$terms[ $term_id ] = $_term;
		}

		$terms = $this->get_updated_terms_count( $terms );
		$terms = $this->filter_by_hide_empty( $terms );
		$terms = apply_filters( 'wcapf_taxonomy_terms', $terms, $this->field );

		// If taxonomy is non-hierarchical then don't need to build the tree.
		if ( ! is_taxonomy_hierarchical( $this->taxonomy ) ) {
			return $terms;
		}

		// If hierarchical display is disabled then don't need to build the tree.
		if ( ! $this->hierarchical ) {
			return $terms;
		}

		return $this->build_tree( $terms );
	}

	/**
	 * Updates the terms count based on the current filter.
	 *
	 * @param array $terms List of all terms.
	 *
	 * @return array
	 */
	private function get_updated_terms_count( $terms ) {
		if ( ! $terms ) {
			return array();
		}

		$term_ids      = wp_list_pluck( $terms, 'id' );
		$updated_count = array();

		$attributes = wc_get_attribute_taxonomy_names();
		$taxonomy   = $this->taxonomy;

		if ( in_array( $taxonomy, $attributes ) && WCAPF_Helper::filtering_via_lookup_table_is_active() ) {
			$filtered_count = $this->get_filtered_term_product_counts_using_lookup_table( $term_ids );
		} else {
			if ( is_taxonomy_hierarchical( $taxonomy ) ) {
				$filtered_count = $this->get_hierarchical_term_product_counts( $term_ids );
			} else {
				$filtered_count = $this->get_non_hierarchical_term_product_counts( $term_ids );
			}
		}

		foreach ( $terms as $term_id => $term ) {
			$term['count'] = isset( $filtered_count[ $term_id ] ) ? $filtered_count[ $term_id ] : 0;

			$updated_count[ $term_id ] = $term;
		}

		return $updated_count;
	}

	/**
	 * Count products within certain terms, taking the main WP query into
	 * consideration.
	 *
	 * This query allows counts to be generated based on the viewed products,
	 * not all products.
	 *
	 * @param array $term_ids List of all term ids.
	 *
	 * @return array The filtered term product counts.
	 */
	private function get_filtered_term_product_counts_using_lookup_table( $term_ids ) {
		global $wpdb;

		$helper = new WCAPF_Helper();
		$utils  = new WCAPF_Product_Filter_Utils();

		$post_statuses  = $helper::filterable_post_statuses();
		$update_count   = $this->auto_count_enabled();
		$hide_stock_out = $helper::hide_stock_out_items();

		$lookup_table_name = $wpdb->prefix . 'wc_product_attributes_lookup';

		list( $meta_query_sql, $tax_query_sql, $search_query, $where_sql ) = $this->get_main_query_data(
			$lookup_table_name,
			'product_or_parent_id'
		);

		$query = array();
		$join  = '';
		$where = '';

		$query['select'] = 'SELECT COUNT(DISTINCT product_or_parent_id) as term_count, term_id as term_count_id';

		$query['from'] = "FROM $lookup_table_name";

		$join .= "INNER JOIN $wpdb->posts ON $wpdb->posts.ID = $lookup_table_name.product_or_parent_id";
		$join .= $meta_query_sql['join'];
		$join .= $tax_query_sql['join'];

		if ( $update_count ) {
			$join .= $utils::get_join_clause();
		}

		$query['join'] = $join;

		$where .= "WHERE $wpdb->posts.post_type IN ('product')";
		$where .= " AND $wpdb->posts.post_status IN ('" . implode( "','", $post_statuses ) . "')";

		$where .= $tax_query_sql['where'] . $meta_query_sql['where'];
		$where .= $search_query ? ' AND ' . $search_query : '';
		$where .= $where_sql;

		$term_ids_sql = $utils::get_ids_sql( $term_ids );

		$where .= " AND $lookup_table_name.taxonomy = '$this->taxonomy'";
		$where .= " AND $lookup_table_name.term_id IN $term_ids_sql";

		$where .= $hide_stock_out ? ' AND in_stock = 1' : '';

		if ( $update_count ) {
			$where .= $utils::get_where_clause( $this->query_type, $this->filter_key );
		}

		$query['where'] = $where;

		$query['group_by'] = "GROUP BY $lookup_table_name.term_id";

		$query = apply_filters( 'wcapf_term_product_counts_query_using_lookup_table', $query, $this->field, $term_ids );
		$query = implode( ' ', $query );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $query, ARRAY_A );

		return array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
	}

	private function get_hierarchical_term_product_counts( $term_ids ) {
		global $wpdb;

		$helper = new WCAPF_Helper;
		$utils  = new WCAPF_Product_Filter_Utils();

		$post_statuses = $helper::filterable_post_statuses();
		$update_count  = $this->auto_count_enabled();

		list( $meta_query_sql, $tax_query_sql, $search_query, $where_sql ) = $this->get_main_query_data();

		$query = array();
		$join  = '';
		$where = '';

		// query all terms
		$_all_terms = get_terms(
			array(
				'taxonomy'   => $this->taxonomy,
				'hide_empty' => false,
				'fields'     => 'ids',
			)
		);

		$_clauses = array();

		foreach ( $_all_terms as $term_id ) {
			$child_terms = get_term_children( $term_id, $this->taxonomy );
			$term_ids_in = array();

			if ( $child_terms ) {
				$_child_terms = array();

				foreach ( $child_terms as $child_term ) {
					$_child_terms[] = $child_term;
				}

				$term_ids_in = array_merge( array( $term_id ), $_child_terms );
			} else {
				$term_ids_in[] = $term_id;
			}

			$ids_in = WCAPF_Product_Filter_Utils::get_ids_sql( $term_ids_in );

			$clauses = "COUNT";
			$clauses .= "(";
			$clauses .= "DISTINCT CASE WHEN terms.term_id IN $ids_in";
			$clauses .= " THEN $wpdb->posts.ID END";
			$clauses .= ") AS `$term_id`";

			$_clauses[] = $clauses;
		}

		$clauses = implode( ', ', $_clauses );

		$query['select'] = "SELECT $clauses";

		$query['from'] = "FROM $wpdb->posts";

		$join .= "INNER JOIN $wpdb->term_relationships AS term_relationships ON $wpdb->posts.ID = term_relationships.object_id";
		$join .= " INNER JOIN $wpdb->term_taxonomy AS term_taxonomy USING(term_taxonomy_id)";
		$join .= " INNER JOIN $wpdb->terms AS terms USING(term_id)";
		$join .= $meta_query_sql['join'];
		$join .= $tax_query_sql['join'];

		if ( $update_count ) {
			$join .= $utils::get_join_clause();
		}

		$query['join'] = $join;

		$where .= "WHERE $wpdb->posts.post_type IN ('product')";
		$where .= " AND $wpdb->posts.post_status IN ('" . implode( "','", $post_statuses ) . "')";

		$where .= $tax_query_sql['where'] . $meta_query_sql['where'];
		$where .= $search_query ? ' AND ' . $search_query : '';
		$where .= $where_sql;

		if ( $update_count ) {
			$where .= $utils::get_where_clause( $this->query_type, $this->filter_key );
		}

		$query['where'] = $where;

		$query = apply_filters( 'wcapf_hierarchical_term_product_counts', $query, $this->field, $term_ids );
		$query = implode( ' ', $query );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return $wpdb->get_row( $query, ARRAY_A );
	}

	/**
	 * Count products within certain terms, taking the main WP query into
	 * consideration.
	 *
	 * This query allows counts to be generated based on the viewed products,
	 * not all products.
	 *
	 * @param array $term_ids List of all term ids.
	 *
	 * @return array The filtered term product counts.
	 */
	private function get_non_hierarchical_term_product_counts( $term_ids ) {
		global $wpdb;

		$helper = new WCAPF_Helper;
		$utils  = new WCAPF_Product_Filter_Utils();

		$post_statuses = $helper::filterable_post_statuses();
		$update_count  = $this->auto_count_enabled();

		list( $meta_query_sql, $tax_query_sql, $search_query, $where_sql ) = $this->get_main_query_data();

		$query  = array();
		$select = '';
		$join   = '';
		$where  = '';

		$select .= "SELECT COUNT(DISTINCT $wpdb->posts.ID) ";
		$select .= 'AS term_count, terms.term_id AS term_count_id';

		$query['select'] = $select;

		$query['from'] = "FROM $wpdb->posts";

		$join .= "INNER JOIN $wpdb->term_relationships AS term_relationships ON $wpdb->posts.ID = term_relationships.object_id";
		$join .= " INNER JOIN $wpdb->term_taxonomy AS term_taxonomy USING(term_taxonomy_id)";
		$join .= " INNER JOIN $wpdb->terms AS terms USING(term_id)";
		$join .= $meta_query_sql['join'];
		$join .= $tax_query_sql['join'];

		if ( $update_count ) {
			$join .= $utils::get_join_clause();
		}

		$query['join'] = $join;

		$term_ids_sql = $utils::get_ids_sql( $term_ids );

		$where .= "WHERE $wpdb->posts.post_type IN ('product')";
		$where .= " AND $wpdb->posts.post_status IN ('" . implode( "','", $post_statuses ) . "')";
		$where .= " AND terms.term_id IN $term_ids_sql";

		$where .= $tax_query_sql['where'] . $meta_query_sql['where'];
		$where .= $search_query ? ' AND ' . $search_query : '';
		$where .= $where_sql;

		if ( $update_count ) {
			$where .= $utils::get_where_clause( $this->query_type, $this->filter_key );
		}

		$query['where'] = $where;

		$query['group_by'] = 'GROUP BY terms.term_id';

		$query = apply_filters( 'wcapf_hierarchical_term_product_counts', $query, $this->field, $term_ids );
		$query = implode( ' ', $query );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $query, ARRAY_A );

		return array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
	}

	/**
	 * Builds the taxonomy tree.
	 *
	 * @source https://stackoverflow.com/a/8587437
	 *
	 * @param array   $terms     The terms.
	 * @param integer $parent_id The parent identifier.
	 * @param int     $depth     The current depth.
	 *
	 * @return array The taxonomy tree.
	 */
	private function build_tree( $terms, $parent_id = 0, $depth = 0 ) {
		$tree      = array();
		$increment = 0;

		foreach ( $terms as $term_id => $term ) {
			if ( 0 === $increment ) {
				$depth ++;
			}

			// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			if ( $term['parent_id'] == $parent_id ) {
				$children = $this->build_tree( $terms, $term_id, $depth );

				if ( $children ) {
					$term['children'] = $children;
				}

				$term['depth'] = $depth;

				$tree[ $term_id ] = $term;
			}

			$increment ++;
		}

		return $tree;
	}

}
