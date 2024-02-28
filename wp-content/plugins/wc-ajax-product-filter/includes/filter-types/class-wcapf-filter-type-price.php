<?php
/**
 * WCAPF_Filter_Type_Price class.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/filter-types
 * @author     wptools.io
 */

/**
 * WCAPF_Filter_Type_Price class.
 *
 * @since 3.0.0
 */
class WCAPF_Filter_Type_Price extends WCAPF_Filter_Type {

	protected function set_properties() {
	}

	protected function prepare_items() {
		return $this->get_range();
	}

	protected function get_range() {
		global $wpdb;

		$helper = new WCAPF_Helper();
		$utils  = new WCAPF_Product_Filter_Utils();

		$post_statuses  = $helper::filterable_post_statuses();
		$hide_stock_out = $helper::hide_stock_out_items();

		list( $meta_query_sql, $tax_query_sql, $search_query, $where_sql ) = $this->get_main_query_data();

		$lookup_table_name = $wpdb->prefix . 'wc_product_meta_lookup';

		$join  = '';
		$where = '';
		$query = array();

		$select = 'SELECT MIN( price_table.min_price ) AS min_price, MAX( price_table.max_price ) AS max_price';

		$query['select'] = $select;
		$query['from']   = "FROM $wpdb->posts";

		$join .= " LEFT JOIN $lookup_table_name AS price_table ON $wpdb->posts.ID = price_table.product_id";
		$join .= $meta_query_sql['join'];
		$join .= $tax_query_sql['join'];

		$update_range = false;

		if ( 'and' === WCAPF_Helper::get_field_relations() ) {
			$update_range = true;
		}

		$update_range = apply_filters( 'wcapf_update_range_min_max', $update_range, $this->field );

		if ( $update_range ) {
			$join .= $utils::get_join_clause();
		}

		$query['join'] = $join;

		$where .= "WHERE $wpdb->posts.post_type IN ('product')";
		$where .= " AND $wpdb->posts.post_status IN ('" . implode( "','", $post_statuses ) . "')";

		$where .= $tax_query_sql['where'] . $meta_query_sql['where'];
		$where .= $search_query ? ' AND ' . $search_query : '';
		$where .= $where_sql;

		$where .= $hide_stock_out ? " AND price_table.stock_status = 'instock'" : '';

		if ( $update_range ) {
			$where .= $utils::get_where_clauses_by_other_filters( $this->filter_key );
		}

		$query['where'] = $where;

		$query = apply_filters( 'wcapf_price_min_max_sql_query', $query, $this->field );
		$query = implode( ' ', $query );

		$results = $wpdb->get_row( $query, ARRAY_A );

		$min = isset( $results['min_price'] ) ? floatval( $results['min_price'] ) : 0;
		$max = isset( $results['max_price'] ) ? floatval( $results['max_price'] ) : 0;

		// Check to see if we should add taxes to the prices if store are excl tax but display incl.
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

		if ( wc_tax_enabled() && ! wc_prices_include_tax() && 'incl' === $tax_display_mode ) {
			$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
			$tax_rates = WC_Tax::get_rates( $tax_class );

			if ( $tax_rates ) {
				$min += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $min, $tax_rates ) );
				$max += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $max, $tax_rates ) );
			}
		}

		return compact( 'min', 'max' );
	}

}
