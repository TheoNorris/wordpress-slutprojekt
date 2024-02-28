/**
 * The admin js scripts file.
 *
 * @since      4.0.0
 * @package    wc-ajax-product-filter
 * @subpackage wc-ajax-product-filter/admin/js
 * @author     wptools.io
 */

/**
 * Show an alert when leaving the page.
 */
window.onbeforeunload = function() {
	if ( typeof wcapf_admin_params !== 'undefined' && wcapf_admin_params.dirty ) {
		return '';
	}
};
