<?php
/**
 * WCAPF_Filter_Type_Product_Status class.
 *
 * @since      3.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/filter-types
 * @author     wptools.io
 */

/**
 * WCAPF_Filter_Type_Product_Status class.
 *
 * @since 3.0.0
 */
class WCAPF_Filter_Type_Product_Status extends WCAPF_Filter_Type {

	protected function set_properties() {
	}

	protected function prepare_items() {
		$manual_options = $this->field->manual_options;

		$_items = $this->prepare_manual_options( $manual_options );
		$items  = $this->get_filtered_product_counts( $_items );
		$items  = $this->filter_by_hide_empty( $items );

		return apply_filters( 'wcapf_product_status_items', $items, $this->field );
	}

	protected function get_filtered_product_counts( $items ) {
		global $wpdb;

		$helper = new WCAPF_Helper();
		$utils  = new WCAPF_Product_Filter_Utils();

		$post_statuses = $helper::filterable_post_statuses();
		$update_count  = $this->auto_count_enabled();

		list( $meta_query_sql, $tax_query_sql, $search_query, $where_sql ) = $this->get_main_query_data();

		foreach ( $items as $item ) {
			$id = $item['id'];

			$query = array();
			$join  = '';
			$where = '';

			$select = "SELECT COUNT(DISTINCT $wpdb->posts.ID) AS count";

			$query['select'] = $select;
			$query['from']   = "FROM $wpdb->posts";

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

			$condition = '';

			if ( 'featured' === $id ) {
				$featured_products = $utils::get_featured_product_ids_sql();

				$condition = " AND $wpdb->posts.ID IN $featured_products";
			} elseif ( 'on_sale' === $id ) {
				$on_sale_products = $utils::get_product_ids_on_sale_sql();

				$condition = " AND $wpdb->posts.ID IN $on_sale_products";
			}

			$where .= apply_filters( 'wcapf_product_counts_where_clause_for_status', $condition, $id, $this->field );

			if ( $update_count ) {
				$where .= $utils::get_where_clause( $this->query_type, $this->filter_key );
			}

			$query['where'] = $where;

			$query = apply_filters( 'wcapf_products_in_status_query_sql', $query, $this->field, $item );

			$sql = implode( ' ', $query );

			$count = $wpdb->get_var( $sql );

			$items[ $id ]['count'] = $count;
		}

		return $items;
	}

}
