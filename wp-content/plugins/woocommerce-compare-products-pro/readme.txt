=== Woocommerce Compare Products ===
Contributors: a3rev, A3 Revolution Software Development team
Tags: WooCommerce, WooCommerce Plugins, WooCommerce compare products, compare products plugin, compare products
Requires at least: 3.7
Tested up to: 3.9.1
Stable tag: 2.2.1

Add a Compare Products Feature to all of your products on you WooCommerce site today with the WooCommerce Compare Products plugin..

== Description ==

WooCommerce Compare Products instantly adds the <strong>cutting edge Compare products feature</strong> to your WooCommerce store in just minutes.

[youtube http://www.youtube.com/watch?v=g8__ZFxKSRA]

WooCommerce Compare Products adds a product Comparison feature to any or all products on your WooCommerce site. Site users click a button or a link to add products to their compare list in the sidebar widget area.

Once products are added to the Compare sidebar widget users click the Compare button and the products they have added show side-by-side with all features in a beautiful mac like pop up screen. In that screen they can compare products prices and features. They can narrow their choices down by removing products. From the pop up screen they can add products to their shopping cart, print the results or return to the product page and continue to add other products.

= Key Features =
* Users don't have to be logged or even registered to use the Compare Products feature.
* Users can add any number of products to the Compare Widget in the sidebar.
* Users can add products to compare from the Product Category Pages or from the single Product page.
* When a product has variations, all the variations (models) that have Compare Products Feature activated are compared. Great way for shoppers to compare different models of the same Product.
* Shoppers can remove unwanted selections right from the sidebar Compare widget or clear all and start again.
* In the Compare pop up window Shoppers can Compare Product Ratings - Product Prices and Product Features.
* Shoppers can add products to their shopping cart right from the Pop Up Compare Screen.
* Any products not removed from the Compare List remain in the Compare Sidebar widget allowing the Shopper to add more products.
* Shoppers can narrow their choice down to 3 products and print them.

= Easy Admin Features =
* The plugin when activated auto adds a Product Comparison link to your WooCommerce sidebar admin section.
* Compare Settings has 3 tabs SETTINGS | FEATURES |  PRODUCTS - each page has extensive help notes via Tool Tips and text.
* Compare Feature only shows on a product once it has been assigned to a Compare Category. Allows you to do an orderly roll-out of the feature across your site.
* Compare Products Express manager- Management Compare features on every product on your site from the one page. See at a glance which products have the feature activated. Add or edit Compare features on any products on your site.
* Edit the Compare Products feature on any products edit page.


[Pro Version](http://a3rev.com/products-page/woocommerce/woocommerce-compare-products/) |
[Read Documentation](http://docs.a3rev.com/user-guides/woocommerce/compare-products/) |
[Support](https://a3rev.com/forums/forum/woocommerce-plugins/compare-products/)

= Localization =
* English (default) - always include.
* .pot file (woo_cp.pot) in languages folder for translations.
* Your translation? Please [send it to us](http://www.a3rev.com/contact/) We'll acknowledge your work and link to your site.
Please [Contact us](http://www.a3rev.com/contact/) if you'd like to provide a translation or an update.


== Screenshots ==



== Installation ==
1. Upload the folder woocommerce-compare-products to the /wp-content/plugins/ directory

2. Activate the plugin through the Plugins menu in WordPress

== Usage ==

1. Open WooCommerce > Compare Settings

2. Opens to the SETTINGS tab

3. Follow the detailed set up instructions on Compare setting dashboard.

* Style your Compare Products Fly-Out screen - upload header image and set screen dimensions.
* Select to show Compare Feature on Product Pages as Button or Hyperlink Text.
* Set text to show in Button or Link.
* Set Compare Products Tab to show in WooCommerce Product Page Navigation Tabs.
* Save Settings to save your work. You are now ready to add the Compare features data for each product.

4. Click the FEATURES tab

5. Click the dropdown arrow at the end of the Master Category tab. You'll see that all of your sites Parent variations have been auto created as Compare Features.

6. Edit each Master category compare feature to add the required feature fields.

10. Follow the instructions on adding and managing Compare Features to the master category.

11. Visit Product edit pages to where you (i) Deactivate the feature for that Product or (ii) Add Compare Feature Fields data for that Products. Update (or publish) and your compare features for that product is published.

Celebrate the extra sales Compare Products brings you !

== Frequently Asked Questions ==

=
When can I use this plugin? =

You can use this plugin when you have installed the WooCommerce plugin.

= How do I change the Color of the Button to match my theme? = It is an easy task to change the color of the button - but you will need some coding knowledge.

All objects in the plugin have a class so you can style for them. Using an ftp client open the style.css in your theme.

Look for the style of your themes buttons below is an example - it will look something like this

#wrap input[type="submit"], #wrap input[type="button"] {
background: url('images/bg-button.png') no-repeat scroll right top transparent;
border: 1px solid #153B94;
border-radius: 5px 5px 5px 5px;
box-shadow: 1px 1px 2px #333333;
color: #FFFFFF;
cursor: pointer;
font-size: 12px;
padding: 9px 27px 7px 10px;
}

Once you have found that in themes style.css directory then add that style into your themes style.css under the class name 'bt_compare_this' which is for the Compare button on the product pages and class name 'compare_button_go' for the Compare button in the sidebar widget. This is how it would look using the example above as the style for the button.

input.bt_compare_this {
background: url('images/bg-button.png') no-repeat scroll right top transparent;
border: 1px solid #153B94;
border-radius: 5px 5px 5px 5px;
box-shadow: 1px 1px 2px #333333;
color: #FFFFFF;
cursor: pointer;
font-size: 12px;
padding: 9px 27px 7px 10px;
}

This will then mean that style will apply for all input tag in div that has the class compare_button_container to change the sidebar widget button you do the same but use the class 'compare_button_go'

== Support ==
All support requests, questions or suggestions should be posted to the [Support](https://a3rev.com/forums/forum/woocommerce-plugins/compare-products/) WooCommerce Compare Products Home page on the a3rev site.

== Upgrade Notice ==

= 2.2.0 =
Major Version upgrade. Full integration with WooCommerce Product categories, Attributes and Terms. More features with less code, overall reduction of 758 lines of code.

= 2.1.6.2 =
Upgrade now for Tweaks that prevent caching plugins caching Compare Widget and Comparison table content. Also 100% Compatility with WC v2.1.8 and WP 3.9

= 2.1.6.1 =
Upgrade now for undefined index bug fix that surfaced in version 2.1.6

= 2.1.2 =
Install this upgrade to fix colour picker bug in your plugin

= 2.1.1 =
Upgrade now to fix a backward compatibility issue with External / Affiliate Links.