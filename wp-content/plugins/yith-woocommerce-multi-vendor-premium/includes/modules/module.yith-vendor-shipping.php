<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Vendor_Shipping
 * @package    Yithemes
 * @since      Version 1.9.17
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Vendor_Shipping' ) ) {

    /**
     * YITH_Vendor_Shipping Class
     */
    class YITH_Vendor_Shipping {

        /**
         * Main instance
         */
        private static $_instance = null;

        /**
         * Main Admin Instance
         *
         * @var YITH_Vendors_Admin | YITH_Vendors_Admin_Premium
         * @since 1.9.17
         */
        public $admin = null;

        /**
         * Main Frontpage Instance
         *
         * @var YITH_Vendors_Frontend | YITH_Vendors_Frontend_Premium
         * @since 1.9.17
         */
        public $frontend = null;

        /**
         * Construct
         */
        public function __construct(){
            
            add_filter( 'yith_wcmv_panel_admin_tabs', array( $this, 'add_shipping_tab' ) );

            add_filter( 'woocommerce_shipping_methods', array( $this , 'add_shipping_method' ) );

            $this->init();

        }

        /**
         * Main plugin Instance
         *
         * @static
         * @return YITH_Vendor_Shipping Main instance
         *
         * @since  1.9.17
         * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Class Initializzation
         *
         * Instance the admin or frontend classes
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  1.0
         * @return void
         * @access protected
         */
        public function init() {
            
            if ( is_admin() ) {
                $this->admin = new YITH_Vendor_Shipping_Admin();
            }

            if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
                $this->frontend = new YITH_Vendor_Shipping_Frontend();
            }
        }

        /**
         * Main plugin Instance
         *
         * @param $tabs The vendor admin tabs
         *
         * @return array vendor admin tabs
         *
         * @since  1.9.17
         * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         */
        public function add_shipping_tab( $tabs ){
            $tabs['vendor-shipping'] = __( 'Shipping', 'yith-woocommerce-product-vendors' );
            return $tabs;
        }

        /**
         * @param $methods
         *
         * @return mixed
         *
         * @since  1.9.17
         * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         */
        public function add_shipping_method( $methods ) {
            $methods['yith_wcmv_vendors'] = 'YITH_WCMV_Shipping_Method';
            return $methods;
        }

        /**
         * @return mixed                                               
         * 
         * @since  1.9.17
         * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         */
        public static function yith_wcmv_get_shipping_processing_times() {
            $times = array(
                '' => __( 'Ready to ship in...', 'yith-woocommerce-product-vendors' ),
                '1' => __( '1 business day', 'yith-woocommerce-product-vendors' ),
                '2' => __( '1-2 business day', 'yith-woocommerce-product-vendors' ),
                '3' => __( '1-3 business day', 'yith-woocommerce-product-vendors' ),
                '4' => __( '3-5 business day', 'yith-woocommerce-product-vendors' ),
                '5' => __( '1-2 weeks', 'yith-woocommerce-product-vendors' ),
                '6' => __( '2-3 weeks', 'yith-woocommerce-product-vendors' ),
                '7' => __( '3-4 weeks', 'yith-woocommerce-product-vendors' ),
                '8' => __( '4-6 weeks', 'yith-woocommerce-product-vendors' ),
                '9' => __( '6-8 weeks', 'yith-woocommerce-product-vendors' ),
            );

            return apply_filters( 'yith_wcmv_shipping_processing_times', $times );
        }

        /**
         * @return bool
         *
         * @since  1.9.17
         * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         */
        public static function is_single_vendor_shipping_enabled( $vendor ) {

            return $vendor->enable_shipping == 'yes';

        }

        /**
         *
         */
        public function get_vendor_from_method_id( $method_id, $packages = null ){
            $vendor = false;
            $packages = empty( $packages ) ? WC()->shipping->get_packages() : $packages;

            foreach ( $packages as $id => $package ){
                if( ! empty( $package['rates'][ $method_id ] ) && ! empty( $package['yith-vendor'] ) ){
                    $vendor = $package['yith-vendor'];
                    break;
                }
            }

            return $vendor;
        }

	    /**
	     * Register the commission linked to order
	     *
	     * @param $order_id int The order ID
	     * @param $posted   array The value request
	     *
	     * @since 1.0
	     */
	    public static function register_commissions( $order_id ) {
		    // Only process commissions once
		    $order = wc_get_order( $order_id );

		    $processed =  $order->get_meta( '_shipping_commissions_processed', true );
		    if ( $processed && $processed == 'yes' ) {
			    return;
		    }

		    $order = wc_get_order( $order_id );
		    $shipping_methods = $order->get_shipping_methods();
		    $vendor_owner = get_post_field( 'post_author', $order_id );
		    $vendor = yith_get_vendor( $vendor_owner, 'user' );

		    if( ! empty( $shipping_methods ) ){
			    foreach( $shipping_methods as $shipping_id => $shipping ){
				    /** @var WC_Order_Item_Shipping $shipping */
				    $args = array(
					    'line_item_id'  => $shipping_id,
					    'order_id'      => $order_id,
					    'user_id'       => $vendor_owner,
					    'vendor_id'     => $vendor->id,
					    'amount'        => ( (float) $shipping->get_total('edit') + (float) $shipping->get_total_tax('edit') ),
					    'last_edit'     => current_time( 'mysql' ),
					    'last_edit_gmt' => current_time( 'mysql', 1 ),
					    'rate'          => 1,
					    'type'          => 'shipping'
				    );

				    $commission_id = YITH_Commission()->add( $args );
			    }
		    }

		    // Mark shipping fee as processed
		    $order->add_meta_data( '_shipping_commissions_processed', 'yes', true );
		    $order->save_meta_data();
	    }
    }
}

/**
 * Main instance of plugin
 *
 * @return /YITH_Vendor_Shipping
 * @since  1.9.17
 * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
 */
if ( ! function_exists( 'YITH_Vendor_Shipping' ) ) {
    function YITH_Vendor_Shipping() {
        return YITH_Vendor_Shipping::instance();
    }
}

YITH_Vendor_Shipping();
