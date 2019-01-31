=== YITH WooCommerce PDF Invoice and Shipping List ===

Contributors: yithemes
Tags: woocommerce, invoice, packing slip, billing, pdf invoice
Requires at least: 4.0
Tested up to: 5.0
Stable tag: 1.2.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Generate and send PDF invoices and shipping list documents for WooCommerce orders via email.

== Description ==

This WooCommerce plugin allows creating PDF invoices and shipping list documents for WooCommerce orders quickly and easily with customizable templates.
Choose if generating invoices manually or automatically using custom number format and send it as email attachment to your customers.
   
= Main features =

* Generate PDF invoices and shipping list documents.
* Customizable invoice number format.
* Autoincrement invoice number.
* Automatically or manually generated invoice documents.
* Generate invoices automatically depending on order status.
* Add PDF invoice as attachment of the email sent to customer.
* Customizable invoice template.
* Customizable shipping list template.
* Download invoices from customers' order page.

For a more detailed list of options and features of the plugin, please look at the [official documentation](http://yithemes.com/docs-plugins/yith-woocommerce-pdf-invoice/ "Yith WooCommerce PDF Invoice official documentation").

Discover all the features of the plugin and install it in your theme: the result will be extremely satisfying.

== Installation ==
Important: First of all, you have to download and activate WooCommerce plugin, which is mandatory for Yith WooCommerce PDF Invoice and Shipping List to be working.

1. Download and unzip the downloaded file.
2. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `YITH WooCommerce PDF Invoice and Shipping List` from "Plugins" page.

= Configuration =

YITH WooCommerce PDF Invoice and Shipping List will add a new tab called "PDF Invoice" in "YIT Plugins" menu item. There, you will find all Yithemes plugins with quick access to plugin setting page.

== Screenshots ==

1. This is invoice setting page, where you can customize invoice creation settings. You can find it in "YIT Plugins" menu item. You can configure invoice header and functioning of automatic generation of invoice number.
2. This is invoice template setting page, where you can choose sections to be displayed in PDF invoice.
3. In back-end order page, you can quickly see invoice number and date for orders invoiced (below order column).
4. In back-end order page, for each order you find buttons for creating/viewing invoice and shipping list documents.
5. In back-end single order page, you find a metabox that shows information about invoice date and number, if any, and some buttons for generating/viewing invoice and shipping list documents.
6. In front-end order page, you find an additional button for orders with an invoice associated. This button lets customers download the invoice.
7. A basic, fully customizable template for invoices. 

== Changelog ==

= Version 1.2.6 - Released: Dec 13, 2018 =

* Fix: WP tested up version

= Version 1.2.5 - Released: Dec 12, 2018 =

* New: Support to WordPress 5.0
* Update: plugin framework version 3.1.10

= Version 1.2.4 - Released: Oct 24, 2018 =

* New: Support to WooCommerce 3.5.0
* Update: plugin framework
* Update: plugin description
* Update: plugin links

= Version 1.2.3 - Released: Jun 26, 2018 =

* Dev: filter yith_ywpi_company_image_path

= Version 1.2.2 - Released: Feb 14, 2018 =

* Fix: use date completed order as invoice date

= Version 1.2.1 - Released: Feb 08, 2018 =

* Fix: error when view or download the PDF

= Version 1.2.0 - Released: Feb 07, 2018 =

* New: support to WooCommerce 3.3.1
* New: support to WordPress 4.9.4
* Update: plugin framework version 3.0.11


= Version 1.1.22 - Released: Oct 18, 2017 =

* Fix: Execute just once the function to protect the invoices folder without activating the plugin manually

= Version 1.1.21 - Released: Oct 17, 2017 =

* Fix: Security for pdf invoices to be downloaded only for the administrator

= Version 1.1.20 - Released: Oct 9, 2017 =

* Fix: Adding images for the logo no bigger than 300x150 pixels

= Version 1.1.19 - Released: Aug 11, 2017 =

* New: support for WooCommerce 3.1.
* New: tested up to WordPress 4.8.1.
* Update: YITH Plugin Framework
* Update: language file
* Dev: filter yith_invoice_get_formatted_date lets third party code alter the date used as the invoice date

= Version 1.1.18 - Released: May 04, 2017 =

* Fix: Unsupported operand types error when using free shipping method.

= Version 1.1.17 - Released: Apr 30, 2017 =

* Update: YITH Plugin Framework.
* Tweak: improved performance of the saving process.
* Tweak: tested up to WordPress 4.7.4.
* Tweak: changed plugin tags.

= Version 1.1.16 - Released: Apr 05, 2017 =

* Fix: YITH Plugin-fw initialization

= Version 1.1.15 - Released: Mar 06, 2017 =

* New:  Support to WooCommerce 2.7.0-RC1
* Update: YITH Plugin Framework
* Tweak: redirect to order page after the generation of the invoice

= Version 1.1.14 - Released: Feb 20, 2017 =

* New: show fees in invoices

= Version 1.1.13 - Released: Jan 16, 2017 =

* New: WordPress 4.7 ready

= Version 1.1.12 - Released: Jun 13, 2016 =

* Updated: WooCommerce 2.6 100% compatible

= Version 1.1.11 - Released: May 10, 2016 =

* Added: filter 'yith_ywpi_new_invoice_number' that lets you manage the invoice number for new documents
* Added: filter 'yith_ywpi_get_formatted_invoice_number' that let you manage how the invoice number and prefix/suffix should be shown

= Version 1.1.10 - Released: May 04, 2016 =

* Fixed: missing YITH Plugin FW files

= Version 1.1.9.1 - Released: May 02, 2016 =

* Updated: plugin compatible with WordPress 4.5
* Updated: plugin author name
* Updated: YITH Plugin Framework

= Version 1.1.8 - Released: Mar 31, 2016 =

* Updated : YITH Plugin framework
* Fixed : now shipping informations shown on shipping list document instead of the billing informations

= Version 1.1.7 - Released: Feb 22, 2016 =

* Updated : YITH Plugin framework

= Version 1.1.6 - Released: Nov 04, 2015 =

* Fixed: invoice generated and attached to emails not related to orders
* Updated : text-domain changed from ywpi to yith-woocommerce-pdf-invoice

= Version 1.1.5 - Released: Sep 04, 2015 =

* Fixed: removed deprecated woocommerce_update_option_X hook.

= Version 1.1.4 - Released: Aug 12, 2015 =

* Updated: update YITH Plugin framework.

= Version 1.1.3 - Released: May 27, 2015 =

* Fixed : localization issue.

= Version 1.1.2 - Released: May 22, 2015 =

* Added : improved unicode support.

= Version 1.1.1 - Released: May 07, 2015 =

* Added : shipping cost details are shown on invoices.

= Version 1.1.0 - Released: Apr 22, 2015 =

* Fixed : security issue (https://make.wordpress.org/plugins/2015/04/20/fixing-add_query_arg-and-remove_query_arg-usage/)

= Version 1.0.3 - Released: Apr 07, 2015 =

* Fixed : documents with greek text could not be rendered correctly.

= Version 1.0.2 - Released: Mar 05, 2015 =

* Fixed: PDF generation failed sometimes.
* Added: support to WPML.

= Version 1.0.1 - Released: Feb 20, 2015 =

* Added: Create PDF shipping list document
* Added: Shipping list customizable template
* Added: Buttons for generating/viewing invoices and shipping list from back-end order page and single order page.
* Added: Woocommerce 2.3 support

= Version 1.0.0 - Released: Feb 13, 2015 =

* Initial release

== Suggestions ==

If you have any suggestions concerning how to improve YITH WooCommerce PDF Invoice and Shipping List, you can [write to us](mailto:plugins@yithemes.com "Your Inspiration Themes"), so that we can improve YITH WooCommerce PDF Invoice and Shipping List.

== Translators ==

= Available Languages =
* English

If you have created your own language pack, or you have got an updated version of an existing one, you can send it [gettext PO and MO file](http://codex.wordpress.org/Translating_WordPress "Translating WordPress")
[use](http://yithemes.com/contact/ "Your Inspiration Themes"), so that we can improve YITH WooCommerce PDF Invoice and Shipping List.

== Upgrade notice ==

= 1.0.1 =

Initial release