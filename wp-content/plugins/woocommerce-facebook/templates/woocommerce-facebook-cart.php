<div class="fb_shopping_cart">
    <?php echo sprintf(_n('<strong>%d item</strong>', '<strong class="count">%d items</strong>', $woocommerce->cart->cart_contents_count, 'woocommerce-facebook-tab'), $woocommerce->cart->cart_contents_count); ?>
    <?php if (sizeof($woocommerce->cart->get_cart())>0) : ?>
	<?php
    if (get_option('js_prices_include_tax')=='yes') :
        _e(' - Total', 'woocommerce-facebook-tab');
    else :
        _e(' - Subtotal', 'woocommerce-facebook-tab');
    endif;

    echo ': ';

    echo '<strong>';
    echo $woocommerce->cart->get_cart_total();
    echo '</strong>';
    ?>
    <?php echo '<span class="actions"><a href="'.$woocommerce->cart->get_cart_url().'" target="_blank">'.__('View Your Cart', 'woocommerce-facebook-tab').'</a> or <a href="'.$woocommerce->cart->get_checkout_url().'" target="_blank">'.__('Checkout', 'woocommerce-facebook-tab').'</a></span>';?>
    <?php endif; ?>
</div>