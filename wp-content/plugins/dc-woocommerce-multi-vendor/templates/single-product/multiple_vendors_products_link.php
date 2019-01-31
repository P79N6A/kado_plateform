<?php
/**
 * Single Product Multiple vendors
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/single-product/multiple_vendors_products_link.php.
 *
 * HOWEVER, on occasion WCMp will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * 
 * @author  WC Marketplace
 * @package dc-woocommerce-multi-vendor/Templates
 * @version 2.3.4
 */
global $WCMp, $product, $wpdb; 

//$have_parent = $product->get_parent_id();
//$parent_product = $product->get_id();
//if($have_parent != 0){
//    $parent_product = $product->get_parent_id();
//}
//$mapped_products = wc_get_products(array('post_parent' => $parent_product, 'posts_per_page' => -1));
//$mapped_products[] = wc_get_product($parent_product);
//if($mapped_products && count($mapped_products) > 1){
//    $button_text = apply_filters('wcmp_more_vendors', __('More Vendors','dc-woocommerce-multi-vendor'));
//    echo '<a  href="#" class="goto_more_offer_tab button">'.$button_text.'</a>';
//}

$has_product_map_id = get_post_meta($product->get_id(), '_wcmp_spmv_map_id', true);
if($has_product_map_id){
    $products_map_data_ids = get_wcmp_spmv_products_map_data($has_product_map_id);
    $mapped_products = array_diff($products_map_data_ids, array($product->get_id()));
    if(count($mapped_products) >= 1){
        $button_text = apply_filters('wcmp_more_vendors', __('More Vendors','dc-woocommerce-multi-vendor'));
        $button_text = apply_filters('wcmp_single_product_more_vendors_text', $button_text, $product);
        echo '<a  href="#" class="goto_more_offer_tab button">'.$button_text.'</a>';
    }
}

