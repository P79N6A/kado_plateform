<?php
/**
 * Admin new order email
 *
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.0.0
 *
 * @var string $email_heading
 * @var YITH_Vendor $vendor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo __( 'Owner', 'yith-woocommerce-product-vendors' ) . ':';
echo $owner->user_firstname . ' ' . $owner->user_lastname . " \n\n";

echo __( 'Store Name', 'yith-woocommerce-product-vendors' ) . ':';
echo $vendor->name . " \n\n";

echo __( 'Location', 'yith-woocommerce-product-vendors' ) . ':';
echo $vendor->location . " \n\n";

echo __( 'Store Email', 'yith-woocommerce-product-vendors' ) . ':';
echo $vendor->store_email . " \n\n";

echo __( 'Telephone', 'yith-woocommerce-product-vendors' ) . ':';
echo $vendor->telephone . " \n\n";