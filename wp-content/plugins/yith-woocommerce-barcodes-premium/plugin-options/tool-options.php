<?php
if( !defined( 'ABSPATH' ) )
    exit;


$tool = array(

    'tool'  =>  array(

        'tool_section_start'   =>  array(
            'name'  => __('Generate Barcode', 'yith-woocommerce-barcodes'),
            'type' =>   'title',
        ),

        'tool_apply_barcodes_product'    => array(
            'name' => __('Apply Barcodes to products', 'yith-woocommerce-barcodes'),
            'type'  =>'apply-barcodes',
            'desc' => __('Apply automatically barcode to all products', 'yith-woocommerce-barcodes' ),
            'id'    =>  'ywbc_apply_product_barcode'
        ),

        'tool_section_end' =>  array(
            'type'  =>  'sectionend',
        ),

    )
);

return apply_filters( 'ywbc_tool_ptions', $tool );