<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


$general_options = array(

	'general' => array(

		array(
			'name' => __( 'Product settings', 'yith-woocommerce-barcodes' ),
			'type' => 'title',
		),
		'ywbc_enable_on_products'                => array(
			'name'    => __( 'Enable on products', 'yith-woocommerce-barcodes' ),
			'type'    => 'checkbox',
			'desc'    => __( 'Enable barcode generation on products', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_enable_on_products',
			'default' => 'yes',
		),
		'ywbc_create_on_products'                => array(
			'name'    => __( 'New products', 'yith-woocommerce-barcodes' ),
			'type'    => 'checkbox',
			'desc'    => __( 'Create barcode automatically for new products', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_create_on_products',
			'default' => 'no',
		),
		'ywbc_products_default_barcode_protocol' => array(
			'name'    => __( 'Product barcode protocol', 'yith-woocommerce-barcodes' ),
			'type'    => 'select',
			'desc'    => __( 'Choose the barcode protocol you want to use to generate a new barcode on products', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_products_protocol',
			'options' => YITH_Barcode::get_protocols(),
			'default' => 'EAN13',
		),
		'ywbc_product_manual_barcode'            => array(
			'name'    => __( 'Enable manual barcode', 'yith-woocommerce-barcodes' ),
			'type'    => 'checkbox',
			'desc'    => __( 'Enable this option, if you want to show a form in product page where a barcode value can be entered', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_product_manual_barcode',
			'default' => 'no',
		),
		'ywbc_show_on_product_page'              => array(
			'name'    => __( 'Show in product page', 'yith-woocommerce-barcodes' ),
			'type'    => 'checkbox',
			'desc'    => __( 'Choose whether you want to show the barcode image on product page or not', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_show_on_product_page',
			'default' => 'yes',
		),
		array(
			'type' => 'sectionend',
		),
		array(
			'name' => __( 'Order settings', 'yith-woocommerce-barcodes' ),
			'type' => 'title',
		),
		'ywbc_enable_on_orders'                  => array(
			'name'    => __( 'Enable on orders', 'yith-woocommerce-barcodes' ),
			'type'    => 'checkbox',
			'desc'    => __( 'Enable barcode generation on orders', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_enable_on_orders',
			'default' => 'yes',
		),
		'ywbc_create_on_orders'                  => array(
			'name'    => __( 'New orders', 'yith-woocommerce-barcodes' ),
			'type'    => 'checkbox',
			'desc'    => __( 'Generate barcode automatically for new orders', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_create_on_orders',
			'default' => 'no',
		),
		'ywbc_orders_default_barcode_protocol'   => array(
			'name'    => __( 'Barcode protocol on orders', 'yith-woocommerce-barcodes' ),
			'type'    => 'select',
			'desc'    => __( 'Choose the barcode protocol you want to use to generate a new barcode on orders', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_orders_protocol',
			'options' => YITH_Barcode::get_protocols(),
			'default' => 'EAN13',
		),
		'ywbc_show_on_order_page'                => array(
			'name'    => __( "Show in order page", 'yith-woocommerce-barcodes' ),
			'type'    => 'checkbox',
			'desc'    => __( 'Set whether the barcode should be shown in order page or not', 'yith-woocommerce-barcodes' ),
			'id'      => 'ywbc_show_on_order_page',
			'default' => 'yes',
		),
		'ywbc_show_on_emails'                    => array(
			'name'    => __( 'Order barcode in emails', 'yith-woocommerce-barcodes' ),
			'type'    => 'radio',
			'id'      => 'ywbc_show_on_emails',
			'options' => array(
				'no'        => __( "Never show order barcode in emails", 'yith-woocommerce-barcodes' ),
				'completed' => __( "Show order barcode only in emails sent after the order is set to 'completed'", 'yith-woocommerce-barcodes' ),
				'all'       => __( "Show order barcode on every email", 'yith-woocommerce-barcodes' ),
			),
			'default' => 'no',
		),
		'ywbc_show_product_barcode_on_emails'                    => array(
			'name'    => __( 'Product barcode in emails', 'yith-woocommerce-barcodes' ),
			'type'    => 'checkbox',
			'id'      => 'ywbc_show_product_barcode_on_emails',
			'default' => 'no',
		),
		array(
			'type' => 'sectionend',
		),
	),
);

return $general_options;
