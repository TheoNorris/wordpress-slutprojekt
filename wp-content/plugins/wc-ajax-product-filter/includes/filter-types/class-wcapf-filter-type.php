<?php
/**
 * WCAPF_Filter_Type class.
 *
 * @since      3.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/filter-types
 * @author     wptools.io
 */

/**
 * WCAPF_Filter_Type class.
 *
 * @since 3.0.0
 */
abstract class WCAPF_Filter_Type {

	/**
	 * Filter key
	 *
	 * @var string
	 */
	protected $filter_key;

	/**
	 * Query type
	 *
	 * @var string
	 */
	protected $query_type;

	/**
	 * Hide empty
	 *
	 * @var string
	 */
	protected $hide_empty;

	/**
	 * The field instance.
	 *
	 * @var WCAPF_Field_Instance
	 */
	protected $field;

	/**
	 * The constructor.
	 *
	 * @param WCAPF_Field_Instance $field The field instance.
	 */
	public function __construct( $field ) {
		$this->field = $field;

		$this->set_default_properties();
		$this->set_properties();
	}

	/**
	 * Sets the default properties.
	 *
	 * @return void
	 */
	private function set_default_properties() {
		$field = $this->field;

		$this->filter_key = $field->filter_key;
		$this->query_type = $field->query_type;
		$this->hide_empty = $field->hide_empty;
	}

	/**
	 * Sets the properties.
	 *
	 * @return void
	 */
	abstract protected function set_properties();

	/**
	 * Gets the items.
	 *
	 * @return array
	 */
	public function get_items() {
		/**
		 * Register a hook to modify the filter items array.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'wcapf_filter_items', $this->prepare_items(), $this->field );
	}

	/**
	 * Prepare the items.
	 *
	 * @return array
	 */
	abstract protected function prepare_items();

	/**
	 * Gets the main query data.
	 *
	 * @param string $primary_table  The primary table name.
	 * @param string $primary_column The primary column name.
	 *
	 * @return array
	 */
	public function get_main_query_data( $primary_table = '', $primary_column = '' ) {
		global $wpdb;

		if ( ! $primary_table ) {
			$primary_table = $wpdb->posts;
		}

		if ( ! $primary_column ) {
			$primary_column = 'ID';
		}

		$meta_query_sql = array( 'join' => '', 'where' => '' );
		$tax_query_sql  = array( 'join' => '', 'where' => '' );
		$search_query   = '';
		$where_sql      = '';

		$post__in       = array();
		$post__not_in   = array();
		$author__in     = array();
		$author__not_in = array();

		if ( is_shop() || is_product_taxonomy() ) {
			$tax_query    = WC_Query::get_main_tax_query();
			$meta_query   = WC_Query::get_main_meta_query();
			$search_query = WC_Query::get_main_search_query_sql();

			$meta_query     = new WP_Meta_Query( $meta_query );
			$tax_query      = new WP_Tax_Query( $tax_query );
			$meta_query_sql = $meta_query->get_sql( 'post', $primary_table, $primary_column );
			$tax_query_sql  = $tax_query->get_sql( $primary_table, $primary_column );

			$main_query     = WC_Query::get_main_query();
			$post__in       = $main_query->get( 'post__in' );
			$post__not_in   = $main_query->get( 'post__not_in' );
			$author__in     = $main_query->get( 'author__in' );
			$author__not_in = $main_query->get( 'author__not_in' );
		}

		list(
			$meta_query_sql,
			$tax_query_sql,
			$search_query,
			$post__in,
			$post__not_in,
			$author__in,
			$author__not_in
			) = apply_filters(
			'wcapf_main_query_data',
			array(
				$meta_query_sql,
				$tax_query_sql,
				$search_query,
				$post__in,
				$post__not_in,
				$author__in,
				$author__not_in
			),
			$primary_table,
			$primary_column
		);

		$utils = new WCAPF_Product_Filter_Utils;

		if ( $post__in ) {
			$ids_in = $utils::get_ids_sql( $post__in );

			$where_sql .= " AND $wpdb->posts.ID IN $ids_in";
		}

		if ( $post__not_in ) {
			$ids_in = $utils::get_ids_sql( $post__not_in );

			$where_sql .= " AND $wpdb->posts.ID NOT IN $ids_in";
		}

		if ( $author__in ) {
			$ids_in = $utils::get_ids_sql( $author__in );

			$where_sql .= " AND $wpdb->posts.post_author IN $ids_in";
		}

		if ( $author__not_in ) {
			$ids_in = $utils::get_ids_sql( $author__not_in );

			$where_sql .= " AND $wpdb->posts.post_author NOT IN $ids_in";
		}

		return array( $meta_query_sql, $tax_query_sql, $search_query, $where_sql );
	}

	/**
	 * @param array $items          The items array.
	 * @param array $filtered_count The filtered count array to sync with.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function sync_items_count( $items, $filtered_count ) {
		if ( ! $items ) {
			return array();
		}

		$updated_count = array();

		foreach ( $items as $key => $range ) {
			$id    = $range['id'];
			$count = isset( $filtered_count[ $id ] ) ? $filtered_count[ $id ] : 0;

			$range['count'] = $count;

			$updated_count[ $key ] = $range;
		}

		return $updated_count;
	}

	/**
	 * Exclude the empty items.
	 *
	 * @param array $items The items.
	 *
	 * @return array
	 */
	protected function filter_by_hide_empty( $items ) {
		if ( 'remove' === $this->hide_empty ) {
			$items_with_count = array();

			foreach ( $items as $key => $item ) {
				if ( $item['count'] ) {
					$items_with_count[ $key ] = $item;
				}
			}

			$items = $items_with_count;
		}

		return $items;
	}

	/**
	 * @return bool
	 */
	protected function auto_count_enabled() {
		return apply_filters(
			'wcapf_filter_update_count',
			$this->field->get_sub_field_value( 'update_count' ),
			$this->field
		);
	}

	/**
	 * Prepare the manual options for product-status, sort-by and per-page filter types.
	 *
	 * @param array $manual_options The manual options from the filter field.
	 *
	 * @return array
	 */
	protected function prepare_manual_options( $manual_options ) {
		$type    = $this->field->filter_type;
		$options = array();

		foreach ( $manual_options as $option ) {
			$value   = $option['value'];
			$label   = $option['label'];
			$tooltip = isset( $option['tooltip'] ) ? $option['tooltip'] : '';
			$count   = 0;

			// In sort-by filter, id is the unique value because id is generated from value and order direction.
			if ( 'sort-by' === $type ) {
				$id = $option['id'];
			} else {
				$id = $value;
			}

			$item = array_merge(
				$option,
				array(
					'id'      => $id,
					'name'    => $label,
					'tooltip' => $tooltip,
					'count'   => $count,
				)
			);

			$options[ $id ] = $item;
		}

		return $options;
	}

}
