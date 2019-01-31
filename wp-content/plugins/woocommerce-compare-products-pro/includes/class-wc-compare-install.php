<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * WooCommerce Compare Install Class
 *
 */
class WC_Compare_Install
{
	/**
	 * Init.
	 */
	public function __construct() {
		
		add_action( 'admin_init', array( $this, 'automatic_add_compare_categories' ) );
		add_action( 'admin_init', array( $this, 'automatic_add_features' ) );
		add_action( 'admin_init', array( $this, 'add_meta_all_products' ) );
		add_action( 'admin_init', array( $this, 'automatic_add_widget_to_sidebar' ) );
	}
	
	/**
	 * Automatic Set all Product Categories as Compare Categories
	 *
	 */
	public function automatic_add_compare_categories() {
		$terms = get_terms( "product_cat", array( 'hide_empty' => 0 ) );
		if ( count( $terms ) > 0 ) {
			foreach ( $terms as $cat ) {
				WC_Compare_Functions::update_compare_category_meta( $cat->term_id, 'is_compare_cat', 'yes' );
			}
		}
	}
	
	/**
	 * Automatic Set all Product Attribute as Compare Features
	 *
	 */
	public function automatic_add_features() {
		$all_attributes = wc_get_attribute_taxonomies();
		
		if ( $all_attributes && is_array( $all_attributes ) && count( $all_attributes ) > 0 ) {
			foreach ( $all_attributes as $attribute ) {
				$field_type = WC_Compare_Functions::get_compare_attribute_meta( $attribute->attribute_id, 'field_type' );
				if ( $field_type !== false && $field_type != '' ) continue;
				
				$attribute_type = 'input-text';
				if ( $attribute->attribute_type == 'select' ) $attribute_type = 'drop-down';
				WC_Compare_Functions::update_compare_attribute_meta( $attribute->attribute_id, 'field_type', $attribute_type );
			}
		}
	}
	
	public function add_meta_all_products() {

		// Add deactivate compare feature meta for all products when activate this plugin
		$have_deactivate_meta = get_posts(array('numberposts' => -1, 'post_type' => array('product', 'product_variation'), 'post_status' => array('publish', 'private'), 'meta_key' => '_woo_deactivate_compare_feature'));
		$have_ids = array();
		if (is_array($have_deactivate_meta) && count($have_deactivate_meta) > 0) {
			foreach ($have_deactivate_meta as $product) {
				$have_ids[] = $product->ID;
			}
		}
		if (is_array($have_ids) && count($have_ids) > 0) {
			$no_deactivate_meta = get_posts(array('numberposts' => -1, 'post_type' => array('product', 'product_variation'), 'post_status' => array('publish', 'private'), 'post__not_in' => $have_ids));
		}else {
			$no_deactivate_meta = get_posts(array('numberposts' => -1, 'post_type' => array('product', 'product_variation'), 'post_status' => array('publish', 'private')));
		}
		if (is_array($no_deactivate_meta) && count($no_deactivate_meta) > 0) {
			foreach ($no_deactivate_meta as $product) {
				add_post_meta($product->ID, '_woo_deactivate_compare_feature', '');
			}
		}

	}
	
	public function automatic_add_widget_to_sidebar() {
		$add_to_sidebars = array('primary', 'primary-widget-area', 'sidebar-1');
		$widget_name = 'woo_compare_widget';
		$sidebar_options = get_option('sidebars_widgets');
		$compare_widget = get_option('widget_'.$widget_name);
		$have_widget = false;
		foreach ($sidebar_options as $siderbar_name => $sidebar_widgets) {
			if ($siderbar_name == 'wp_inactive_widgets') continue;
			if (is_array($sidebar_widgets) && count($sidebar_widgets) > 0) {
				foreach ($sidebar_widgets as $sidebar_widget) {
					if (stristr($sidebar_widget, $widget_name) !== false) {
						$have_widget = true;
						break;
					}
				}
			}
			if ($have_widget) break;
		}
		if (!$have_widget) {
			if (!is_array($compare_widget)) $compare_widget = array();
			$count = count($compare_widget)+1;
			$added_widget = false;
			$new_sidebar_options = $sidebar_options;
			foreach ($add_to_sidebars as $sidebar_name) {
				if (isset($sidebar_options[$sidebar_name])) {
					$sidebar_options[$sidebar_name][] = $widget_name.'-'.$count;
					$added_widget = true;
					break;
				}
			}
			if (!$added_widget) {
				foreach ($new_sidebar_options as $siderbar_name => $sidebar_widgets) {
					if ($siderbar_name == 'wp_inactive_widgets') continue;
					$sidebar_options[$siderbar_name][] = $widget_name.'-'.$count;
					break;
				}
			}

			// add widget to sidebar:
			$compare_widget[$count] = array(
				'title' => ''
			);
			update_option('sidebars_widgets', $sidebar_options);
			update_option('widget_'.$widget_name, $compare_widget);
		}
	}
	
}

return new WC_Compare_Install();
?>