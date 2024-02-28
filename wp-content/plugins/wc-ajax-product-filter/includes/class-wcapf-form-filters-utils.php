<?php
/**
 * The form filters class that saves the form filters to the database.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/includes
 * @author     wptools.io
 */

/**
 * WCAPF_Form_Filters_Utils class.
 *
 * @since 4.0.0
 */
class WCAPF_Form_Filters_Utils {

	/**
	 * @param array $form_filters
	 * @param int   $new_form_id
	 * @param bool  $migrate
	 *
	 * @return array
	 */
	public function save_form_filters( $form_filters, $new_form_id, $migrate = false ) {
		$possible_types = WCAPF_API_Utils::get_filter_types();
		$valid_types    = wp_list_pluck( $possible_types, 'value' );
		$filter_types   = array();
		$filters        = array();
		$errors         = array();

		// Check for error coming for filter keys.
		if ( $form_filters ) {
			foreach ( $form_filters as $filter_order => $filter ) {
				list(
					,
					,
					$post_name,
					$type,
					$taxonomy,
					$meta_key,
					) = $this->get_filter_data( $filter );

				list( , $error_data ) = $this->retrieve_filter_key(
					$type,
					$possible_types,
					$taxonomy,
					$filter,
					$post_name,
					$meta_key,
					$filter_order
				);

				if ( $error_data ) {
					$errors[] = $error_data;
				}
			}
		}

		if ( $errors ) {
			wp_send_json_error( array( 'errors' => $errors ) );
		}

		if ( $form_filters ) {
			foreach ( $form_filters as $filter_order => $filter ) {
				$is_new       = isset( $filter['isNew'] ) ? $filter['isNew'] : '';
				$unique_index = isset( $filter['uniqueIndex'] ) ? $filter['uniqueIndex'] : '';

				list(
					$filter_title,
					$filter_id,
					$post_name,
					$type,
					$taxonomy,
					$meta_key,
					$component
					) = $this->get_filter_data( $filter );

				if ( ! in_array( $type, $valid_types ) ) {
					continue;
				}

				if ( 'taxonomy' === $type && ! $taxonomy ) {
					continue;
				} elseif ( 'post-meta' === $type && ! $meta_key ) {
					continue;
				} elseif ( 'component' === $type && ! $component ) {
					continue;
				}

				list( $post_name, , $filter_type_data ) = $this->retrieve_filter_key(
					$type,
					$possible_types,
					$taxonomy,
					$filter,
					$post_name,
					$meta_key,
					$filter_order
				);

				if ( 'taxonomy' === $type ) {
					$taxonomy = isset( $filter['taxonomy'] ) ? sanitize_text_field( $filter['taxonomy'] ) : '';

					if ( ! $taxonomy ) {
						continue;
					}

					$filter_type = $type . '>' . $taxonomy;
				} elseif ( 'post-meta' === $type ) {
					$meta_key = isset( $filter['meta_key'] ) ? sanitize_text_field( $filter['meta_key'] ) : '';

					if ( ! $meta_key ) {
						continue;
					}

					$filter_type = $type . '>' . $meta_key;
				} elseif ( 'component' === $type ) {
					$filter_type = $type . '>' . $component;
				} else {
					$filter_type = $type;
				}

				// Don't add same filter type in a form multiple times.
				if ( in_array( $filter_type, $filter_types ) ) {
					$post_status = 'draft';
				} else {
					$post_status = 'publish';

					// When migrating we'll keep the existing post_status.
					if ( $migrate && ! empty( $filter['post_status'] ) ) {
						$post_status = sanitize_text_field( $filter['post_status'] );

						unset( $filter['post_status'] );
					}
				}

				if ( 'component' !== $type ) {
					$filter_types[] = $filter_type;
				}

				if ( 'component' !== $type && ! $filter_title ) {
					if ( 'post-meta' === $type ) {
						$filter_title = $filter_type_data['label'] . '[' . $meta_key . ']';
					} else {
						$filter_title = $filter_type_data['label'];
					}
				}

				if ( 'component' === $type && 'active-filters' === $component && ! $filter_title ) {
					$filter_title = __( 'Active Filters', 'wc-ajax-product-filter' );
				}

				$post_arr = array( 'post_title' => $filter_title );

				if ( $filter_id && 'wcapf-filter' === get_post_type( $filter_id ) ) {
					$post_arr['ID']          = $filter_id;
					$post_arr['post_status'] = $post_status;

					if ( $migrate ) {
						$post_arr['post_parent']  = $new_form_id;
						$post_arr['post_excerpt'] = $filter_type;
					}

					$new_filter_id = wp_update_post( $post_arr, true );
				} else {
					$post_arr['post_type']    = 'wcapf-filter';
					$post_arr['post_status']  = $post_status;
					$post_arr['post_parent']  = $new_form_id;
					$post_arr['post_excerpt'] = $filter_type;

					$new_filter_id = wp_insert_post( $post_arr, true );
				}

				if ( is_wp_error( $new_filter_id ) ) {
					continue;
				}

				if ( 'component' === $type ) {
					$post_name = '';
				}

				$filter['id']        = $new_filter_id;
				$filter['title']     = $filter_title;
				$filter['field_key'] = $post_name;

				$sanitized = $this->sanitize_filter_data( $filter, $migrate );

				// Remove 'isNew', 'uniqueIndex' when saving into the database.
				if ( isset( $sanitized['isNew'] ) ) {
					unset( $sanitized['isNew'] );
				}

				if ( isset( $sanitized['uniqueIndex'] ) ) {
					unset( $sanitized['uniqueIndex'] );
				}

				$post_arr = array(
					'ID'           => $new_filter_id,
					'post_content' => maybe_serialize( $sanitized ),
					'post_name'    => $post_name,
					'menu_order'   => $filter_order,
				);

				add_filter( 'pre_wp_unique_post_slug', function () use ( $post_name ) {
					return $post_name;
				} );

				$new_filter_id = wp_update_post( $post_arr, true );

				if ( ! is_wp_error( $new_filter_id ) ) {
					// Add 'isNew', 'uniqueIndex' when returning the filter data for React UI.
					if ( $is_new ) {
						$sanitized['isNew'] = $is_new;
					}

					if ( $unique_index ) {
						$sanitized['uniqueIndex'] = $unique_index;
					}

					$filters[] = $sanitized;
				}
			}
		}

		return array( $filters, $errors );
	}

	/**
	 * @param $filter
	 *
	 * @return array
	 */
	private function get_filter_data( $filter ) {
		$filter_title = isset( $filter['title'] ) ? sanitize_text_field( $filter['title'] ) : '';
		$filter_id    = isset( $filter['id'] ) ? absint( $filter['id'] ) : 0;
		$post_name    = isset( $filter['field_key'] ) ? sanitize_title( $filter['field_key'] ) : '';
		$type         = isset( $filter['type'] ) ? sanitize_text_field( $filter['type'] ) : '';
		$taxonomy     = isset( $filter['taxonomy'] ) ? sanitize_text_field( $filter['taxonomy'] ) : '';
		$meta_key     = isset( $filter['meta_key'] ) ? sanitize_text_field( $filter['meta_key'] ) : '';
		$component    = isset( $filter['component'] ) ? sanitize_text_field( $filter['component'] ) : '';

		return array( $filter_title, $filter_id, $post_name, $type, $taxonomy, $meta_key, $component );
	}

	/**
	 * Tries to retrieve the filter key for the filter.
	 *
	 * @param string $type
	 * @param array  $possible_types
	 * @param string $taxonomy
	 * @param array  $filter_data
	 * @param string $post_name
	 * @param string $meta_key
	 * @param int    $filter_order
	 *
	 * @return array
	 */
	public function retrieve_filter_key(
		$type,
		$possible_types,
		$taxonomy,
		$filter_data,
		$post_name,
		$meta_key,
		$filter_order
	) {
		if ( 'component' === $type ) {
			return array(
				$post_name,
				array(), // Empty error data.
				array(), // Empty filter type data.
			);
		}

		if ( 'taxonomy' === $type ) {
			$taxonomy_index    = array_search( 'taxonomy', array_column( $possible_types, 'value' ) );
			$taxonomy_options  = $possible_types[ $taxonomy_index ];
			$taxonomy_types    = $taxonomy_options['options'];
			$filter_type_index = array_search( $taxonomy, array_column( $taxonomy_types, 'value' ) );
			$filter_type_data  = $taxonomy_types[ $filter_type_index ];
		} else {
			$filter_type_index = array_search( $type, array_column( $possible_types, 'value' ) );
			$filter_type_data  = $possible_types[ $filter_type_index ];
		}

		$global_filter_key = WCAPF_API_Utils::get_global_filter_key( $filter_data );

		// Try to grab the global filter key when possible.
		if ( ! $post_name && $global_filter_key ) {
			$post_name = $global_filter_key;
		}

		// Set default filter key.
		if ( ! $post_name ) {
			if ( 'post-meta' === $type ) {
				$post_name = '_' . $meta_key;
			} else {
				$post_name = $filter_type_data['key'];
			}
		}

		$error_data = array();

		// Check if pro version found and pretty url is enabled then don't generate the below errors.
		// if ( ! WCAPF_Helper::found_pro_version() ) {

		if ( post_type_exists( $post_name ) ) {
			$error_data = array(
				'key'       => 'field_key_error_',
				'message'   => $this->generate_filter_key_error_message( 'post-type' ),
				'order'     => $filter_order,
				'field_key' => $post_name,
			);
		} elseif ( taxonomy_exists( $post_name ) ) {
			$error_data = array(
				'key'       => 'field_key_error_',
				'message'   => $this->generate_filter_key_error_message( 'taxonomy' ),
				'order'     => $filter_order,
				'field_key' => $post_name,
			);
		} elseif ( 's' === $post_name ) {
			$message = __( 'This is a reserved key. Please use a different string.', 'wc-ajax-product-filter' );

			$error_data = array(
				'key'       => 'field_key_error_',
				'message'   => $message,
				'order'     => $filter_order,
				'field_key' => $post_name,
			);
		}

		// }

		return array( $post_name, $error_data, $filter_type_data );
	}

	/**
	 * Generates the filter key error message.
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	private function generate_filter_key_error_message( $type ) {
		$post_type_err = __(
			"Post type name can't be used as a filter key, it'll create conflict.",
			'wc-ajax-product-filter'
		);

		$tax_type_err = __(
			"Taxonomy name can't be used as a filter key, it'll create conflict.",
			'wc-ajax-product-filter'
		);

		$common_err = __( 'Please make it unique by adding an underscore.', 'wc-ajax-product-filter' );

		return sprintf(
			'%s %s',
			'taxonomy' === $type ? $tax_type_err : $post_type_err,
			$common_err
		);
	}

	/**
	 * Sanitize the filter data before saving into the database.
	 *
	 * @param array $filter  The filter data.
	 * @param bool  $migrate Determines if we are migrating.
	 *
	 * @return array
	 */
	private function sanitize_filter_data( $filter, $migrate ) {
		$float_fields  = array( 'min_value', 'max_value', 'step' );
		$absint_fields = array( 'id', 'decimal_places', 'soft_limit', 'max_height', 'gap' );

		$limit_fields = array(
			'include_terms',
			'exclude_terms',
			'include_authors',
			'exclude_authors',
			'include_user_roles',
		);

		$single_array_fields = array( 'parent_term' );

		$value_may_have_spaces = array(
			'value_prefix',
			'value_postfix',
			'values_separator',
			'text_before_min_value',
			'text_before_max_value',
		);

		$markup_fields = array( 'help_text' );

		$sanitized_filter = array();

		foreach ( $filter as $key => $value ) {
			if ( in_array( $key, $float_fields ) ) {
				$value = floatval( $value );

				if ( 'min_value' === $key && ! $value ) {
					$value = 0;
				}

				if ( 'max_value' === $key && ! $value ) {
					$value = 100;
				}

				if ( 'step' === $key && ! $value ) {
					$value = 1;
				}
			} elseif ( in_array( $key, $absint_fields ) ) {
				$value = absint( $value );
			} elseif ( in_array( $key, $limit_fields ) ) {
				if ( ! $migrate ) {
					// Pick the ids only.
					$value = wp_list_pluck( $value, 'value' );
					$value = array_map( 'sanitize_text_field', $value );
				}
			} elseif ( in_array( $key, $single_array_fields ) ) {
				$value = isset( $value['value'] ) ? $value['value'] : '';
			} elseif ( in_array( $key, $value_may_have_spaces ) ) {
				$value = str_replace( ' ', '&nbsp;', $value );

				$with_markup = array( 'values_separator', 'text_before_min_value', 'text_before_max_value' );

				if ( in_array( $key, $with_markup ) ) {
					$value = wp_kses_data( $value );
				} else {
					$value = sanitize_text_field( $value );
				}
			} elseif ( in_array( $key, $markup_fields ) ) {
				$value = wp_kses_post( $value );
			} elseif ( 'product_status_options' === $key ) {
				$value = $this->sanitize_product_status_options( $value );
			} else {
				if ( ! $migrate ) {
					$value = sanitize_text_field( $value );
				}
			}

			$sanitized_filter[ $key ] = $value;
		}

		return apply_filters( 'wcapf_sanitize_filter_data', $sanitized_filter, $filter, $migrate );
	}

	/**
	 * @param array $options The array of options.
	 *
	 * @return array
	 */
	private function sanitize_product_status_options( $options ) {
		if ( ! is_array( $options ) ) {
			return array();
		}

		$array    = array();
		$defaults = wp_list_pluck( WCAPF_API_Utils::product_status_options(), 'label', 'value' );

		foreach ( $options as $option ) {
			$option = WCAPF_API_Utils::sanitize_manual_option_data( $option );
			$value  = $option['value'];
			$label  = $option['label'];

			if ( ! strlen( $value ) ) {
				continue;
			}

			if ( ! strlen( $label ) ) {
				$label = isset( $defaults[ $value ] ) ? $defaults[ $value ] : $value;
			}

			$array[] = array_merge( $option, array( 'label' => $label ) );
		}

		return $array;
	}

}
