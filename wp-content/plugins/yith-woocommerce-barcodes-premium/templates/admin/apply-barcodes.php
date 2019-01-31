<?php
if (!defined('ABSPATH')) {
    exit;
}

$action = isset($option['action']) ? $option['action'] : 'apply_barcode_to_products';
$desc = isset($option['desc']) ? $option['desc'] : '';
$name = isset($option['name']) ? $option['name'] : '';

$query_args = apply_filters('yith_barcode_query_args_get_products',array(
    'post_type' => apply_filters('yith_barcode_query_post_type', 'product' ),
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'meta_query' => array(
        array(
            'key' => YITH_Barcode::YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE,
            'compare' => 'NOT EXISTS'
        )
    )
    )
);

$products = get_posts($query_args);

$json_product_ids = array();

foreach ($products as $product) {
    $json_product_ids[] = $product;
}
$json_product_ids = esc_attr(json_encode($json_product_ids));
?>

<tr valign="top">
    <th scope="row" class="titledesc"><?php esc_attr_e($name); ?></th>
    <td class="forminp">

        <input type="button" class="button button-primary ywbc_apply_barcode"
               data-barcode_action="<?php esc_attr_e($action); ?>" data-item_ids="<?php echo $json_product_ids; ?>"
               value="<?php _e(sprintf('Apply to %s products', count($products)), 'yith-woocommerce-barcode'); ?>">
        <span class="description"><?php esc_attr_e($desc); ?></span>
    </td>
</tr>
<tr valign="top">
    <td colspan="2">
        <div class="ywbc_apply_messages">
            <span class="ywbc_apply_icon"></span>
            <span class="ywbc_apply_text"></span>
        </div>
    </td>
</tr>
<tr valign="top">
    <td class="forminp forminp-progressbar" colspan="2">
        <div class="ywbc_apply_progressbar" id="ywbc_apply_progressbar_all">
            <div class="ywbc_apply_progressbar_percent" id="ywbc_apply_progressbar_percent_all"></div>
        </div>
    </td>
</tr>