<?php
/**
 * The file responsible for loading the dependencies for the WCAPF plugin.
 *
 * @since 4.1.0
 */

require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-admin.php';
require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-api-utils.php';
require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-default-data.php';
require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-field-instance.php';
require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-form.php';
require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-form-filters-utils.php';
require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-frontend-scripts.php';
require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-helper.php';
require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-post-type.php';
require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-product-filter.php';
require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-product-filter-utils.php';
require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-template-loader.php';
require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-url-builder.php';
require_once WCAPF_PLUGIN_DIR . '/includes/class-wcapf-walker.php';

// Loads the filter types.
require_once WCAPF_PLUGIN_DIR . '/includes/filter-types/class-wcapf-filter-type.php';
require_once WCAPF_PLUGIN_DIR . '/includes/filter-types/class-wcapf-filter-type-post-author.php';
require_once WCAPF_PLUGIN_DIR . '/includes/filter-types/class-wcapf-filter-type-post-meta.php';
require_once WCAPF_PLUGIN_DIR . '/includes/filter-types/class-wcapf-filter-type-price.php';
require_once WCAPF_PLUGIN_DIR . '/includes/filter-types/class-wcapf-filter-type-product-status.php';
require_once WCAPF_PLUGIN_DIR . '/includes/filter-types/class-wcapf-filter-type-taxonomy.php';

// Loads the hooks.
require_once WCAPF_PLUGIN_DIR . '/includes/hooks/class-wcapf-api.php';
require_once WCAPF_PLUGIN_DIR . '/includes/hooks/class-wcapf-hooks.php';
require_once WCAPF_PLUGIN_DIR . '/includes/hooks/class-wcapf-post-author-filter.php';
require_once WCAPF_PLUGIN_DIR . '/includes/hooks/class-wcapf-rating-filter.php';
require_once WCAPF_PLUGIN_DIR . '/includes/hooks/class-wcapf-taxonomy-filter.php';

// Migration dependencies.
require_once WCAPF_PLUGIN_DIR . '/includes/migration/class-wcapf-v4-migration.php';
require_once WCAPF_PLUGIN_DIR . '/includes/migration/class-wcapf-v4-migration-hooks.php';

// Loads the shortcodes.
require_once WCAPF_PLUGIN_DIR . '/includes/shortcodes/class-wcapf-active-filters-shortcode.php';
require_once WCAPF_PLUGIN_DIR . '/includes/shortcodes/class-wcapf-filter-shortcode.php';
require_once WCAPF_PLUGIN_DIR . '/includes/shortcodes/class-wcapf-filter-form-shortcode.php';
require_once WCAPF_PLUGIN_DIR . '/includes/shortcodes/class-wcapf-reset-button-shortcode.php';

// Loads the widgets.
require_once WCAPF_PLUGIN_DIR . '/includes/widgets/class-wcapf-filter-widget.php';

// Loads the compatibility fixes.
require_once WCAPF_PLUGIN_DIR . '/includes/wcapf-compatibility-fixes.php';
