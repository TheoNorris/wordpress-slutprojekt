<?php
/**
 * WCAPF_Filter_Type_Post_Meta class.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/filter-types
 * @author     wptools.io
 */

/**
 * WCAPF_Filter_Type_Post_Meta class.
 *
 * @since 4.0.0
 */
class WCAPF_Filter_Type_Post_Meta extends WCAPF_Filter_Type {

	/**
	 * Post meta key
	 *
	 * @var string
	 */
	protected $meta_key;

	protected function set_properties() {
		$field = $this->field;

		$this->meta_key = $field->meta_key;
	}

	protected function prepare_items() {
		$meta_values = $this->get_text_automatic_values();
		$meta_values = $this->get_updated_meta_values_count( $meta_values );
		$meta_values = $this->filter_by_hide_empty( $meta_values );

		return apply_filters( 'wcapf_post_meta_text_items', $meta_values, $this->field );
	}

	/**
	 * @return array
	 */
	protected function get_text_automatic_values() {
		$meta_values = array();

		$_meta_values = $this->get_meta_values();

		// Parse the array.
		foreach ( $_meta_values as $value ) {
			$meta_value    = $value['meta_value'];
			$product_count = $value['meta_count'];

			$data = array(
				'id'    => $meta_value,
				'name'  => $meta_value,
				'count' => $product_count,
			);

			$meta_values[ $meta_value ] = $data;
		}

		return $meta_values;
	}

	/**
	 * Gets the meta values for the meta key.
	 *
	 * @return array
	 */
	protected function get_meta_values() {
		$query = $this->get_sql_query();

		global $wpdb;

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return $wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * @param string $filter_type The filter type.
	 *
	 * @return string
	 */
	private function get_sql_query( $filter_type = 'non-filtered' ) {
		global $wpdb;

		$helper = new WCAPF_Helper();
		$utils  = new WCAPF_Product_Filter_Utils();

		$post_statuses = $helper::filterable_post_statuses();
		$update_count  = $this->auto_count_enabled();

		list( $meta_query_sql, $tax_query_sql, $search_query, $where_sql ) = $this->get_main_query_data();

		$query = array();
		$join  = '';
		$where = '';

		$query['select'] = "SELECT COUNT(DISTINCT $wpdb->posts.ID) AS meta_count, metas.meta_value";;
		$query['from'] = "FROM $wpdb->posts";

		$join .= "INNER JOIN $wpdb->postmeta AS metas ON $wpdb->posts.ID = metas.post_id";
		$join .= $meta_query_sql['join'];
		$join .= $tax_query_sql['join'];

		if ( 'filtered' === $filter_type && $update_count ) {
			$join .= $utils::get_join_clause();
		}

		$query['join'] = $join;

		$where .= "WHERE $wpdb->posts.post_type IN ('product')";
		$where .= " AND $wpdb->posts.post_status IN ('" . implode( "','", $post_statuses ) . "')";
		$where .= " AND metas.meta_key = '$this->meta_key'";
		$where .= " AND metas.meta_value <> ''"; // TODO: Check for empty and null columns.

		$where .= $tax_query_sql['where'] . $meta_query_sql['where'];
		$where .= $search_query ? ' AND ' . $search_query : '';
		$where .= $where_sql;

		if ( 'non-filtered' === $filter_type ) {
			$limit_options = $this->field->get_sub_field_value( 'limit_options' );

			if ( 'include' === $limit_options ) {
				$include_ids = $this->field->get_sub_field_value( 'limit_values_by_id' );
				$include_ids = explode( ',', $include_ids );

				$where .= " AND metas.meta_value IN ('" . implode( "','", $include_ids ) . "')";
			} elseif ( 'exclude' === $limit_options ) {
				$exclude_ids = $this->field->get_sub_field_value( 'exclude_values_id' );
				$exclude_ids = explode( ',', $exclude_ids );

				$where .= " AND metas.meta_value NOT IN ('" . implode( "','", $exclude_ids ) . "')";
			}
		}

		if ( 'filtered' === $filter_type && $update_count ) {
			$where .= $utils::get_where_clause( $this->query_type, $this->filter_key );
		}

		$query['where']    = $where;
		$query['group_by'] = 'GROUP BY metas.meta_value';

		$query = apply_filters( 'wcapf_meta_query_sql', $query, $this->field );

		return implode( ' ', $query );
	}

	/**
	 * Updates the count based on the current filter.
	 *
	 * @param array $meta_values List of all meta values.
	 *
	 * @return array
	 */
	protected function get_updated_meta_values_count( $meta_values ) {
		if ( ! $meta_values ) {
			return array();
		}

		$filtered_count = $this->get_filtered_meta_product_counts();
		$updated_count  = array();

		foreach ( $meta_values as $meta_value => $data ) {
			$data['count'] = isset( $filtered_count[ $meta_value ] ) ? $filtered_count[ $meta_value ] : 0;

			$updated_count[ $meta_value ] = $data;
		}

		return $updated_count;
	}

	/**
	 * Gets the filtered count.
	 *
	 * @return array
	 */
	protected function get_filtered_meta_product_counts() {
		$query = $this->get_sql_query( 'filtered' );

		global $wpdb;

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $query, ARRAY_A );

		return wp_list_pluck( $results, 'meta_count', 'meta_value' );
	}

}
