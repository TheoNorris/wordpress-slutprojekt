<?php
/**
 * The post author filter class.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/hooks
 * @author     wptools.io
 */

/**
 * WCAPF_Post_Author_Filter class.
 *
 * @since 4.0.0
 */
class WCAPF_Post_Author_Filter {

	/**
	 * Constructor.
	 */
	private function __construct() {
	}

	/**
	 * Returns an instance of this class.
	 *
	 * @return WCAPF_Post_Author_Filter
	 */
	public static function instance() {
		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance ) {
			$instance = new WCAPF_Post_Author_Filter();
			$instance->init_hooks();
		}

		return $instance;
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		add_filter( 'wcapf_get_post_author_args', array( $this, 'limit_post_authors' ), 10, 2 );
		add_filter( 'wcapf_get_post_author_args', array( $this, 'sort_post_authors' ), 15, 2 );
	}

	/**
	 * @param array                $args           The arguments of the get_users function.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	public function limit_post_authors( $args, $field_instance ) {
		$limit_options = $field_instance->get_sub_field_value( 'limit_options' );

		if ( 'manual_entry' === $field_instance->get_options ) {
			return $args;
		}

		if ( 'include' === $limit_options ) {
			$args['include'] = $field_instance->get_sub_field_value( 'include_authors' );
		} elseif ( 'exclude' === $limit_options ) {
			$args['exclude'] = $field_instance->get_sub_field_value( 'exclude_authors' );
		} elseif ( 'user_roles' === $limit_options ) {
			$args['role__in'] = $field_instance->get_sub_field_value( 'include_user_roles' );
		}

		return $args;
	}

	/**
	 * @param array                $args           The arguments of the get_users function.
	 * @param WCAPF_Field_Instance $field_instance The field instance.
	 *
	 * @return array
	 */
	public function sort_post_authors( $args, $field_instance ) {
		$allowed   = array( 'id', 'name' );
		$order_by  = $field_instance->get_sub_field_value( 'post_author_order_by' );
		$order_dir = $field_instance->get_sub_field_value( 'post_author_order_dir' );

		if ( in_array( $order_by, $allowed ) ) {
			$args['orderby'] = $order_by;
		}

		if ( 'asc' === $order_dir ) {
			$args['order'] = 'ASC';
		} else {
			$args['order'] = 'DESC';
		}

		return $args;
	}

}

WCAPF_Post_Author_Filter::instance();
