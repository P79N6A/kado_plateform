<?php
/**
 * Plugin Name: KADO RAYON Metabox
 * Plugin URI: http://domain.com
 * Description: Permet de lier les metabox au produits dans KADO
 * Author: MVONDO Yannick
 * Author URI: http:// domain.com
 * Version: 1.0
 */


//add_filter( 'rwmb_meta_boxes', 'rayon_meta_boxes' );
add_action('admin_init', 'rayon_meta_boxes');
function rayon_meta_boxes( $meta_boxes ) {
    /*$meta_boxes[] = array(
        'title' => __( ' Rayon KADO Info', 'pharmacy'),
        'fields' => array(
            'id' => 'unit',
            'name' => 'text',
            'datalist' => array(
                'options' => array(
                    __('Box', 'pharmacy'),
                    __('Blister pack', 'pharmacy'),
                )
            )
        )
    );*/
return $meta_boxes;
}