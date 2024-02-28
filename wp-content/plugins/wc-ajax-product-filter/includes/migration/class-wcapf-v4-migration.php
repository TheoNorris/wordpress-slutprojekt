<?php
/**
 * WCAPF_V4_Migration class.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes/migration
 * @author     wptools.io
 */

/**
 * WCAPF_V4_Migration class.
 *
 * @since 4.0.0
 */
class WCAPF_V4_Migration {

	/**
	 * The constructor.
	 */
	private function __construct() {
	}

	/**
	 * Returns an instance of this class.
	 *
	 * @return WCAPF_V4_Migration
	 */
	public static function instance() {
		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance ) {
			$instance = new WCAPF_V4_Migration();
		}

		return $instance;
	}

	public function try_to_run_v4_migration() {
		$db_version_option_key  = 'wcapf_db_version';
		$existing_wcapf_version = get_option( $db_version_option_key );

		$perform_v4_migration = false;

		if ( version_compare( $existing_wcapf_version, '4.0.0', '<' ) ) {
			$perform_v4_migration = true;
		} elseif ( get_option( 'wcapf_run_migrate' ) ) {
			$perform_v4_migration = true;
		}

		if ( $perform_v4_migration ) {
			$this->do_migrate();

			error_log( 'Ran the automatic wcapf v4 migration.' );

			// Show the v4 migration notice.
			update_option( 'wcapf_v4_migration_notice_status', '1' );

			// Show the review filters notice after v4 migration.
			update_option( 'wcapf_v4_review_filters_notice_status', '1' );

			// Update the db version.
			$plugin_version = defined( 'WCAPF_BASIC_VERSION' ) ? WCAPF_BASIC_VERSION : WCAPF_VERSION;
			update_option( $db_version_option_key, $plugin_version );

			// We don't want to migrate again.
			delete_option( 'wcapf_run_migrate' );

			// Clear the forms with locations transients.
			delete_transient( 'wcapf_forms_with_locations' );
		}

		$set_default_settings = false;

		if ( get_option( 'wcapf_set_default_settings' ) ) {
			$set_default_settings = true;
		} elseif ( get_option( 'wcapf_update_default_settings' ) ) {
			$set_default_settings = true;
		}

		$settings_option_key = 'wcapf_settings';

		if ( $set_default_settings ) {
			$default_settings  = WCAPF_Default_Data::default_settings();
			$existing_settings = get_option( $settings_option_key, array() );

			update_option( $settings_option_key, array_merge( $default_settings, $existing_settings ) );

			// We don't want to set the settings again.
			delete_option( 'wcapf_set_default_settings' );
			delete_option( 'wcapf_update_default_settings' );
		}
	}

	/**
	 * @return void
	 */
	public function do_migrate() {
		$transient_name = 'wcapf_v4_migration_status';

		// Don't run the migration if already running.
		if ( get_transient( $transient_name ) ) {
			return;
		}

		set_transient( $transient_name, 1 );

		$this->migrate_settings();
		$this->migrate_filters();

		delete_transient( $transient_name );
	}

	/**
	 * Migrate the plugin settings.
	 *
	 * @return void
	 */
	public function migrate_settings() {
		$option_key  = 'wcapf_settings';
		$v3_settings = get_option( $option_key );
		$v4_settings = array();

		$default_settings = WCAPF_Default_Data::default_settings();

		foreach ( $default_settings as $key => $_value ) {
			$mapped_key = $key;

			// Map key.
			if ( 'sorting_data_in_active_filters' === $key ) {
				$mapped_key = 'show_sorting_data_in_active_filters';
			}

			if ( 'attach_combobox_on_sorting' === $key ) {
				$mapped_key = 'attach_chosen_on_sorting';
			}

			if ( isset( $v3_settings[ $mapped_key ] ) ) {
				$value = $v3_settings[ $mapped_key ];
			} else {
				// Default data.
				$value = $_value;
			}

			// Loading animation.
			if ( 'loading_animation' === $key ) {
				$value = 'overlay-with-icon';
			}

			// Initially we disable the scroll to element.
			if ( 'scroll_window' === $key ) {
				$value = 'none';
			}

			$v4_settings[ $key ] = $value;
		}

		update_option( $option_key, $v4_settings );

		error_log( 'The wcapf settings migrated successfully!' );
	}

	/**
	 * Migrate the filters.
	 *
	 * @return void
	 */
	public function migrate_filters() {
		// Retrieve all filters.
		$filters = get_posts(
			array(
				'post_type'   => 'wcapf-filter',
				'post_status' => 'any',
				'nopaging'    => true,
				'orderby'     => 'ID', // Sort by ID
				'order'       => 'ASC', // Ascending order
			)
		);

		// Custom sorting function to prioritize 'publish' status.
		usort( $filters, function ( $a, $b ) {
			if ( $a->post_status === 'publish' && $b->post_status !== 'publish' ) {
				return - 1; // $a comes before $b
			} elseif ( $a->post_status !== 'publish' && $b->post_status === 'publish' ) {
				return 1; // $b comes before $a
			} else {
				return $a->ID - $b->ID; // Sort by ID in ascending order
			}
		} );

		$filter_default_data = WCAPF_Default_Data::filter_default_data();

		$taxonomy_types  = array( 'custom-taxonomy', 'attribute', 'category', 'tag' );
		$component_types = array( 'reset-button', 'active-filters' );

		$migrated_filters = array();

		foreach ( $filters as $filter ) {
			$v3_field_data = get_post_meta( $filter->ID, '_field_data', true );

			if ( ! $v3_field_data ) {
				continue;
			}

			$migrated_data = array();

			// Set the post_status.
			$migrated_data['post_status'] = $filter->post_status;

			foreach ( $filter_default_data as $key => $_value ) {
				$mapped_key = $key;

				// Map key.
				if ( 'id' === $key ) {
					$mapped_key = 'field_id';
				}

				if ( isset( $v3_field_data[ $mapped_key ] ) ) {
					$value = $v3_field_data[ $mapped_key ];
				} else {
					// Default data.
					$value = $_value;
				}

				// Title.
				if ( 'title' === $key ) {
					$value = $filter->post_title;
				}

				// Taxonomy type.
				if ( 'type' === $key && in_array( $value, $taxonomy_types ) ) {
					$value = 'taxonomy';
				}

				// Component type.
				if ( 'type' === $key && in_array( $value, $component_types ) ) {
					$v3_field_data['component'] = $value;

					$value = 'component';
				}

				// Post property.
				if ( 'type' === $key && 'post-property' === $value ) {
					if ( isset( $v3_field_data['post_property'] ) && 'post_author' === $v3_field_data['post_property'] ) {
						$value = 'post-author';
					}
				}

				// Soft limit.
				if ( 'enable_reduce_height' === $key && ! empty( $v3_field_data['enable_soft_limit'] ) ) {
					$value = 'soft_limit';
				}

				$migrated_data[ $key ] = $value;
			}

			if ( 'taxonomy' === $migrated_data['type'] ) {
				// Set taxonomy.
				if ( 'category' === $v3_field_data['type'] ) {
					$migrated_data['taxonomy'] = 'product_cat';
				} elseif ( 'tag' === $v3_field_data['type'] ) {
					$migrated_data['taxonomy'] = 'product_tag';
				}

				// Tax hierarchical data.
				if ( ! empty( $migrated_data['taxonomy'] ) ) {
					$hierarchical = '';

					if ( is_taxonomy_hierarchical( $migrated_data['taxonomy'] ) ) {
						$hierarchical = '1';
					}

					$migrated_data['taxHierarchical'] = $hierarchical;
				}

				// Order data.
				if ( empty( $migrated_data['order_terms_by'] ) ) {
					$migrated_data['order_terms_by'] = 'default';
				}

				if ( empty( $migrated_data['order_terms_dir'] ) ) {
					$migrated_data['order_terms_dir'] = 'asc';
				}

				// Options data.
				$include_terms = array();
				$exclude_terms = array();
				$parent_term   = 0;
				$child_terms   = array();

				if ( ! empty( $v3_field_data['limit_values_by_id'] ) ) {
					$include_terms = explode( ',', $v3_field_data['limit_values_by_id'] );

					$migrated_data['include_terms'] = $include_terms;
				}

				if ( ! empty( $v3_field_data['exclude_values_id'] ) ) {
					$exclude_terms = explode( ',', $v3_field_data['exclude_values_id'] );

					$migrated_data['exclude_terms'] = $exclude_terms;
				}

				if ( ! empty( $v3_field_data['parent_term'] ) ) {
					$parent_term = absint( $v3_field_data['parent_term'] );

					if ( $parent_term ) {
						$migrated_data['parent_term'] = $parent_term;

						$children = get_term_children( $parent_term, $migrated_data['taxonomy'] );

						if ( $children ) {
							$child_terms = $children;
						}
					}
				}

				$limit_by = $migrated_data['limit_options'];

				if ( ! empty( $v3_field_data['custom_appearance_options'] ) ) {
					$display_type    = $migrated_data['display_type'];
					$appearance_data = $v3_field_data['custom_appearance_options'];

					$manual_options = array();

					foreach ( $appearance_data as $id => $data ) {
						$data = array_merge(
							$data,
							array(
								'label'                   => '',
								'tooltip'                 => '',
								'value'                   => $id,
								'swatch'                  => $display_type,
								'secondary_color_enabled' => '',
								'secondary_color'         => '',
							)
						);

						switch ( $limit_by ) {
							case 'include':
								if ( $include_terms && in_array( $id, $include_terms ) ) {
									$manual_options[] = $data;
								}

								break;

							case 'exclude':
								if ( $exclude_terms && ! in_array( $id, $exclude_terms ) ) {
									$manual_options[] = $data;
								}

								break;

							case 'child':
								if ( $parent_term && $child_terms && in_array( $id, $child_terms ) ) {
									$manual_options[] = $data;
								}

								break;

							default:
								$manual_options[] = $data;

								break;
						}
					}

					$migrated_data['manual_options'] = $manual_options;
				}

				$swatch_types    = array( 'image', 'color' );
				$v3_display_type = $migrated_data['display_type'];

				if ( in_array( $v3_display_type, $swatch_types ) ) {
					$migrated_data['get_options'] = 'manual_entry';

					$migrated_data['display_type']               = 'label';
					$migrated_data['custom_display_type_layout'] = 'inline';

					// Enable swatches.
					$migrated_data['enable_swatch']     = '1';
					$migrated_data['swatch_type']       = $v3_display_type;
					$migrated_data['swatch_with_label'] = '';
				}
			}

			// Hide Active filters.
			if ( 'component' === $migrated_data['type'] && 'active-filters' === $migrated_data['component'] ) {
				$show_if_empty = $v3_field_data['show_if_empty'];

				if ( ! $show_if_empty ) {
					$migrated_data['empty_filter_message'] = '';
				}
			}

			$migrated_filters[] = $migrated_data;
		}

		if ( ! $migrated_filters ) {
			return;
		}

		$form_settings = WCAPF_Default_Data::form_default_data();

		$post_arr = array(
			'post_title'   => __( 'Default form', 'wc-ajax-product-filter' ),
			'post_content' => maybe_serialize( $form_settings ),
			'menu_order'   => 0,
			'post_type'    => 'wcapf-form',
			'post_status'  => 'publish',
		);

		$new_form_id = wp_insert_post( $post_arr, true );

		$form_filters_utils = new WCAPF_Form_Filters_Utils();

		list( , $errors ) = $form_filters_utils->save_form_filters( $migrated_filters, $new_form_id, true );

		if ( $errors ) {
			$message = 'The following error occurred when trying to migrate the filter data for v4:';
			$message .= "\n";
			$message .= print_r( $errors, true );

			error_log( $message );
		} else {
			update_option( 'wcapf_migrated_filters_form_id', $new_form_id );

			error_log( 'The wcapf filters data migrated successfully!' );
		}
	}

}

if ( ! function_exists( 'WCAPF_V4_Migration' ) ) {
	/**
	 * Return single instance for WCAPF_V4_Migration class.
	 *
	 * @since 4.0.0
	 *
	 * @return WCAPF_V4_Migration
	 */
	function WCAPF_V4_Migration() {
		return WCAPF_V4_Migration::instance();
	}
}
