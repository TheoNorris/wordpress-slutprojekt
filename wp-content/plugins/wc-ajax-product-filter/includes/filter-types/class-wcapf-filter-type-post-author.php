<?php
/**
 * WCAPF_Filter_Type_Post_Author class.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/filter-types
 * @author     wptools.io
 */

/**
 * WCAPF_Filter_Type_Post_Author class.
 *
 * @since 4.0.0
 */
class WCAPF_Filter_Type_Post_Author extends WCAPF_Filter_Type {

	/**
	 * Post Property.
	 *
	 * @var string
	 */
	protected $post_property;

	protected function set_properties() {
		$field = $this->field;

		$this->post_property = $field->post_property;
	}

	/**
	 * Prepare the post author filter items.
	 *
	 * @return array
	 */
	protected function prepare_items() {
		$args = array(
			'fields' => array( 'ID', 'display_name' ),
			'number' => 99, // We are limiting the maximum number of users considering larger sites.
		);

		$args   = apply_filters( 'wcapf_get_post_author_args', $args, $this->field );
		$_users = get_users( $args );
		$users  = array();

		foreach ( $_users as $user ) {
			/**
			 * @var WP_User $user
			 */
			$user_id = $user->ID;
			$name    = $user->display_name;

			$author = array(
				'id'    => $user_id,
				'name'  => $name,
				'count' => 0,
			);

			$users[ $user_id ] = $author;
		}

		$items = $this->get_filtered_product_counts( $users );
		$items = $this->filter_by_hide_empty( $items );

		return apply_filters( 'wcapf_post_author_items', $items, $this->field );
	}

	private function get_filtered_product_counts( $items ) {
		global $wpdb;

		$query = $this->get_sql_query();

		$filtered_count = $wpdb->get_results( $query, ARRAY_A );
		$filtered_count = wp_list_pluck( $filtered_count, 'count', 'author_id' );

		return $this->sync_items_count( $items, $filtered_count );
	}

	private function get_sql_query() {
		global $wpdb;

		$helper = new WCAPF_Helper();
		$utils  = new WCAPF_Product_Filter_Utils();

		$post_statuses = $helper::filterable_post_statuses();
		$update_count  = $this->auto_count_enabled();

		list( $meta_query_sql, $tax_query_sql, $search_query, $where_sql ) = $this->get_main_query_data();

		$query = array();
		$join  = '';
		$where = '';

		$property = $this->post_property;

		$query['select'] = "SELECT COUNT(DISTINCT $wpdb->posts.ID) AS count, $wpdb->posts.$property AS author_id";;
		$query['from'] = "FROM $wpdb->posts";

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

		$query['group_by'] = 'GROUP BY author_id';

		$query = apply_filters( 'wcapf_post_author_query_sql', $query, $this->field );

		return implode( ' ', $query );
	}

}
