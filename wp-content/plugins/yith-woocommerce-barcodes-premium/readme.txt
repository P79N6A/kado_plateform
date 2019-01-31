=== YITH WooCommerce Barcodes and QR code Premium ===

Contributors: yithemes
Tags: barcode, bar code, qr code, product bar code, order bar code, product barcode, order barcode
Requires at least: 4.0.0
Tested up to: 5.0.x
Stable tag: 1.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Documentation: https://docs.yithemes.com/yith-woocommerce-barcodes/

Add barcode an QR code features to your products and orders and let you execute automatic action with the shortcodes

== Changelog ==

= Version 1.2.4 - Released: Dec 11, 2018 =

* New: support to WordPress 5.0
* New: plugin shortcodes are editable using Gutenberg editor
* Update: plugin core to version 3.1.10
* Update: Dutch language file


= Version 1.2.3 - Released: Oct 23, 2018 =

* Update: plugin framework
* Update: plugin description
* Update: plugin links

= Version 1.2.2 - Released: Oct 17, 2018 =

* New: Support to WooCommerce 3.5.0

* Tweak: read a barcode with a scanner
* Tweak: display the variation name in the barcode search
* Tweak: Improving the search products by barcode row
* Tweak: Include class emogrifier
* Tweak: new action links and plugin row meta in admin manage plugins page
* Update: Spanish language
* Update: updated the official documentation url of the plugin
* Update: updated plugin -fw
* Fix: issue in the code generation behaviour
* Fix: Show barcode for variation products in the email
* Fix: change the status when you add check-in-ticket button on shortcode
* Dev: added filter yith_ywbc_barcode_action_search
* Dev: deleting console logs
* Dev: added a new filter to allow to generate barcodes by role
* Dev: Added a new filter in the query post type
* Dev: added filter 'yith_ywbc_ean13_formatted_value'
* Dev: added filter 'yith_ywcb_execute_default_qrcode_generation_process'
* Dev: added filter 'yith_ywbc_barcode_src'
* Dev: added filter 'yith_ywbc_qrcode_generation'

= Version 1.2.1 - Released: Feb 23, 2018 =

* New: support to WordPress 4.9.4
* Update: plugin framework 3.0.12
* Dev: new filter 'yith_barcode_display_value'
* Dev: new filter 'yith_ywbc_formatted_value'
* Dev: new filter 'yith_ywbc_image_filename'
* Dev: new filter 'yith_ywbc_image_size'
* Dev: new filter 'yith_wcbc_image_margin'


= Version 1.2.0 - Released: Jan 30, 2018 =

* New: support to WordPress 4.9.2
* New: support to WooCommerce 3.3.0-RC2
* Update: plugin framework 3.0.11
* Tweak: barcode image showed in png format
* New: check-in for multiple tickets contained in the same order (in combination with YITH Event Tickets plugin)
* Fix: fatal error searching products by shortcode (using WooCommerce 2.6.14)
* Fix: compatibility with deposit and down payments, checking when show_on_emails


= Version 1.1.3 - Released: Nov 27, 2017 =
* New: support to WooCommerce 3.2.5
* New: support to WordPress 4.9


= Version 1.1.2 - Released: Nov 08, 2017 =

* New: Support to WooCommerce 3.2.3
* Fix: search form not works for unlogged users

= Version 1.1.1 - Released: Jul 05, 2017 =

* New: Support to WooCommerce 3.1
* Update: language files

= Version 1.1.0 - Released: Jun 12, 2017 =

* New: filter products by their barcode and manage the stock dynamically.
* New: filter orders by their barcode and manage the order status dynamically.
* New: template ywbc-search-products-row.php shows details about a product matching with search criteria.
* New: template ywbc-search-orders-row.php shows details about an order matching with search criteria.
* Update: template ywbc-search-products.php was split and now uses template ywbc-search-products-row.php.
* Update: template ywbc-search-orders.php was split and now uses template ywbc-search-orders-row.php.

= Version 1.0.14 - Released: May 04, 2017 =

* Fix: variation barcode image not shown on product page if there isn't a default barcode image for the variable product.

= Version 1.0.13 - Released: Apr 30, 2017 =

* Update: YITH Plugin-FW.
* Fix: missing barcode on customer email.

= Version 1.0.12 - Released: Apr 05, 2017 =

* New: show barcode value on variation selection at front end product page(for variable products).
* Fix: some barcode values not saved correctly.
* Dev: filter 'yith_ywbc_render_barcode_html' lets third party plugins to edit the Barcode HTML elements rendered by the plugin.

= Version 1.0.11 - Released: Mar 28, 2017 =

* New:  Support to WordPress 4.7.3.
* Fix: barcode not generated automatically on new order.
* Fix: barcode path not working on IIS server.
* Fix: caching issue while saving barcode values with WC 3.0 RC2.
* Fix: not existing save_cpt_objects() call in place of save_cpt_object() in YITH_Barcode class.

= Version 1.0.10 - Released: Mar 23, 2017 =

* New:  Support to WooCommerce 2.7.0-RC1
* New: create manual or automatic barcode for variable products
* Update: YITH Plugin Framework
* Fix: product's barcode image not shown on emails.
* Fix: manual barcode value not saved correctly with EAN13 protocol.

= Version 1.0.9 - Released: Jan 12, 2017 =

* New: store the user that completed the order with a barcode scan
* New: generate barcode for variable products
* Fix: wrong results filtering product per product type
* Fix: embedded images in email are not visible with some email client

= Version 1.0.8 - Released: Jan 04, 2017 =

* Add: choose if the product's barcode should be shown on emails

= Version 1.0.7 - Released: Dec 23, 2016 =

* New: searching by barcode value on orders list
* Fix: the search for the value of the product bar code returns duplicate results

= Version 1.0.6 - Released: Dec 07, 2016 =

* New: ready for WordPress 4.7

= Version 1.0.5 - Released: Sep 16, 2016 =

* Fix: QR code not rendered through shortcode if the QR code protocol was not used on products or orders too

= Version 1.0.4 - Released: Sep 10, 2016 =

* Fix: wrong filename used in the rendering method

= Version 1.0.3 - Released: Jun 21, 2016 =

* New: information about the current progression of the background generation process
* Tweak: generate barcode in background only for products without a barcode

= Version 1.0.2 - Released: Jun 17, 2016 =

* New: generate barcode for all products in background

= Version 1.0.1 - Released: Jun 13, 2016 =

* Tweak: WooCommerce 2.6 100% compatible

= Version 1.0.0 - Released: May 20, 2016 =

* First release

== Suggestions ==

If you have suggestions about how to improve YITH WooCommerce Barcodes and QR code, you can [write us](mailto:plugins@yithemes.com "Your Inspiration Themes") so we can bundle them into YITH WooCommerce Barcodes and QR code.

== Translators ==

If you have created your own language pack, or have an update for an existing one, you can send [gettext PO and MO file](http://codex.wordpress.org/Translating_WordPress "Translating WordPress")
[use](http://yithemes.com/contact/ "Your Inspiration Themes") so we can bundle it into YITH WooCommerce Barcodes and QR code languages.

 = Available Languages =
 * English