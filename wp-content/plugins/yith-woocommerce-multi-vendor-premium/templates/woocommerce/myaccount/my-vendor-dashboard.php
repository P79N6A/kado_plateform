<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


?>
<h2><?php _e( 'My Vendor Dashboard', 'yith-woocommerce-product-vendors' ) ?></h2>

<p class="myaccount_vendor_dashboard">
	<?php
    if( $is_pending ){
        _e( "You'll be able to access your dashboard as soon as the administrator approves your vendor account.", 'yith-woocommerce-product-vendors' );
        echo '<br/>';
    }

	_e( 'From your vendor dashboard you can view your recent commissions, view the sales report and manage your store and payment settings.', 'yith-woocommerce-product-vendors' );

    if( ! $is_pending ){
        echo ' ';
        printf( __( 'Click <a href="%s">here</a> to access <strong>%s dashboard</strong>.', 'yith-woocommerce-product-vendors' ), apply_filters( 'yith_wcmv_my_vendor_dashboard_uri', esc_url( admin_url() ) ), $vendor_name );
    }

    ?>
</p>
