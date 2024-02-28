<?php
/*
 * Plugin Name:       WCAPF - WooCommerce Ajax Product Filter
 * Plugin URI:        https://wptools.io/wc-ajax-product-filter/?utm_source=plugins+listing&utm_medium=wcapf+free&utm_campaign=WCAPF+Pro+Details
 * Description:       A plugin to filter WooCommerce products with AJAX request.
 * Version:           4.2.0
 * Requires at least: 6.0
 * Requires PHP:      7.2
 * Author:            wptools.io
 * Author URI:        https://wptools.io?utm_source=plugins+listing&utm_medium=wcapf+free&utm_campaign=Business+Website
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       wc-ajax-product-filter
 * Domain Path:       /languages
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * @since     3.0.0
 * @package   wc-ajax-product-filter
 * @copyright Copyright (c) 2018, wptools.io
 * @author    wptools.io
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Defines constant WCAPF_VERSION
if ( ! defined( 'WCAPF_VERSION' ) ) {
	define( 'WCAPF_VERSION', '4.2.0' );
}

class WCAPF_Plugin {

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'show_admin_notice' ) );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'woocommerce_loaded', array( $this, 'load_dependencies' ) );

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
	}

	public function load_plugin_textdomain() {
		if ( ! $this->should_we_proceed() ) {
			return;
		}

		// Load translated strings.
		load_plugin_textdomain(
			'wc-ajax-product-filter',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}

	/**
	 * Used to check if wcapf-pro-v2.1 or greater than is found, if found we don't load the free version.
	 *
	 * @return bool
	 */
	private function should_we_proceed() {
		if ( ! defined( 'WCAPF_PRO_VERSION' ) ) {
			return true;
		}

		if ( version_compare( WCAPF_PRO_VERSION, '2.1', '>=' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check requirements, if requirements fail show notice.
	 */
	public function show_admin_notice() {
		if ( ! $this->should_we_proceed() ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$notices = $this->check_requirements();

		if ( ! empty( $notices ) ) {
			// Get the first notice.
			$notice = array_shift( $notices );
			echo '<div class="notice notice-error"><p>' . wp_kses_post( $notice ) . '</p></div>';
		}
	}

	/**
	 * Check if the requirements are met.
	 *
	 * @param string $type Type of messages to return ('notices' or 'messages').
	 *
	 * @return array Array of admin notices or messages for failed requirements.
	 */
	private function check_requirements( $type = 'notices' ) {
		$notices       = array();
		$messages      = array();
		$wc_version    = defined( 'WC_VERSION' ) ? WC_VERSION : '';
		$wp_version    = get_bloginfo( 'version' );
		$php_version   = PHP_VERSION;
		$dashboard_url = get_dashboard_url( get_current_user_id(), 'plugins.php' );

		$required_wp_version  = '6.0';
		$required_wc_version  = '6.6';
		$required_php_version = '7.2';

		if ( ! class_exists( 'WooCommerce' ) ) {
			$notices[] = __( 'WCAPF - WooCommerce Ajax Product Filter requires WooCommerce. The plugin is currently NOT RUNNING.', 'wc-ajax-product-filter' );

			$messages[] = sprintf(
				__( '%sWCAPF - WooCommerce Ajax Product Filter can not be activated. %s It requires WooCommerce plugin to be activated. Please activate the WooCommerce plugin first ✌️ %s Back %s', 'wc-ajax-product-filter' ),
				'<strong>',
				'</strong><br><br>',
				'<br /><br /><a href="' . esc_url( $dashboard_url ) . '" class="button button-primary">',
				'</a>'
			);
		} elseif ( version_compare( $wc_version, $required_wc_version, '<' ) ) {
			$notices[] = sprintf(
				__( 'WCAPF - WooCommerce Ajax Product Filter requires WooCommerce version %s or higher, but WooCommerce version %s is used on the site. The plugin is currently NOT RUNNING.', 'wc-ajax-product-filter' ),
				$required_wc_version,
				$wc_version
			);

			$messages[] = sprintf(
				__( '%sWCAPF - WooCommerce Ajax Product Filter can not be activated. %s It requires WooCommerce version %s or higher, but WooCommerce version %s is used on the site. Please upgrade the WooCommerce version first ✌️ %s Back %s', 'wc-ajax-product-filter' ),
				'<strong>',
				'</strong><br><br>',
				$required_wc_version,
				$wc_version,
				'<br /><br /><a href="' . esc_url( $dashboard_url ) . '" class="button button-primary">',
				'</a>'
			);
		}

		if ( version_compare( $wp_version, $required_wp_version, '<' ) ) {
			$notices[] = sprintf(
				__( 'WCAPF - WooCommerce Ajax Product Filter requires WordPress version %s or higher, but WordPress version %s is used on the site. The plugin is currently NOT RUNNING.', 'wc-ajax-product-filter' ),
				$required_wp_version,
				$wp_version
			);

			$messages[] = sprintf(
				__( '%sWCAPF - WooCommerce Ajax Product Filter can not be activated. %s It requires WordPress version %s or higher, but WordPress version %s is used on the site. Please upgrade the WordPress version first ✌️ %s Back %s', 'wc-ajax-product-filter' ),
				'<strong>',
				'</strong><br><br>',
				$required_wp_version,
				$wp_version,
				'<br /><br /><a href="' . esc_url( $dashboard_url ) . '" class="button button-primary">',
				'</a>'
			);
		}

		if ( version_compare( $php_version, $required_php_version, '<' ) ) {
			$notices[] = sprintf(
				__( 'WCAPF - WooCommerce Ajax Product Filter requires PHP version %s or higher, but PHP version %s is used on the site. The plugin is currently NOT RUNNING.', 'wc-ajax-product-filter' ),
				$required_php_version,
				$php_version
			);

			$messages[] = sprintf(
				__( '%sWCAPF - WooCommerce Ajax Product Filter can not be activated. %s It requires PHP version %s or higher, but PHP version %s is used on the site. Please upgrade the PHP version first ✌️ %s Back %s', 'wc-ajax-product-filter' ),
				'<strong>',
				'</strong><br><br>',
				$required_php_version,
				$php_version,
				'<br /><br /><a href="' . esc_url( $dashboard_url ) . '" class="button button-primary">',
				'</a>'
			);
		}

		if ( 'notices' === $type ) {
			return $notices;
		}

		return $messages;
	}

	public function activate() {
		$failed_requirements = $this->check_requirements( 'messages' );

		if ( $failed_requirements ) {
			// Get the first requirement.
			$requirement = array_shift( $failed_requirements );

			deactivate_plugins( basename( __FILE__ ) );

			wp_die( $requirement );
		}

		$db_version_option_key  = 'wcapf_db_version';
		$existing_wcapf_version = get_option( $db_version_option_key );

		// If any records for v3 exist, perform migration to v4.
		if ( ! $existing_wcapf_version ) {
			$filters = get_posts( array( 'post_type' => 'wcapf-filter', 'post_status' => 'any', 'fields' => 'ids' ) );

			// Migrate from v3 to v4.
			if ( $filters ) {
				update_option( 'wcapf_run_migrate', '1' );

				$this->save_activation_time_in_db();

				return;
			}
		}

		if ( ! $existing_wcapf_version ) {
			// Set default settings if no existing version found.
			update_option( 'wcapf_set_default_settings', '1' );
		} elseif ( version_compare( WCAPF_VERSION, $existing_wcapf_version, '>' ) ) {
			// Update default settings if a newer version is detected.
			update_option( 'wcapf_update_default_settings', '1' );
		}

		// Update the db version.
		update_option( $db_version_option_key, WCAPF_VERSION );

		// Clear the forms with locations transients.
		delete_transient( 'wcapf_forms_with_locations' );

		$this->save_activation_time_in_db();
	}

	private function save_activation_time_in_db() {
		$activation_time = get_option( 'wcapf_activation_time' );

		if ( ! $activation_time ) {
			update_option( 'wcapf_activation_time', time() );
		}
	}

	public function load_dependencies() {
		if ( ! $this->should_we_proceed() ) {
			return;
		}

		$met_requirements = empty( $this->check_requirements() );

		if ( ! $met_requirements ) {
			return;
		}

		$this->define_constants();

		require_once WCAPF_PLUGIN_DIR . '/includes/wcapf-dependencies.php';

		/**
		 * Register a hook to load any other dependencies after the plugin files are loaded.
		 */
		do_action( 'wcapf_loaded' );
	}

	private function define_constants() {
		// Defines constant WCAPF_PLUGIN_FILE
		if ( ! defined( 'WCAPF_PLUGIN_FILE' ) ) {
			define( 'WCAPF_PLUGIN_FILE', __FILE__ );
		}

		// Defines constant WCAPF_PLUGIN_DIR
		if ( ! defined( 'WCAPF_PLUGIN_DIR' ) ) {
			define( 'WCAPF_PLUGIN_DIR', __DIR__ );
		}

		// Defines constant WCAPF_PLUGIN_URL
		if ( ! defined( 'WCAPF_PLUGIN_URL' ) ) {
			define( 'WCAPF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Defines constant WCAPF_CACHE_TIME
		if ( ! defined( 'WCAPF_CACHE_TIME' ) ) {
			define( 'WCAPF_CACHE_TIME', 60 * 60 * 12 );
		}
	}

}

new WCAPF_Plugin();

/**
 * We are not loading the wcapf-pro-v1 as it is not compatible with the new wcapf versions anymore.
 *
 * @return void
 */
function wcapf_unload_pro_v1() {
	remove_action( 'woocommerce_loaded', 'wcapf_pro_loads_dependencies', 99 );
}

add_action( 'woocommerce_loaded', 'wcapf_unload_pro_v1' );

/**
 * Uninstalling WCAPF - WooCommerce Ajax Product Filter deletes the plugin settings, forms and filters.
 *
 * @since        4.1.0
 * @package      wc-ajax-product-filter-pro
 * @author       wptools.io
 *
 * @noinspection SqlNoDataSourceInspection
 * @noinspection SqlDialectInspection
 */
function wcapf_uninstall_cleanup() {
	// Don't proceed if pro version activated.
	if ( function_exists( 'wcapf_fs_uninstall_cleanup' ) ) {
		return;
	}

	$option_key = 'wcapf_settings';
	$settings   = get_option( $option_key );

	if ( ! $settings['remove_data'] ) {
		return;
	}

	global $wpdb;

	// Delete records from the options table.
	delete_option( $option_key );
	delete_option( 'wcapf_run_migrate' );
	delete_option( 'wcapf_set_default_settings' );
	delete_option( 'wcapf_update_default_settings' );
	delete_option( 'wcapf_filter_keys_order' );
	delete_option( 'wcapf_db_version' );
	delete_option( 'wcapf_activation_time' );

	// Delete v4 migration related records from the options table.
	delete_option( 'wcapf_v4_migration_notice_status' );
	delete_option( 'wcapf_migrated_filters_form_id' );

	// Delete transients.
	delete_transient( 'wcapf_v4_migration_status' );

	// Clear the forms with locations transients.
	delete_transient( 'wcapf_forms_with_locations' );

	// Delete posts + data.
	$wpdb->query( "DELETE FROM $wpdb->posts WHERE post_type IN ('wcapf-form', 'wcapf-filter');" );
	$wpdb->query( "DELETE meta FROM $wpdb->postmeta meta LEFT JOIN $wpdb->posts posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );

	// Delete review notice related records from the user meta table.
	$meta_keys = array(
		'wcapf_form_updates_count',
		'wcapf_review_notice_for_milestone_achieved_dismissed',
		'wcapf_review_notice_for_milestone_achieved_dismissed_at',
		'wcapf_review_notice_time_since_hide_permanently',
		'wcapf_review_notice_time_since_dismissed_at',
	);

	$meta_keys_string = '(' . implode( ', ', array_map( function ( $key ) {
			return "'$key'";
		}, $meta_keys ) ) . ')';

	$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key IN $meta_keys_string;" );
}

register_uninstall_hook( __FILE__, 'wcapf_uninstall_cleanup' );
