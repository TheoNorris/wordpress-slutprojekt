<?php
/**
 * The taxonomy filter class.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/hooks
 * @author     wptools.io
 */

/**
 * WCAPF_Taxonomy_Filter class.
 *
 * @since 4.0.0
 */
class WCAPF_Taxonomy_Filter {

	/**
	 * Constructor.
	 */
	private function __construct() {
	}

	/**
	 * Returns an instance of this class.
	 *
	 * @return WCAPF_Taxonomy_Filter
	 */
	public static function instance() {
		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance ) {
			$instance = new WCAPF_Taxonomy_Filter();
			$instance->init_hooks();
		}

		return $instance;
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		add_filter( 'wcapf_get_terms_args', array( $this, 'limit_taxonomy_terms' ), 15, 2 );
		add_filter( 'wcapf_get_terms_args', array( $this, 'sort_taxonomy_terms' ), 20, 2 );

		add_filter( 'wcapf_taxonomy_terms', array( $this, 'adjust_parent_term_id' ), 15, 2 );

		add_filter( 'wcapf_taxonomy_filter_values', array( $this, 'term_ids_from_term_slugs' ), 10, 2 );
		add_filter( 'wcapf_ancestors_of_active_terms', array( $this, 'set_ancestors_of_active_terms' ), 10, 2 );
		add_filter( 'wcapf_active_taxonomy_filter_data', array( $this, 'filter_data_from_term_slugs' ), 10, 3 );
	}

	/**
	 * @param array                $args           The arguments of the get_terms function.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	public function limit_taxonomy_terms( $args, $field_instance ) {
		if ( 'rating' === $field_instance->type ) {
			return $args;
		}

		if ( 'manual_entry' === $field_instance->get_options ) {
			return $args;
		}

		$limit_options = $field_instance->get_sub_field_value( 'limit_options' );
		$taxonomy      = $field_instance->taxonomy;

		if ( 'include' === $limit_options || 'exclude' === $limit_options ) {
			if ( 'include' === $limit_options ) {
				$_term_ids     = $field_instance->get_sub_field_value( 'include_terms' );
				$include_child = $field_instance->get_sub_field_value( 'include_child' );
			} else {
				$_term_ids     = $field_instance->get_sub_field_value( 'exclude_terms' );
				$include_child = $field_instance->get_sub_field_value( 'exclude_child' );
			}

			if ( $_term_ids ) {
				if ( $include_child && is_array( $_term_ids ) ) {
					$term_ids = array();

					foreach ( $_term_ids as $term_id ) {
						$term_ids[]  = $term_id;
						$child_terms = get_term_children( $term_id, $taxonomy );

						if ( $child_terms ) {
							$term_ids = array_merge( $term_ids, $child_terms );
						}
					}
				} else {
					$term_ids = $_term_ids;
				}

				$args[ $limit_options ] = $term_ids;
			}
		}

		return $args;
	}

	/**
	 * @param array                $args           The arguments of the get_terms function.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	public function sort_taxonomy_terms( $args, $field_instance ) {
		if ( 'rating' === $field_instance->type ) {
			return $args;
		}

		$allowed   = array( 'id', 'name' );
		$order_by  = $field_instance->get_sub_field_value( 'order_terms_by' );
		$order_dir = $field_instance->get_sub_field_value( 'order_terms_dir' );

		if ( in_array( $order_by, $allowed ) ) {
			if ( 'id' === $order_by ) {
				$order_by = 'term_id';
			}

			$args['orderby'] = $order_by;
		}

		if ( 'asc' === $order_dir ) {
			$args['order'] = 'ASC';
		} else {
			$args['order'] = 'DESC';
		}

		return $args;
	}

	/**
	 * @param array                $terms          The taxonomy terms.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	public function adjust_parent_term_id( $terms, $field_instance ) {
		if ( 'rating' === $field_instance->type ) {
			return $terms;
		}

		if ( 'manual_entry' === $field_instance->get_options ) {
			return $terms;
		}

		if ( ! $field_instance->hierarchical ) {
			return $terms;
		}

		$limit_options = $field_instance->get_sub_field_value( 'limit_options' );

		if ( 'include' === $limit_options || 'exclude' === $limit_options ) {
			if ( 'include' === $limit_options ) {
				$term_ids = $field_instance->get_sub_field_value( 'include_terms' );
			} else {
				$term_ids = $field_instance->get_sub_field_value( 'exclude_terms' );
			}

			if ( ! $term_ids ) {
				return $terms;
			}

			return WCAPF_Product_Filter_Utils::adjust_parent_id_for_hierarchy_terms( $field_instance, $terms );
		}

		return $terms;
	}

	/**
	 * @param array                $values         The filter values.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	public function term_ids_from_term_slugs( $values, $field_instance ) {
		if ( 'rating' === $field_instance->type ) {
			return $values;
		}

		if ( ! $field_instance->use_term_slug ) {
			return $values;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $field_instance->taxonomy,
				'hide_empty' => false,
				'slug'       => $values,
				'fields'     => 'ids',
			)
		);

		if ( is_wp_error( $terms ) ) {
			return array();
		}

		return $terms;
	}

	/**
	 * @param array                $ancestor_ids   The ancestor term ids.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array The ancestor term slugs.
	 */
	public function set_ancestors_of_active_terms( $ancestor_ids, $field_instance ) {
		if ( 'rating' === $field_instance->type ) {
			return $ancestor_ids;
		}

		if ( ! $field_instance->use_term_slug ) {
			return $ancestor_ids;
		}

		if ( ! $ancestor_ids ) {
			return array();
		}

		$term_slugs = get_terms(
			array(
				'taxonomy'   => $field_instance->taxonomy,
				'hide_empty' => false,
				'include'    => $ancestor_ids,
				'fields'     => 'slugs',
			)
		);

		if ( is_wp_error( $term_slugs ) ) {
			return array();
		}

		return $term_slugs;
	}

	/**
	 * @param array                $filter_data    The filter data.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 * @param array                $term_slugs     The term slugs.
	 *
	 * @return array
	 */
	public function filter_data_from_term_slugs( $filter_data, $field_instance, $term_slugs ) {
		if ( 'rating' === $field_instance->type ) {
			return $filter_data;
		}

		if ( ! $field_instance->use_term_slug ) {
			return $filter_data;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $field_instance->taxonomy,
				'hide_empty' => false,
				'slug'       => $term_slugs,
				'orderby'    => 'slug__in',
			)
		);

		if ( is_wp_error( $terms ) ) {
			return array();
		}

		$array  = wp_list_pluck( $terms, 'name', 'slug' );
		$sorted = array();

		foreach ( $array as $key => $value ) {
			$sorted[ rawurldecode( $key ) ] = $value;
		}

		return $sorted;
	}

}

WCAPF_Taxonomy_Filter::instance();
