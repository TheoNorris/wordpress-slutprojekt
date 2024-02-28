<?php
/**
 * The api class.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/hooks
 * @author     wptools.io
 */

/**
 * WCAPF_API class.
 *
 * @since 4.0.0
 */
class WCAPF_API {

	/**
	 * Constructor.
	 */
	private function __construct() {
	}

	/**
	 * Returns an instance of this class.
	 *
	 * @return WCAPF_API
	 */
	public static function instance() {
		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance ) {
			$instance = new WCAPF_API();
			$instance->init_hooks();
		}

		return $instance;
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		add_action( 'wp_ajax_wcapf_get_terms_for_modal', array( $this, 'get_terms_for_modal' ) );
		add_action( 'wp_ajax_wcapf_get_meta_values_for_modal', array( $this, 'get_meta_values_for_modal' ) );
		add_action( 'wp_ajax_wcapf_get_post_authors_for_modal', array( $this, 'get_post_authors_for_modal' ) );
		add_action( 'wp_ajax_wcapf_get_terms_for_dropdown', array( $this, 'get_terms_for_dropdown' ) );
		add_action( 'wp_ajax_wcapf_get_authors_for_dropdown', array( $this, 'get_authors_for_dropdown' ) );
		add_action( 'wp_ajax_wcapf_get_pages_for_dropdown', array( $this, 'get_pages_for_dropdown' ) );
		add_action( 'wp_ajax_wcapf_get_products_for_dropdown', array( $this, 'get_products_for_dropdown' ) );

		// For form.
		add_action( 'wp_ajax_wcapf_create_sample_form', array( $this, 'create_sample_form' ) );
		add_action( 'wp_ajax_wcapf_get_form_data', array( $this, 'get_form_data' ) );
		add_action( 'wp_ajax_wcapf_add_form', array( $this, 'add_form' ) );
		add_action( 'wp_ajax_wcapf_save_form', array( $this, 'save_form' ) );
		add_action( 'wp_ajax_wcapf_delete_form', array( $this, 'delete_form' ) );
		add_action( 'wp_ajax_wcapf_delete_filter', array( $this, 'delete_filter' ) );

		// Save settings.
		add_action( 'wp_ajax_wcapf_save_settings', array( $this, 'save_settings' ) );

		// Track the form updates count to show the review notice.
		add_action( 'wcapf_form_saved', array( $this, 'track_form_updates_count' ) );
	}

	/**
	 * Creates the sample form via ajax.
	 *
	 * @return void
	 */
	public function create_sample_form() {
		$this->verify_nonce();
		$this->verify_permission();

		$form_settings = WCAPF_Default_Data::form_default_data();

		$post_arr = array(
			'post_title'   => __( 'Sample form', 'wc-ajax-product-filter' ),
			'post_content' => maybe_serialize( $form_settings ),
			'menu_order'   => 0,
			'post_type'    => 'wcapf-form',
			'post_status'  => 'publish',
		);

		$new_form_id = wp_insert_post( $post_arr, true );

		if ( is_wp_error( $new_form_id ) ) {
			wp_send_json_error( $new_form_id->get_error_message() );
		}

		$form_filters_utils = new WCAPF_Form_Filters_Utils();
		$form_filters_utils->save_form_filters( WCAPF_Default_Data::get_sample_filters(), $new_form_id );

		wp_send_json_success( WCAPF_API_Utils::get_form_data( $new_form_id ) );
	}

	/**
	 * Handles the nonce verification.
	 *
	 * @since 4.2.0
	 *
	 * @return void
	 */
	private function verify_nonce() {
		$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : '';

		if ( ! wp_verify_nonce( $nonce, 'wcapf-nonce' ) ) {
			wp_send_json_error( __( 'Nonce verification failed', 'wc-ajax-product-filter' ) );
		}
	}

	/**
	 * Handles the permission check.
	 *
	 * @since 4.2.0
	 *
	 * @return void
	 */
	private function verify_permission() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied', 'wc-ajax-product-filter' ) );
		}
	}

	/**
	 * Gets the form data via ajax for the edit form UI.
	 *
	 * @return void
	 */
	public function get_form_data() {
		$this->verify_nonce();
		$this->verify_permission();

		$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : '';
		$form    = get_post( $post_id );

		if ( ! $form || 'wcapf-form' !== $form->post_type ) {
			wp_send_json_error( __( 'Invalid form id', 'wc-ajax-product-filter' ) );
		}

		$args = array(
			'post_type'   => 'wcapf-filter',
			'post_status' => 'any',
			'nopaging'    => true,
			'post_parent' => $post_id,
			'order'       => 'ASC',
			'orderby'     => 'menu_order',
		);

		$filter_ids   = get_posts( $args );
		$form_filters = array();

		foreach ( $filter_ids as $filter_id ) {
			$post = get_post( $filter_id );

			if ( ! $post || 'wcapf-filter' !== $post->post_type ) {
				continue;
			}

			$filter = maybe_unserialize( $post->post_content );
			$filter = $this->parse_filter_data( $filter );

			$form_filters[] = $filter;
		}

		$form_settings = maybe_unserialize( $form->post_content );
		$form_settings = $this->parse_form_settings( $form_settings );

		wp_send_json_success(
			array(
				'post_id'       => $post_id,
				'post_title'    => get_the_title( $post_id ),
				'filter_keys'   => WCAPF_API_Utils::get_filter_keys(),
				'form_filters'  => $form_filters,
				'form_settings' => $form_settings,
			)
		);
	}

	/**
	 * Parse the filter data for react ui.
	 *
	 * @param array $filter The filter data.
	 *
	 * @return array
	 */
	private function parse_filter_data( $filter ) {
		$value_may_have_spaces = array(
			'value_prefix',
			'value_postfix',
			'values_separator',
			'text_before_min_value',
			'text_before_max_value',
		);

		foreach ( $value_may_have_spaces as $key ) {
			$value = isset( $filter[ $key ] ) ? $filter[ $key ] : '';

			$filter[ $key ] = str_replace( '&nbsp;', ' ', $value );
		}

		// Prepare the term data.
		$parent_term   = isset( $filter['parent_term'] ) ? $filter['parent_term'] : '';
		$include_terms = isset( $filter['include_terms'] ) ? $filter['include_terms'] : array();
		$exclude_terms = isset( $filter['exclude_terms'] ) ? $filter['exclude_terms'] : array();

		if ( $parent_term ) {
			$term = get_term( $parent_term );

			$filter['parent_term'] = array(
				'value' => $term->term_id,
				'label' => $term->name,
			);
		}

		$array = array(
			'include_terms' => $include_terms,
			'exclude_terms' => $exclude_terms,
		);

		foreach ( $array as $key => $values ) {
			if ( $values ) {
				$parsed = array();

				foreach ( $values as $id ) {
					$term = get_term( $id );

					if ( $term && ! is_wp_error( $term ) ) {
						$parsed[] = array(
							'value' => $term->term_id,
							'label' => $term->name,
						);
					}
				}

				$filter[ $key ] = $parsed;
			}
		}

		// Prepare the user data.
		$include_authors = isset( $filter['include_authors'] ) ? $filter['include_authors'] : array();
		$exclude_authors = isset( $filter['exclude_authors'] ) ? $filter['exclude_authors'] : array();

		$array = array(
			'include_authors' => $include_authors,
			'exclude_authors' => $exclude_authors,
		);

		foreach ( $array as $key => $values ) {
			if ( $values ) {
				$parsed = array();

				foreach ( $values as $id ) {
					$user = get_userdata( $id );

					if ( $user && ! is_wp_error( $user ) ) {
						$parsed[] = array(
							'value' => $user->ID,
							'label' => $user->display_name,
						);
					}
				}

				$filter[ $key ] = $parsed;
			}
		}

		// Prepare user roles.
		$include_user_roles = isset( $filter['include_user_roles'] ) ? $filter['include_user_roles'] : array();

		if ( $include_user_roles ) {
			$roles  = WCAPF_Product_Filter_Utils::get_user_roles();
			$parsed = array();

			foreach ( $include_user_roles as $role ) {
				$parsed[] = array(
					'value' => $role,
					'label' => $roles[ $role ],
				);
			}

			$filter['include_user_roles'] = $parsed;
		}

		$type = isset( $filter['type'] ) ? $filter['type'] : '';

		if ( 'taxonomy' === $type ) { // Set the term name for the options table.
			$manual_options = isset( $filter['manual_options'] ) ? $filter['manual_options'] : array();

			if ( $manual_options ) {
				$parsed = array();

				foreach ( $manual_options as $option ) {
					$term = get_term( $option['value'] );

					if ( ! $term ) {
						continue;
					}

					$option['name'] = $term->name;

					$parsed[] = $option;
				}

				$filter['manual_options'] = $parsed;
			}
		} elseif ( 'post-author' === $type ) { // Set the username for the options table.
			$manual_options = isset( $filter['manual_options'] ) ? $filter['manual_options'] : array();

			if ( $manual_options ) {
				$parsed = array();

				foreach ( $manual_options as $option ) {
					$user = get_userdata( $option['value'] );

					$option['name'] = $user->display_name;

					$parsed[] = $option;
				}

				$filter['manual_options'] = $parsed;
			}
		}

		return apply_filters( 'wcapf_parse_filter_data', $filter );
	}

	/**
	 * Parse the form settings for react ui.
	 *
	 * @param array $sanitized The sanitized form settings.
	 *
	 * @return array
	 */
	private function parse_form_settings( $sanitized ) {
		return apply_filters( 'wcapf_parse_form_settings', $sanitized );
	}

	public function add_form() {
		$this->verify_nonce();
		$this->verify_permission();

		$form_title     = isset( $_POST['form_title'] ) ? sanitize_text_field( $_POST['form_title'] ) : '';
		$_form_settings = isset( $_POST['form_settings'] ) ? $_POST['form_settings'] : '';
		$form_settings  = stripslashes( $_form_settings );
		$form_settings  = json_decode( $form_settings, true );

		$post_arr = array(
			'post_title'   => $form_title,
			'post_type'    => 'wcapf-form',
			'post_status'  => 'publish',
			'post_content' => maybe_serialize( $form_settings ),
		);

		$new_form_id = wp_insert_post( $post_arr, true );

		if ( is_wp_error( $new_form_id ) ) {
			wp_send_json_error( $new_form_id->get_error_message() );
		}

		wp_send_json_success( WCAPF_API_Utils::get_form_data( $new_form_id ) );
	}

	/**
	 * Saves the form via ajax.
	 *
	 * @return void
	 */
	public function save_form() {
		$this->verify_nonce();
		$this->verify_permission();

		$form_id    = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
		$form_title = isset( $_POST['form_title'] ) ? sanitize_text_field( $_POST['form_title'] ) : '';

		$_form_filters = isset( $_POST['form_filters'] ) ? $_POST['form_filters'] : '';
		$form_filters  = stripslashes( $_form_filters );
		$form_filters  = json_decode( $form_filters, true );

		$_form_settings = isset( $_POST['form_settings'] ) ? $_POST['form_settings'] : '';
		$form_settings  = stripslashes( $_form_settings );
		$form_settings  = json_decode( $form_settings, true );

		if ( ! $form_id || 'wcapf-form' !== get_post_type( $form_id ) ) {
			wp_send_json_error( __( 'Invalid form id', 'wc-ajax-product-filter' ) );
		}

		/**
		 * The hooks for altering the form settings.
		 */
		$sanitized_form_settings = apply_filters(
			'wcapf_sanitize_form_settings',
			$this->sanitize_form_settings( $form_settings ),
			$form_settings
		);

		$post_arr = array(
			'ID'           => $form_id,
			'post_title'   => $form_title,
			'post_content' => maybe_serialize( $sanitized_form_settings ),
			'menu_order'   => isset( $form_settings['priority'] ) ? $form_settings['priority'] : 0,
		);

		$new_form_id = wp_update_post( $post_arr, true );

		if ( is_wp_error( $new_form_id ) ) {
			wp_send_json_error( $new_form_id->get_error_message() );
		}

		$form_filters_utils = new WCAPF_Form_Filters_Utils();

		list( $filters, $errors ) = $form_filters_utils->save_form_filters( $form_filters, $new_form_id );

		if ( $errors ) {
			wp_send_json_error( array( 'errors' => $errors ) );
		}

		/**
		 * Register a hook that is triggerred after a form is saved.
		 */
		do_action( 'wcapf_form_saved', $new_form_id );

		$filters_for_ui = array();

		foreach ( $filters as $filter ) {
			$filters_for_ui[] = $this->parse_filter_data( $filter );
		}

		$form_settings = $this->parse_form_settings( $sanitized_form_settings );

		$response = array(
			'filter_keys'   => WCAPF_API_Utils::get_filter_keys(),
			'form_filters'  => $filters_for_ui,
			'form_settings' => $form_settings,
		);

		$response['show_review_notice'] = WCAPF_Helper::review_notice_for_milestone_achieved_can_be_shown();

		wp_send_json_success( $response );
	}

	/**
	 * Sanitize the filter data before saving into the database.
	 *
	 * @param array $form The form data.
	 *
	 * @return array
	 */
	private function sanitize_form_settings( $form ) {
		$sanitized = array();

		foreach ( $form as $key => $value ) {
			$value = sanitize_text_field( $value );

			$sanitized[ $key ] = $value;
		}

		return $sanitized;
	}

	/**
	 * Gets the taxonomy terms for the manual options modal via ajax.
	 *
	 * @return void
	 */
	public function get_terms_for_modal() {
		$this->verify_nonce();
		$this->verify_permission();

		$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_text_field( $_GET['taxonomy'] ) : '';
		$number   = apply_filters( 'wcapf_max_number_of_terms_for_browse_options_modal', 99 );

		$args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'fields'     => 'id=>name',
			'number'     => absint( $number ),
		);

		$terms    = get_terms( $args );
		$response = array();

		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term_id => $term_name ) {
				$response[] = array(
					'value' => $term_id,
					'label' => $term_name,
				);
			}
		}

		wp_send_json_success( $response );
	}

	/**
	 * Gets the taxonomy terms via ajax.
	 *
	 * @return void
	 */
	public function get_terms_for_dropdown() {
		$this->verify_nonce();
		$this->verify_permission();

		$taxonomy    = isset( $_GET['taxonomy'] ) ? sanitize_text_field( $_GET['taxonomy'] ) : '';
		$only_parent = isset( $_GET['only_parent'] ) ? sanitize_text_field( $_GET['only_parent'] ) : '';
		$keyword     = isset( $_GET['keyword'] ) ? sanitize_text_field( $_GET['keyword'] ) : '';
		$page        = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;

		$per_page = 20;
		$offset   = ( $page - 1 ) * $per_page;

		$args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'search'     => $keyword,
			'number'     => $per_page,
			'offset'     => $offset,
			'fields'     => 'id=>name',
		);

		if ( 'true' === $only_parent ) {
			$includes = array();

			$all_terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
					'fields'     => 'ids',
				)
			);

			if ( $all_terms && ! is_wp_error( $all_terms ) ) {
				foreach ( $all_terms as $term_id ) {
					if ( get_term_children( $term_id, $taxonomy ) ) {
						$includes[] = $term_id;
					}
				}
			}

			$args['include'] = $includes;
		}

		$terms = get_terms( $args );

		$response = array();

		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term_id => $term_name ) {
				$response[] = array(
					'label' => $term_name,
					'value' => absint( $term_id ),
				);
			}
		}

		wp_send_json_success( $response );
	}

	/**
	 * Gets the post authors via ajax.
	 *
	 * @return void
	 */
	public function get_authors_for_dropdown() {
		$this->verify_nonce();
		$this->verify_permission();

		$keyword = isset( $_GET['keyword'] ) ? sanitize_text_field( $_GET['keyword'] ) : '';
		$page    = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;

		$per_page = 20;
		$offset   = ( $page - 1 ) * $per_page;

		$args = array(
			'search' => $keyword,
			'number' => $per_page,
			'offset' => $offset,
			'fields' => array( 'ID', 'display_name' ),
		);

		$users    = get_users( $args );
		$response = array();

		if ( $users ) {
			foreach ( $users as $user ) {
				$response[] = array(
					'label' => $user->display_name,
					'value' => absint( $user->ID ),
				);
			}
		}

		wp_send_json_success( $response );
	}

	/**
	 * Gets the pages via ajax.
	 *
	 * @return void
	 */
	public function get_pages_for_dropdown() {
		$this->verify_nonce();
		$this->verify_permission();

		$keyword  = isset( $_GET['keyword'] ) ? sanitize_text_field( $_GET['keyword'] ) : '';
		$page     = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;
		$show_all = ! empty( $_GET['show_all'] ) ? true : false;

		$per_page = 20;
		$offset   = ( $page - 1 ) * $per_page;

		$args = array(
			'post_type'      => 'page',
			's'              => $keyword,
			'posts_per_page' => $per_page,
			'offset'         => $offset,
		);

		if ( $show_all ) {
			$wc_pages = array( 'cart', 'checkout', 'myaccount', 'terms' );
		} else {
			$wc_pages = array( 'shop', 'cart', 'checkout', 'myaccount', 'terms' );
		}

		$page_ids = array();

		foreach ( $wc_pages as $page ) {
			$wc_page_id = wc_get_page_id( $page );

			if ( 0 < $wc_page_id ) {
				$page_ids[] = $wc_page_id;
			}
		}

		if ( $page_ids ) {
			$args['exclude'] = $page_ids;
		}

		$pages    = get_posts( $args );
		$response = array();

		if ( $pages ) {
			foreach ( $pages as $page ) {
				$page_id = $page->ID;

				$response[] = array(
					'label'     => $page->post_title,
					'value'     => absint( $page_id ),
					'permalink' => get_the_permalink( $page_id ),
				);
			}
		}

		wp_send_json_success( $response );
	}

	/**
	 * Gets the products via ajax.
	 *
	 * @return void
	 */
	public function get_products_for_dropdown() {
		$this->verify_nonce();
		$this->verify_permission();

		$keyword = isset( $_GET['keyword'] ) ? sanitize_text_field( $_GET['keyword'] ) : '';
		$page    = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;

		$per_page = 20;
		$offset   = ( $page - 1 ) * $per_page;

		$args = array(
			'post_type'      => 'product',
			's'              => $keyword,
			'posts_per_page' => $per_page,
			'offset'         => $offset,
		);

		$products = get_posts( $args );
		$response = array();

		if ( $products ) {
			foreach ( $products as $product ) {
				$response[] = array(
					'label' => $product->post_title,
					'value' => absint( $product->ID ),
				);
			}
		}

		wp_send_json_success( $response );
	}

	/**
	 * Gets the meta values via ajax.
	 *
	 * @return void
	 */
	public function get_meta_values_for_modal() {
		$this->verify_nonce();
		$this->verify_permission();

		$meta_key = isset( $_GET['meta_key'] ) ? sanitize_text_field( $_GET['meta_key'] ) : '';

		$values   = WCAPF_Helper::get_available_meta_values( $meta_key );
		$response = array();

		if ( $values ) {
			foreach ( $values as $value ) {
				$response[] = array(
					'value' => $value,
					'label' => $value,
				);
			}
		}

		wp_send_json_success( $response );
	}

	/**
	 * Gets the post authors via ajax.
	 *
	 * @return void
	 */
	public function get_post_authors_for_modal() {
		$this->verify_nonce();
		$this->verify_permission();

		$roles = isset( $_GET['roles'] ) ? $_GET['roles'] : array();

		$args = array(
			'role__in' => $roles,
			'fields'   => array( 'ID', 'display_name' ),
		);

		$users    = get_users( $args );
		$response = array();

		if ( $users ) {
			foreach ( $users as $user ) {
				$response[] = array(
					'label' => $user->display_name,
					'value' => $user->ID,
				);
			}
		}

		wp_send_json_success( $response );
	}

	/**
	 * Deletes the form via ajax.
	 *
	 * @return void
	 */
	public function delete_form() {
		$this->verify_nonce();
		$this->verify_permission();

		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : '';

		if ( $post_id && 'wcapf-form' === get_post_type( $post_id ) ) {
			$filters = get_posts(
				array(
					'post_type'   => 'wcapf-filter',
					'post_status' => 'any',
					'post_parent' => $post_id,
					'nopaging'    => true,
					'fields'      => 'ids',
				)
			);

			$delete = wp_delete_post( $post_id, true );

			if ( $delete ) {
				foreach ( $filters as $filter_id ) {
					wp_delete_post( $filter_id, true );
				}

				wp_send_json_success( __( 'Form deleted successfully', 'wc-ajax-product-filter' ) );
			} else {
				wp_send_json_error(
					__( 'There was a problem deleting the form, please try again.', 'wc-ajax-product-filter' )
				);
			}
		} else {
			wp_send_json_error( __( 'Invalid form id', 'wc-ajax-product-filter' ) );
		}
	}

	/**
	 * Deletes the filter via ajax.
	 *
	 * @return void
	 */
	public function delete_filter() {
		$this->verify_nonce();
		$this->verify_permission();

		$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : '';

		if ( $post_id && 'wcapf-filter' === get_post_type( $post_id ) ) {
			$delete = wp_delete_post( $post_id, true );

			if ( $delete ) {
				wp_send_json_success( __( 'Filter deleted successfully', 'wc-ajax-product-filter' ) );
			} else {
				wp_send_json_error(
					__( 'There was a problem deleting the filter, please try again.', 'wc-ajax-product-filter' )
				);
			}
		} else {
			wp_send_json_error( __( 'Invalid filter id', 'wc-ajax-product-filter' ) );
		}
	}

	/**
	 * Saves the plugin settings via ajax.
	 *
	 * @return void
	 */
	public function save_settings() {
		$this->verify_nonce();
		$this->verify_permission();

		$_settings          = isset( $_POST['settings'] ) ? $_POST['settings'] : array();
		$_filter_keys       = isset( $_POST['filter_keys'] ) ? $_POST['filter_keys'] : array();
		$update_filter_keys = isset( $_POST['update_filter_keys'] ) ? $_POST['update_filter_keys'] : '';

		$settings = stripslashes( $_settings );
		$settings = json_decode( $settings, true );

		/**
		 * The hooks for altering the form settings.
		 */
		$sanitized_settings = apply_filters(
			'wcapf_sanitize_settings',
			$this->sanitize_settings_data( $settings ),
			$settings
		);

		update_option( WCAPF_Helper::settings_option_key(), $sanitized_settings );

		if ( ! $update_filter_keys ) {
			wp_send_json_success(
				array(
					'settings'           => WCAPF_API_Utils::get_settings(),
					'global_filter_keys' => WCAPF_API_Utils::get_filter_keys( true ),
				)
			);
		}

		$filter_keys = stripslashes( $_filter_keys );
		$filter_keys = json_decode( $filter_keys, true );

		$possible_types  = WCAPF_API_Utils::get_filter_types();
		$new_filter_keys = array();
		$errors          = array();

		$form_filter_utils = new WCAPF_Form_Filters_Utils();

		foreach ( $filter_keys as $order => $filter ) {
			$post_name = isset( $filter['field_key'] ) ? sanitize_title( $filter['field_key'] ) : '';
			$type      = isset( $filter['type'] ) ? $filter['type'] : '';
			$taxonomy  = isset( $filter['taxonomy'] ) ? $filter['taxonomy'] : '';
			$meta_key  = isset( $filter['meta_key'] ) ? $filter['meta_key'] : '';

			list( $post_name, $error_data ) = $form_filter_utils->retrieve_filter_key(
				$type,
				$possible_types,
				$taxonomy,
				$filter,
				$post_name,
				$meta_key,
				$order
			);

			if ( $error_data ) {
				$errors[] = $error_data;

				continue;
			}

			// Don't proceed if error found from previous filters.
			if ( $errors ) {
				continue;
			}

			$filter['field_key'] = $post_name;

			$new_filter_keys[] = $filter;
		}

		if ( $errors ) {
			wp_send_json_error( array( 'errors' => $errors ) );
		}

		$update_filter_key_errors = array();

		foreach ( $new_filter_keys as $filter ) {
			$post_name = $filter['field_key'];

			// Fetch the filters with old filter keys.
			$filters = get_posts(
				array(
					'post_type' => 'wcapf-filter',
					'name'      => $filter['_field_key'],
				)
			);

			// Update the filter keys for each filter found.
			foreach ( $filters as $filter_data ) {
				$filter_settings = maybe_unserialize( $filter_data->post_content );

				// Update the field key.
				$filter_settings['field_key'] = $post_name;

				$post_arr = array(
					'ID'           => $filter_data->ID,
					'post_name'    => $post_name,
					'post_content' => maybe_serialize( $filter_settings ),
				);

				add_filter( 'pre_wp_unique_post_slug', function () use ( $post_name ) {
					return $post_name;
				} );

				$updated_filter_id = wp_update_post( $post_arr, true );

				if ( is_wp_error( $updated_filter_id ) ) {
					$update_filter_key_errors[] = $updated_filter_id->get_error_message();
				}
			}
		}

		if ( $update_filter_key_errors ) {
			// Send the first error only.
			wp_send_json_error( $update_filter_key_errors[0] );
		}

		/**
		 * Hook to run functions after filter keys are updated.
		 */
		do_action( 'wcapf_after_update_filter_keys', $new_filter_keys );

		wp_send_json_success(
			array(
				'settings'           => WCAPF_API_Utils::get_settings(),
				'global_filter_keys' => WCAPF_API_Utils::get_filter_keys( true ),
			)
		);
	}

	/**
	 * Sanitizes the settings data coming from react ui.
	 *
	 * @param array $settings The plugin settings array.
	 *
	 * @return array
	 */
	private function sanitize_settings_data( $settings ) {
		$sanitized = array();

		foreach ( $settings as $key => $value ) {
			if ( 'author_roles' === $key ) {
				$value = wp_list_pluck( $settings['author_roles'], 'value' );
			} elseif ( 'sort_by_form' === $key ) {
				$value = absint( $value );
			} else {
				$value = sanitize_text_field( $value );
			}

			$sanitized[ $key ] = $value;
		}

		return $sanitized;
	}

	/**
	 * Track the form updates count to show the review notice.
	 *
	 * @return void
	 */
	public function track_form_updates_count() {
		$user_id  = get_current_user_id();
		$meta_key = 'wcapf_form_updates_count';

		$form_updates_count = intval( get_user_meta( $user_id, $meta_key, true ) );

		if ( $form_updates_count ) {
			$form_updates_count ++;
		} else {
			$form_updates_count = 1;
		}

		update_user_meta( $user_id, $meta_key, $form_updates_count );
	}

}

WCAPF_API::instance();
