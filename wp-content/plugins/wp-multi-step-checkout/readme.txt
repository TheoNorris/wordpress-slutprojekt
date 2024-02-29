=== Multi-Step Checkout for WooCommerce ===
Created: 30/10/2017
Contributors: diana_burduja
Email: diana@burduja.eu
Tags: multistep checkout, multi-step-checkout, woocommerce, checkout, shop checkout, checkout steps, checkout wizard, checkout style, checkout page
Requires at least: 3.0.1
Tested up to: 6.5
Stable tag: 2.27
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP: 5.2.4

Change your WooCommerce checkout page with a multi-step checkout page. This will let your customers have a faster and easier checkout process, therefore a better conversion rate for you.


== Description ==

Create a better user experience by splitting the checkout process in several steps. This will also improve your conversion rate.

The plugin was made with the use of the WooCommerce standard templates. This ensure that it should work with most the themes out there. Nevertheless, if you find that something isn't properly working, let us know in the Support forum.

= Features =

* Sleak design
* Mobile friendly
* Responsive layout
* Adjust the main color to your theme
* Inherit the form and buttons design from your theme
* Keyboard navigation

= Available translations = 

* German
* French

Tags: multistep checkout, multi-step-checkout, woocommerce, checkout, shop checkout, checkout steps, checkout wizard, checkout style, checkout page

== Installation ==

* From the WP admin panel, click "Plugins" -> "Add new".
* In the browser input box, type "Multi-Step Checkout for WooCommerce".
* Select the "Multi-Step Checkout for WooCommerce" plugin and click "Install".
* Activate the plugin.

OR...

* Download the plugin from this page.
* Save the .zip file to a location on your computer.
* Open the WP admin panel, and click "Plugins" -> "Add new".
* Click "upload".. then browse to the .zip file downloaded from this page.
* Click "Install".. and then "Activate plugin".

OR...

* Download the plugin from this page.
* Extract the .zip file to a location on your computer.
* Use either FTP or your hosts cPanel to gain access to your website file directories.
* Browse to the `wp-content/plugins` directory.
* Upload the extracted `wp-multi-step-checkout` folder to this directory location.
* Open the WP admin panel.. click the "Plugins" page.. and click "Activate" under the newly added "Multi-Step Checkout for WooCommerce" plugin.

== Frequently Asked Questions ==

= Why is the login form missing on the checkout page? =
Make sure to enable the `Display returning customer login reminder on the "Checkout" page` option on the `WP Admin -> WooCommerce -> Settings -> Accounts` page

= Is the plugin GDPR compatible? =
The plugin doesn't add any cookies and it doesn't modify/add/delete any of the form fields. It simply reorganizes the checkout form into steps.

= My checkout page still isn't multi-step, though the plugin is activated =
Make sure to purge the cache from any of the caching plugins, or of reverse proxy services (for example CloudFlare) you're using.

Another possible cause could be that the checkout page isn't using the default [woocommerce_checkout] shortcode. For example, the Elementor Pro checkout element replaces the default [woocommerce_checkout] shortcode with its HTML counterpart. Go to the "WP Admin -> Pages" page, open the checkout page for editing and make sure the [woocommerce_checkout] is present there.

== Screenshots ==

1. Login form
2. Billing
3. Review Order
4. Choose Payment
5. Settings page
6. On mobile devices

== Changelog ==

= 2.27 2024-02-11 =
* Compatibility with the Huntor theme
* Fix: the steps don't scroll up to the top on the Flatsome theme

= 2.26 2023-10-16 =
* Fix: open the Login step when clicking the "Please log in" link in the "Account already registered" message
* Feature: option to place the input fields from the Delivery & Pickup Time Date plugin in a separate step

= 2.25 2023-06-24 =
* Compatibility with the Mollie Payments plugin

= 2.24 2023-05-19 =
* Fix: missing "next/previous" button with the Elementor Pro checkout widget
* Fix: the Payment section was missing on the Blocksy theme, when the payment is in a separate step
* Fix: use the "default" checkout layout type from the Astra Addon Pro plugin 
* Compatibility with the WooCommerce "Custom Order Tables" feature

= 2.23 2023-02-03 =
* Fix: add the "woocommerce_checkout_before_order_review" action hook
* Compatibility with the Fastland theme.

[See changelog for all versions](https://plugins.svn.wordpress.org/wp-multi-step-checkout/trunk/changelog.txt).
