=== WCAPF - WooCommerce Ajax Product Filter ===
Contributors: shamimmoeen
Tags: product filter, ajax product filter, ajax filter, woocommerce ajax product filter, woocommerce product filter, woocommerce ajax filter, woocommerce filter, products filter, ajax filter, advanced products filter, woocommerce layered nav, woocommerce layered navigation, ajax filtered nav, ajax filtered navigation, price filter, ajax price filter, ajax product search, woocommerce product sorting, woocommerce, wc ajax product filter, wc product filter, wc ajax filter, wc filter
Requires at least: 6.0
Tested up to: 6.4.1
Stable tag: 4.2.0
Requires PHP: 7.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

WCAPF - WooCommerce Ajax Product Filter is a powerful plugin that enhances the filtering functionality of your WooCommerce store.

== Description ==

**WCAPF - WooCommerce Ajax Product Filter** is a powerful plugin that enhances the filtering functionality of your WooCommerce store. It allows your customers to easily find and narrow down their product search using a dynamic and interactive filter system. With Ajax-based filtering, the plugin provides instant results without page reloading, ensuring a smooth and seamless user experience.

[**View Demo**](https://demos.wptools.io/wc-ajax-product-filter/?utm_source=wordpress.org&utm_medium=free+plugin+listing&utm_campaign=WCAPF+Demo) | [**Documentation**](https://wptools.io/docs/wc-ajax-product-filter/?utm_source=wordpress.org&utm_medium=free+plugin+listing&utm_campaign=WCAPF+Documentation) | [**💎 Upgrade to Pro**](https://wptools.io/wc-ajax-product-filter/?utm_source=wordpress.org&utm_medium=free+plugin+listing&utm_campaign=WCAPF+Pro+Upgrade)

== Filtering Options ==

- **Filter by Taxonomy:** Enable customers to filter products based on different taxonomies, such as categories, tags, and custom taxonomies.

- **Filter by Attribute:** Allow customers to refine their product search by attribute values like size, color, material, and more.

- **Filter by Price:** Provide a price range filter to help customers find products within their desired price range.

- **Filter by Rating:** Enable customers to filter products based on user ratings to find highly rated items.

- **Filter by Product Status:** Allow customers to filter products based on their status, such as featured or in-stock items.

- **Filter by Post-Author:** Provide the ability to filter products based on the author of the associated posts.

- **Filter by Post-Meta:** Offer customers the ability to filter products based on custom post metadata, with support for different value types like text, number, or date.

- **Filter by Keyword:** Enable customers to search for products using keywords to find specific items quickly.

- **Sort by Filter:** Offer customers the option to sort products based on different criteria, enhancing their browsing experience.

- **Per Page Filter:** Allow customers to select the number of products displayed per page.

== Flexible Filter Display Options ==

- **Multiple Display Options:** Present filter options using checkboxes, radio buttons, dropdowns, multiselect, or labels.

- **Styling and Layout:** Choose from list, inline, grid, or hierarchical display for taxonomies, with accordion support for hierarchies.

- **Price Filter Options:** Provide a slider with min and max input fields for the price filter, or define custom price ranges displayed as checkboxes, radio buttons, or other options.

- **Product Count Display:** Show the number of products available for each filter option, helping customers gauge the product availability within their selected criteria.

- **Tooltip Information:** Display informative tooltips when customers hover over filter options, including product count information within the tooltip.

- **Automatic or Manual Filter Options:** Set filter options to be generated automatically or manually enter custom options, allowing for precise customization of labels and tooltips.

- **Options Ordering:** Define the order of filter options, including taxonomy, post-author, and post-meta filters. For taxonomy filters, order by ID, Name, Slug, Count, or Include Order. For post-author filters, order by ID, Name, Count, or Include Order. For post-meta filters, order by Value, Label, or Count.

- **Include/Exclude Options:** For taxonomy filters, include or exclude specific terms. If the taxonomy is hierarchical, set the parent term to limit the child terms displayed. Choose to include only immediate child terms or all child terms. For post-author filters, include or exclude specific users and specify user roles. For post-meta filters, include or exclude by values.

- **Accordion and Tooltip:** Implement accordion-style display for filters, allowing users to expand or collapse the filter options. When collapsed, the filter displays the filter title only, and when expanded, it shows the filter options. Additionally, use tooltip icons beside the filter titles to provide additional information about each filter option when users hover over them.

- **Search Field:** Include a search field for quick navigation through the filter options. Visitors can enter keywords in the search field to narrow down the available filter options, making it easier to find specific choices.

- **Show More/Show Less Toggle:** Provide a toggle option to show or hide additional filter options for better user experience. The toggle allows users to expand or collapse the filter options, reducing clutter and improving usability.

- **Max Height of Filter Block:** Set the maximum height of a filter block to limit the number of visible filter options. This feature helps users show more filters within a specific height, enhancing the browsing experience and avoiding overwhelming displays.

- **Color/Image Swatches:** Enhance the visual appeal of your filter options by [displaying them using color or image swatches](https://wptools.io/docs/wc-ajax-product-filter/common/color-image-swatches/?utm_source=wordpress.org&utm_medium=free+plugin+listing&utm_campaign=WCAPF+Documentation). For color swatches, the plugin looks for the color first and, if not found, displays the image. The color swatch data includes the primary color and secondary color, when both exist in the swatch data it enables the color gradient which is a more comprehensive visual representation. For image swatches, the data includes the image ID. For the **popular variation swatches plugins** the swatch data can be obtained from term meta automatically. Otherwise, you'll need to manually input the filter options and configure the swatch data.

== Advanced Filtering Functionality ==

- **Variable Product Filtering Support:** Ensure seamless filtering for variable products, including support for attribute lookup tables.

- **Ajax Pagination:** Enable seamless browsing with Ajax-based pagination, allowing customers to navigate through filtered results without page reloads.

- **Active Filters Display:** Showcase the active filters prominently above the product loop, making it easy for customers to see and modify their selections.

- **Active Sorting Option:** Enable AJAX for the default product sorting dropdown and display the active sorting option in the active filters section.

== Easy Integration ==
Effortlessly integrate the filters into your store using a single widget or shortcode. The plugin allows you to create filters by creating a form and adding filters inside it. The flexibility of the plugin allows you to create unlimited forms and filters according to your specific needs. By using the "**Available on**" setting, you can precisely determine where the filters should be displayed. Simply add the form widget (named '**WCAPF - Product Filter Form**') to the sidebar, and the filters will automatically appear based on the current template being used.

== Different Filters for Different Archive Pages ==
Customize the filter options based on specific product categories by [displaying unique filters on various archive pages](https://wptools.io/docs/wc-ajax-product-filter/common/different-filters-for-different-archive-pages/?utm_source=wordpress.org&utm_medium=free+plugin+listing&utm_campaign=WCAPF+Documentation). This feature allows you to create specialized filter sets that address the specific needs of each product category.

For example, clothing filters may include options for *size* and *color*, while laptop filters may include options for *processor*, *motherboard*, *RAM*, and *hard disk*. By using different filters for different archive pages, you can enhance the filtering experience for your customers.

== Integration on Singular Pages ==
[Integrate filters on singular pages](https://wptools.io/docs/wc-ajax-product-filter/common/filters-on-custom-pages/?utm_source=wordpress.org&utm_medium=free+plugin+listing&utm_campaign=WCAPF+Documentation) by utilizing the `[wcapf_products]` shortcode in conjunction with the form shortcode `[wcapf_form]`. This integration allows you to display products similarly to the shop page and effectively combines them with the filters.

The `[wcapf_products]` shortcode leverages the popular **WooCommerce products shortcode**, providing extensive customization options. With the product loop, you can:

- Specify the layout (such as standard shop loop, products with pagination, or only products)
- Select the type of products to display (on sale, best selling, top rated)
- Define the visibility of products (visible, catalog, search, hidden, featured)
- Configure the product ordering
- Determine the number of columns for the grid layout
- Set the total number of displayed products
- Apply taxonomy and meta queries
- Exclude specific products
- Define a custom message when no products match the applied filters

== Drag and Drop Filter Creation ==
The plugin allows users to add filters in just a few clicks. By clicking the “Add Filter” button, a new filter is added with automatically configured settings. Users can easily customize the filter settings, reorder filters, and delete them as needed.

== Customizable Filter Keys in URL ==
Set the filter key used in the URL to identify the type of filter applied.

For example, in the URL **../?category=sneakers&material=mesh**, **category**, and **material** are the filer keys.

== Dynamic Product Count ==
Update the product count dynamically based on the applied filters, providing accurate and real-time information to customers, allowing customers to filter products with confidence and avoiding "no results found" messages.

== Hide/Disable Irrelevant Options ==
Streamline the filtering experience by hiding or disabling options that return zero products. The plugin offers the flexibility to either hide or disable irrelevant options based on the applied filters. By removing these options, users can focus on relevant choices, enhancing their browsing experience.

== Hide Empty Filters ==
Exclude empty filters from display to declutter the user interface. Empty filters that do not have any available options are automatically hidden, reducing visual noise and improving the usability of the filter system.

== Option to Disable Ajax Filtering ==
Provide flexibility to disable AJAX filtering if needed. While AJAX filtering is generally recommended for seamless browsing, users can choose to disable it to troubleshoot any potential JavaScript conflicts that may arise after updating the product loop.

== Multi-Vendor Plugin Support ==
When enabled, the filter plugin supports [multi-vendor setups](https://wptools.io/docs/wc-ajax-product-filter/common/filter-by-vendor/?utm_source=wordpress.org&utm_medium=free+plugin+listing&utm_campaign=WCAPF+Documentation) by showing the store name as the filter option label. This allows customers to easily filter products from specific vendors or stores.

== Customization ==
Easily customize the appearance of the filters to match the design and style of your theme. The plugin provides a variety of settings on the plugin settings page, **including CSS variables**, which allow developers to quickly transform the global style of a filter to a unique style.

== Theme Compatibility ==
The plugin is designed to be highly compatible with a wide range of themes that follow the standard guidelines set by WooCommerce. This means it is likely to work effectively with almost any theme you choose.

== Developer Friendly ==
It includes essential hooks, filters and supports template overriding, allowing developers to alter the filter data, change the appearance of filters, and easily modify the plugin's behavior.

== Frequently Asked Questions ==

= How can I get support? =

If you have any questions or need support, we're here to help. Free users can use the [WordPress.org support forum](https://wordpress.org/support/plugin/wc-ajax-product-filter/), while premium users can contact us through our [website's support page](https://wptools.io/support/?utm_source=wordpress.org&utm_medium=free+plugin+listing&utm_campaign=WCAPF+Pro+Support). You can also check our [documentation](https://wptools.io/docs/wc-ajax-product-filter/?utm_source=wordpress.org&utm_medium=free+plugin+listing&utm_campaign=WCAPF+Documentation) for guides and information.

= How can I see the plugin in action? =

You can see the plugin in action by visiting our [demo site](https://demos.wptools.io/wc-ajax-product-filter/?utm_source=wordpress.org&utm_medium=free+plugin+listing&utm_campaign=WCAPF+Demo). It provides a live demonstration of the plugin's features and functionality.

= Is the plugin compatible with all WordPress themes? =

The plugin is compatible with most WordPress themes that follow the WooCommerce theme guidelines. However, some themes may require additional customization.

= Can users select multiple options from a filter? =

Yes, if enabled in the filter settings, users can select multiple options from a filter. This provides flexibility and convenience in refining their product search.

= Are the filters responsive and user-friendly? =

Yes, the filters are responsive and designed with user-friendliness in mind. The plugin offers features like accordion and tooltip display, a search field, show more/show less toggle, and customizable filter block height for an enhanced user experience.

= Why is the filter not working? =

If you are experiencing issues with the plugin's functionality, please follow these steps:

* Ensure that your WordPress and WooCommerce versions meet the minimum requirements specified by the plugin.
* Test the plugin with a default WordPress theme. If it works with the default themes, the issue may be related to the theme you are using.
* Deactivate all your plugins and then re-activate them one by one, checking your site for the problem after each reactivation.

If the issue persists, please open a support ticket, and our dedicated support team will assist you further.

== Screenshots ==

1. Filters Demo 1
2. Filters Demo 2
3. Filters Demo 3
4. List of Forms
5. Form Filters
6. Plugin Settings

== Changelog ==

= 4.2.0 (27 August 2023) =

* Fix: Resolved issue with unwanted filter options on attribute filters using lookup tables
* Security: Addressed vulnerabilities for enhanced plugin security

= 4.1.0 (20 July 2023) =

* New Feature – Added keyword filter to enable product filtering using keywords
* Compatibility – Ensured plugin compatibility with WordPress 6.0
* UI Enhancement – Improved the admin user interface

= 4.0.0 (15 June 2023) =

* **Redesigned Admin UI:** We have revamped the plugin's admin interface to provide a more intuitive and user-friendly experience. You'll find it easier than ever to configure and manage your product filters.

* **Codebase Refactoring:** We have refactored the codebase to optimize performance and lay a solid foundation for future enhancements.

* **Filter-Form Association:** Filters are now linked to a form. This means that filters are contained within a specific form, simplifying management and organization. The form shortcode is now used to display all filters linked to that form on the frontend.

* **Additional Settings:** We have introduced additional settings that offer you more control over your filters.

* **Bug Fixes:** We have diligently addressed and fixed several bugs reported by our valued users. These fixes contribute to a more stable and reliable plugin performance.

= 3.3.2 (04 January 2023) =

* Prevent redirect to product page while filtering on the search page and getting a single result.
* Fix SQL error caused by joining alias

= 3.3.1 (27 September 2022) =

* Make it compatible with woo-variation-swatches

= 3.3.0 (13 September 2022) =

* Feature - Option to specify user roles for post author filter

= 3.2.0 (01 August 2022) =

* Fix - custom loading image issue

= 3.1.0 (29 July 2022) =

* Fix - loading image upload button js issue
* Fix - product sorting via ajax issue
* Fix - products count result not updating issue

= 3.0.0 (17 July 2022) =

* Release pro version
* Refactor the plugin

= 2.0.3 =

* Fixed bug - 'wcapf_get_term_objects' function was working incorrectly

= 2.0.2 =

* Added option to disable transients
* Added option to clear transients

= 2.0.1 =

* Fixed filtering bugs on product taxonomy pages
* Added more functionality to price filter display type list widget
* Added option to enable/disable font awesome
* Updated localization

= 2.0 =

* Fixed PHP version related issue
* Fixed translation and localization issue
* Fixed compatibility issue with WooCommerce Average Rating Filter
* Added functionality to filter products by price list
* Added functionality to clear all active filters with one click

= 1.0 =

* Initial release

== Upgrade Notice ==

= 4.0.0 =

We are excited to announce the release of a major upgrade for our plugin, now known as **WCAPF - WooCommerce Ajax Product Filter**! Version 4.0.0 introduces exciting new features, improved performance, and enhanced customization options to empower you with greater control over your filters.

**What's New:**

1. **Redesigned Admin UI:** We have revamped the plugin's admin interface to provide a more intuitive and user-friendly experience. You'll find it easier than ever to configure and manage your product filters.

2. **Codebase Refactoring:** We have refactored the codebase to optimize performance and lay a solid foundation for future enhancements.

3. **Filter-Form Association:** Filters are now linked to a form. This means that filters are contained within a specific form, simplifying management and organization. The form shortcode is now used to display all filters linked to that form on the frontend.

4. **Additional Settings:** We have introduced additional settings that offer you more control over your filters.

**Bug Fixes:**

We have diligently addressed and fixed several bugs reported by our valued users. These fixes contribute to a more stable and reliable plugin performance.

**Migration Notice:**

To ensure a seamless transition to the new version, we have automatically migrated your existing filters to a form during the upgrade process. However, please note that the order of the filters within the form may require adjustment. To ensure the correct order, kindly visit the edit form page in the admin area, as indicated in the migration notice displayed upon upgrade. By manually adjusting the filter order within the form, you can restore the desired sequence.

**Upgrade Instructions for Existing Users:**

If you are an existing user upgrading from version 3 to version 4, we recommend taking a backup of your WordPress site and database for added safety. You can then proceed with the upgrade using either the FTP method or the automatic update method.

**Installation for New Users:**

For new installations, there are no additional steps required. The plugin can be installed like any other WordPress plugin.

**Support and Feedback:**

We appreciate your continued support and patience during the upgrade process. Should you encounter any issues or have any questions, please don't hesitate to reach out to our dedicated support team. We are committed to providing you with prompt assistance and ensuring a seamless experience with our plugin.

**Enjoy the New Version:**

Thank you for choosing WCAPF - WooCommerce Ajax Product Filter. We hope you enjoy the new features and improvements in version 4.0.0!
