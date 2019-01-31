<?php
    global $WCMp;

    $is_method_taxable_array = array(
        'none'      => __( 'None', 'dc-woocommerce-multi-vendor' ),
        'taxable'   => __( 'Taxable' , 'dc-woocommerce-multi-vendor' )
    );

    $calculation_type = array(
        'class' => __( 'Per class: Charge shipping for each shipping class individually', 'dc-woocommerce-multi-vendor' ),
        'order' => __( 'Per order: Charge shipping for the most expensive shipping class', 'dc-woocommerce-multi-vendor' ),
    );
?>
<div class="collapse wcmp-modal-dialog" id="wcmp_shipping_method_edit_container">
    <div class="wcmp-modal">
        <div class="wcmp-modal-content">
            <section class="wcmp-modal-main" role="main">
                <header class="wcmp-modal-header page_collapsible modal_head" id="wcmp_shipping_method_edit_general_head">
                    <h1><?php _e( 'Edit Shipping Methods', 'dc-woocommerce-multi-vendor' ); ?></h1>
                    <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                        <span class="screen-reader-text"><?php _e( 'Close modal panel', 'dc-woocommerce-multi-vendor' ); ?></span>
                    </button>  
                </header>
                <article class="modal_body" id="wcmp_shipping_method_edit_form_general_body"> 
                    <input id="method_id_selected" class="form-control" type="hidden" name="method_id_selected"> 
                    <input id="instance_id_selected" class="form-control" type="hidden" name="instance_id_selected"> 
                    <div class="shipping_form" id="free_shipping">
                        <div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Method Title', 'dc-woocommerce-multi-vendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <input id="method_title_fs" class="form-control" type="text" name="method_title" placholder="<?php _e( 'Enter method title', 'dc-woocommerce-multi-vendor' ); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Minimum order amount for free shipping', 'dc-woocommerce-multi-vendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <input id="minimum_order_amount_fs" class="form-control" type="text" name="minimum_order_amount" placholder="<?php _e( '0.00', 'dc-woocommerce-multi-vendor' ); ?>">
                            </div>
                        </div>
                        <input type="hidden" id="method_description_fs" name="method_description" value="" />
                        <!--div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Description', 'dc-woocommerce-multi-vendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <textarea id="method_description_fs" class="form-control" name="method_description"></textarea>
                            </div>
                        </div-->
                    </div>
                    <!-- Local Pickup -->
                    <div class="shipping_form" id="local_pickup">
                        <div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Method Title', 'dc-woocommerce-multi-vendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <input id="method_title_lp" class="form-control" type="text" name="method_title" placholder="<?php _e( 'Enter method title', 'dc-woocommerce-multi-vendor' ); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Cost', 'dc-woocommerce-multi-vendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <input id="method_cost_lp" class="form-control" type="text" name="method_cost" placholder="<?php _e( '0.00', 'dc-woocommerce-multi-vendor' ); ?>">
                            </div>
                        </div>
                        <?php if( apply_filters( 'show_shipping_zone_tax', true ) ) { ?>
                            <div class="form-group">
                                <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Tax Status', 'dc-woocommerce-multi-vendor' ); ?></label>
                                <div class="col-md-9 col-sm-9">
                                    <select id="method_tax_status_lp" class="form-control" name="method_tax_status">
                                        <?php foreach( $is_method_taxable_array as $key => $value ) { ?>
                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <input type="hidden" id="method_description_lp" name="method_description" value="" />
                        <!--div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Description', 'dc-woocommerce-multi-vendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <textarea id="method_description_lp" class="form-control" name="method_description"></textarea>
                            </div>
                        </div-->
                    </div>
                    
                    <div class="shipping_form" id="flat_rate">
                        <div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Method Title', 'dc-woocommerce-multi-vendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <input id="method_title_fr" class="form-control" type="text" name="method_title" placholder="<?php _e( 'Enter method title', 'dc-woocommerce-multi-vendor' ); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Cost', 'dc-woocommerce-multi-vendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <input id="method_cost_fr" class="form-control" type="text" name="method_cost" placholder="<?php _e( '0.00', 'dc-woocommerce-multi-vendor' ); ?>">
                            </div>
                        </div>
                        <?php if( apply_filters( 'show_shipping_zone_tax', true ) ) { ?>
                            <div class="form-group">
                                <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Tax Status', 'dc-woocommerce-multi-vendor' ); ?></label>
                                <div class="col-md-9 col-sm-9">
                                    <select id="method_tax_status_fr" class="form-control" name="method_tax_status">
                                        <?php foreach( $is_method_taxable_array as $key => $value ) { ?>
                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <input type="hidden" id="method_description_fr" name="method_description" value="" />
                        <!--div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Description', 'dc-woocommerce-multi-vendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <textarea id="method_description_fr" class="form-control" name="method_description"></textarea>
                            </div>
                        </div-->
                    <?php

                        if (!apply_filters( 'hide_vendor_shipping_classes', false )) { ?>
                            <div class="wcmp_shipping_classes">
                                <hr>
                                <h2><?php _e('Shipping Class Cost', 'dc-woocommerce-multi-vendor'); ?></h2> 
                                <div class="description mb-15"><?php _e('These costs can be optionally entered based on the shipping class set per product( This cost will be added with the shipping cost above).', 'dc-woocommerce-multi-vendor'); ?></div>
                                <?php
                            
                                // $shipping_classes =  WC()->shipping->get_shipping_classes();
                                $shipping_classes =  get_vendor_shipping_classes();

                                if(empty($shipping_classes)) {
                                    echo '<div class="no_shipping_classes">' . __("No Shipping Classes set by Admin", 'dc-woocommerce-multi-vendor') . '</div>';
                                } else {
                                    foreach ($shipping_classes as $shipping_class ) {
                                        ?>
                                        <div class="form-group">
                                            <label for="" class="control-label col-sm-3 col-md-3"><?php printf( __( 'Cost of Shipping Class: "%s"', 'dc-woocommerce-multi-vendor' ), $shipping_class->name ); ?></label>
                                            <div class="col-md-9 col-sm-9">
                                                <input id="<?php echo $shipping_class->slug; ?>" class="form-control sc_vals" type="text" name="shipping_class_cost[]" placholder="<?php _e( 'N/A', 'dc-woocommerce-multi-vendor' ); ?>" data-shipping_class_id="<?php echo $shipping_class->term_id; ?>">
                                                <div class="description"><?php _e( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'dc-woocommerce-multi-vendor' ) . '<br/><br/>' . _e( 'Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.', 'dc-woocommerce-multi-vendor' ); ?></div>
                                            </div>
                                        </div>
                                        <?php 
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Calculation type', 'dc-woocommerce-multi-vendor' ); ?></label>
                                        <div class="col-md-9 col-sm-9">
                                            <select id="calculation_type" class="form-control" name="calculation_type">
                                                <?php foreach( $calculation_type as $key => $value ) { ?>
                                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                } ?>
                            </div>
                        <?php } ?>
                    </div> 
                    <?php do_action( 'wcmp_vendor_shipping_methods_edit_form_fields', get_current_user_id() ); ?>
                </article>
                <footer class="modal_footer" id="wcmp_shipping_method_edit_general_footer">
                    <div class="inner">
                        <button class="btn btn-default update-shipping-method" id="wcmp_shipping_method_edit_button"><?php _e( 'Save changes', 'dc-woocommerce-multi-vendor' ); ?></button>
                    </div>
                </footer> 
            </section>   
        </div>
    </div>
    <div class="wcmp-modal-backdrop modal-close"></div>
</div>