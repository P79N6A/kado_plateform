<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * WooCommerce Compare Hook Filter
 *
 * Hook anf Filter into woocommerce plugin
 *
 * Table Of Contents
 *
 * register_admin_screen()
 * template_loader()
 * add_google_fonts()
 * include_customized_style()
 * woocp_shop_add_compare_button()
 * woocp_shop_add_compare_button_below_cart()
 * woocp_details_add_compare_button()
 * woocp_details_add_compare_button_below_cart()
 * add_compare_button()
 * show_compare_fields()
 * woocp_variable_ajax_add_to_cart()
 * woocp_add_to_compare()
 * woocp_remove_from_popup_compare()
 * woocp_update_compare_popup()
 * woocp_update_compare_widget()
 * woocp_update_total_compare()
 * woocp_remove_from_compare()
 * woocp_clear_compare()
 * woocp_footer_script()
 * woocp_variable_add_to_cart_script()
 * woocp_product_featured_tab()
 * woocp_product_featured_tab_woo_2_0()
 * woocp_product_featured_panel()
 * woocp_product_featured_panel_woo_2_0()
 * woocp_set_selected_attributes()
 * a3_wp_admin()
 * admin_sidebar_menu_css()
 */
class WC_Compare_Hook_Filter
{
	public static function register_admin_screen () {
		
		$product_comparison = add_menu_page( __('Product Comparison', 'woo_cp'), __('WC Compare', 'woo_cp'), 'manage_woocommerce', 'woo-compare-features', array( 'WC_Compare_Features_Panel', 'admin_screen' ), null, '55.222');
		
		$compare_features = add_submenu_page('woo-compare-features', __( 'Compare Category & Attribute', 'woo_cp' ), __( 'Category & Attribute', 'woo_cp' ), 'manage_woocommerce', 'woo-compare-features', array( 'WC_Compare_Features_Panel', 'admin_screen' ) );
		
		$compare_products = add_submenu_page('woo-compare-features', __( 'Compare Products Manager', 'woo_cp' ), __( 'Product Manager', 'woo_cp' ), 'manage_woocommerce', 'woo-compare-products', array( 'WC_Compare_Products_Class', 'woocp_products_manager' ) );
				
	} // End register_admin_screen()
	
	public static function template_loader( $template ) {
		global $product_compare_id;
		global $post;
		$current_db_version = get_option( 'woocommerce_db_version', null );

		if ( is_object( $post ) && $product_compare_id == $post->ID ) {
			
			if ( version_compare( $current_db_version, '2.1.0', '<' ) && null !== $current_db_version ) {
				$file 	= 'product-compare-old.php';
			} else {
				$file 	= 'product-compare.php';
			}
			$find[] = $file;
			$find[] = apply_filters( 'woocommerce_template_url', 'woocommerce/' ) . $file;
			
			$template = locate_template( $find );
			if ( ! $template ) $template = WOOCP_FILE_PATH . '/templates/' . $file;

		}
	
		return $template;
	}
	
	public static function nocache_ours_page() {
		global $product_compare_id;
		
		$woocp_page_uris   = array();
		// Exclude querystring when using page ID
		$woocp_page_uris[] = 'p=' . $product_compare_id;
		$woocp_page_uris[] = 'page_id=' . $product_compare_id;
		
		// Exclude permalinks
		$comparision_page      = get_post( $product_compare_id );
		
		if ( ! is_null( $comparision_page ) )
			$woocp_page_uris[] = '/' . $comparision_page->post_name;
		
		if ( is_array( $woocp_page_uris ) ) {
			foreach( $woocp_page_uris as $uri ) {
				if ( strstr( $_SERVER['REQUEST_URI'], $uri ) ) {
					if ( ! defined( 'DONOTCACHEPAGE' ) )
						define( "DONOTCACHEPAGE", "true" );
		
					if ( ! defined( 'DONOTCACHEOBJECT' ) )
						define( "DONOTCACHEOBJECT", "true" );
		
					if ( ! defined( 'DONOTCACHEDB' ) )
						define( "DONOTCACHEDB", "true" );
		
					nocache_headers();
				}
			}
		}
	}
	
	public static function add_google_fonts() {
		global $wc_compare_fonts_face;
		global $woo_compare_product_page_button_style, $woo_compare_product_page_view_compare_style, $woo_compare_widget_style, $woo_compare_widget_title_style, $woo_compare_widget_button_style, $woo_compare_widget_clear_all_style, $woo_compare_grid_view_button_style, $woo_compare_gridview_view_compare_style;
		$google_fonts = array( 
							$woo_compare_product_page_button_style['product_compare_link_font']['face'], 
							$woo_compare_product_page_button_style['button_font']['face'], 
							$woo_compare_product_page_view_compare_style['product_view_compare_link_font']['face'], 
							$woo_compare_product_page_view_compare_style['button_font']['face'], 
							$woo_compare_widget_style['text_font']['face'], 
							$woo_compare_widget_title_style['widget_title_font']['face'],
							$woo_compare_widget_title_style['total_font']['face'],
							$woo_compare_widget_button_style['compare_widget_link_font']['face'],
							$woo_compare_widget_button_style['button_font']['face'],
							$woo_compare_widget_clear_all_style['clear_text_font']['face'],
							$woo_compare_widget_clear_all_style['clear_all_button_font']['face'],
							$woo_compare_grid_view_button_style['link_font']['face'],
							$woo_compare_grid_view_button_style['button_font']['face'],
							$woo_compare_gridview_view_compare_style['gridview_view_compare_link_font']['face'],
						);
						
		$google_fonts = apply_filters( 'wc_compare_google_fonts', $google_fonts );
		
		$wc_compare_fonts_face->generate_google_webfonts( $google_fonts );
	}
	
	public static function add_google_fonts_comparison_page() {
		global $wc_compare_fonts_face;
		global $woo_compare_page_style, $woo_compare_table_content_style, $woo_compare_product_prices_style, $woo_compare_addtocart_style, $woo_compare_viewcart_style, $woo_compare_print_page_settings, $woo_compare_close_window_button_style;
		$google_fonts = array( 
							$woo_compare_page_style['no_product_message_font']['face'],
							$woo_compare_table_content_style['feature_title_font']['face'],
							$woo_compare_table_content_style['content_font']['face'],
							$woo_compare_table_content_style['empty_font']['face'],
							$woo_compare_table_content_style['product_name_font']['face'],
							$woo_compare_product_prices_style['price_font']['face'],
							$woo_compare_addtocart_style['addtocart_link_font']['face'],
							$woo_compare_addtocart_style['addtocart_button_font']['face'],
							$woo_compare_viewcart_style['viewcart_link_font']['face'],
							$woo_compare_print_page_settings['print_message_font']['face'],
							$woo_compare_print_page_settings['print_link_font']['face'],
							$woo_compare_print_page_settings['button_font']['face'],
							$woo_compare_close_window_button_style['close_link_font']['face'],
							$woo_compare_close_window_button_style['button_font']['face'],
						);
						
		$google_fonts = apply_filters( 'wc_comparison_page_google_fonts', $google_fonts );
		
		$wc_compare_fonts_face->generate_google_webfonts( $google_fonts );
	}
	
	public static function include_customized_style() {
		include( WOOCP_DIR. '/templates/customized_style.php' );
	}

	public static function woocp_shop_add_compare_button($template_name, $template_path, $located) {
		global $post;
		global $product;
		global $woo_compare_grid_view_settings, $woo_compare_grid_view_button_style;
		global $woo_compare_comparison_page_global_settings;
		global $woo_compare_gridview_view_compare_style;
		global $product_compare_id;
		extract($woo_compare_grid_view_settings);
		extract($woo_compare_grid_view_button_style);
		if ($template_name == 'loop/add-to-cart.php') {
			$product_id = $product->id;
			if (($post->post_type == 'product' || $post->post_type == 'product_variation') && WC_Compare_Functions::check_product_activate_compare($product_id) && WC_Compare_Functions::check_product_have_cat($product_id)) {
				
				$widget_compare_popup_view_button = '';
				if ( $woo_compare_comparison_page_global_settings['open_compare_type'] != 'new_page' ) $widget_compare_popup_view_button = 'woo_bt_view_compare_popup';
				
				$compare_grid_view_custom_class = '';
				$compare_grid_view_text = $woo_compare_grid_view_button_style['button_text'];
				$compare_grid_view_class = 'woo_bt_compare_this_button';
				if ($woo_compare_grid_view_button_style['grid_view_button_type'] == 'link') {
					$compare_grid_view_custom_class = '';
					$compare_grid_view_text = $woo_compare_grid_view_button_style['link_text'];
					$compare_grid_view_class = 'woo_bt_compare_this_link';
				}
				
				$view_compare_html = '';
				if ($woo_compare_gridview_view_compare_style['disable_gridview_view_compare'] == 0) {
					$gridview_view_compare_custom_class = '';
					$gridview_view_compare_text = $woo_compare_gridview_view_compare_style['gridview_view_compare_link_text'];
					$gridview_view_compare_class = 'woo_bt_view_compare_link';
					
					$product_compare_page = get_permalink($product_compare_id);
					if ($woo_compare_comparison_page_global_settings['open_compare_type'] != 'new_page') {
						$product_compare_page = '#';
					}
					$view_compare_html = '<div style="clear:both;"></div><a class="woo_bt_view_compare '.$widget_compare_popup_view_button.' '.$gridview_view_compare_class.' '.$gridview_view_compare_custom_class.'" href="'.$product_compare_page.'" target="_blank" alt="" title="" style="display:none;">'.$gridview_view_compare_text.'</a>';
				}
				
				$compare_html = '<div class="woo_grid_compare_button_container"><a class="woo_bt_compare_this '.$compare_grid_view_class.' '.$compare_grid_view_custom_class.'" id="woo_bt_compare_this_'.$product_id.'">'.$compare_grid_view_text.'</a>' . $view_compare_html . '<input type="hidden" id="input_woo_bt_compare_this_'.$product_id.'" name="product_compare_'.$product_id.'" value="'.$product_id.'" /></div>';
				echo $compare_html;
			}
		}
	}
	
	public static function woocp_shop_add_compare_button_below_cart() {
		global $post;
		global $product;
		global $woo_compare_grid_view_settings, $woo_compare_grid_view_button_style;
		global $woo_compare_comparison_page_global_settings;
		global $woo_compare_gridview_view_compare_style;
		global $product_compare_id;
		
		if ( $woo_compare_grid_view_settings['disable_grid_view_compare'] == 1 || $woo_compare_grid_view_settings['grid_view_button_position'] == 'above' ) return;
		
		extract($woo_compare_grid_view_settings);
		extract($woo_compare_grid_view_button_style);
			$product_id = $product->id;
			if (($post->post_type == 'product' || $post->post_type == 'product_variation') && WC_Compare_Functions::check_product_activate_compare($product_id) && WC_Compare_Functions::check_product_have_cat($product_id)) {
				
				$widget_compare_popup_view_button = '';
				if ( $woo_compare_comparison_page_global_settings['open_compare_type'] != 'new_page' ) $widget_compare_popup_view_button = 'woo_bt_view_compare_popup';
				
				$compare_grid_view_custom_class = '';
				$compare_grid_view_text = $woo_compare_grid_view_button_style['button_text'];
				$compare_grid_view_class = 'woo_bt_compare_this_button';
				if ($woo_compare_grid_view_button_style['grid_view_button_type'] == 'link') {
					$compare_grid_view_custom_class = '';
					$compare_grid_view_text = $woo_compare_grid_view_button_style['link_text'];
					$compare_grid_view_class = 'woo_bt_compare_this_link';
				}
				
				$view_compare_html = '';
				if ($woo_compare_gridview_view_compare_style['disable_gridview_view_compare'] == 0) {
					$gridview_view_compare_custom_class = '';
					$gridview_view_compare_text = $woo_compare_gridview_view_compare_style['gridview_view_compare_link_text'];
					$gridview_view_compare_class = 'woo_bt_view_compare_link';
					
					$product_compare_page = get_permalink($product_compare_id);
					if ($woo_compare_comparison_page_global_settings['open_compare_type'] != 'new_page') {
						$product_compare_page = '#';
					}
					$view_compare_html = '<div style="clear:both;"></div><a class="woo_bt_view_compare '.$widget_compare_popup_view_button.' '.$gridview_view_compare_class.' '.$gridview_view_compare_custom_class.'" href="'.$product_compare_page.'" target="_blank" alt="" title="" style="display:none;">'.$gridview_view_compare_text.'</a>';
				}
				
				$compare_html = '<div class="woo_grid_compare_button_container"><a class="woo_bt_compare_this '.$compare_grid_view_class.' '.$compare_grid_view_custom_class.'" id="woo_bt_compare_this_'.$product_id.'">'.$compare_grid_view_text.'</a>' . $view_compare_html . '<input type="hidden" id="input_woo_bt_compare_this_'.$product_id.'" name="product_compare_'.$product_id.'" value="'.$product_id.'" /></div>';
				echo $compare_html;
			}
	}

	public static function woocp_details_add_compare_button() {
		global $post;
		global $product;
		global $woo_compare_product_page_button_style;
		global $woo_compare_product_page_settings;
		global $woo_compare_comparison_page_global_settings;
		global $woo_compare_product_page_view_compare_style;
		global $product_compare_id;
		extract($woo_compare_product_page_button_style);
		
			$product_id = $product->id;
			if (($post->post_type == 'product' || $post->post_type == 'product_variation') && WC_Compare_Functions::check_product_activate_compare($product_id) && $woo_compare_product_page_settings['auto_add'] == 'yes' && WC_Compare_Functions::check_product_have_cat($product_id)) {
				
				$widget_compare_popup_view_button = '';
				if ( $woo_compare_comparison_page_global_settings['open_compare_type'] != 'new_page' ) $widget_compare_popup_view_button = 'woo_bt_view_compare_popup';
				
				$product_compare_custom_class = '';
				$product_compare_text = $woo_compare_product_page_button_style['product_compare_button_text'];
				$product_compare_class = 'woo_bt_compare_this_button';
				if ($woo_compare_product_page_button_style['product_compare_button_type'] == 'link') {
					$product_compare_custom_class = '';
					$product_compare_text = $woo_compare_product_page_button_style['product_compare_link_text'];
					$product_compare_class = 'woo_bt_compare_this_link';
				}
				
				$view_compare_html = '';
				if ($woo_compare_product_page_view_compare_style['disable_product_view_compare'] == 0) {
					$product_view_compare_custom_class = '';
					$product_view_compare_text = $woo_compare_product_page_view_compare_style['product_view_compare_link_text'];
					$product_view_compare_class = 'woo_bt_view_compare_link';
					if ($woo_compare_product_page_view_compare_style['product_view_compare_button_type'] == 'button') {
						$product_view_compare_custom_class = '';
						$product_view_compare_text = $woo_compare_product_page_view_compare_style['product_view_compare_button_text'];
						$product_view_compare_class = 'woo_bt_view_compare_button';
					}
					$product_compare_page = get_permalink($product_compare_id);
					if ($woo_compare_comparison_page_global_settings['open_compare_type'] != 'new_page') {
						$product_compare_page = '#';
					}
					$view_compare_html = '<div style="clear:both;"></div><a class="woo_bt_view_compare '.$widget_compare_popup_view_button.' '.$product_view_compare_class.' '.$product_view_compare_custom_class.'" href="'.$product_compare_page.'" target="_blank" alt="" title="" style="display:none;">'.$product_view_compare_text.'</a>';
				}
				$compare_html = '<div class="woo_compare_button_container"><a class="woo_bt_compare_this '.$product_compare_class.' '.$product_compare_custom_class.'" id="woo_bt_compare_this_'.$product_id.'">'.$product_compare_text.'</a>' . $view_compare_html . '<input type="hidden" id="input_woo_bt_compare_this_'.$product_id.'" name="product_compare_'.$product_id.'" value="'.$product_id.'" /></div>';
				echo $compare_html;
			}
	}
	
	public static function woocp_details_add_compare_button_below_cart($template_name, $template_path, $located){
		global $post;
		global $product;
		global $woo_compare_product_page_button_style;
		global $woo_compare_product_page_settings;
		global $woo_compare_comparison_page_global_settings;
		global $woo_compare_product_page_view_compare_style;
		global $product_compare_id;
		extract($woo_compare_product_page_button_style);
		if (in_array($template_name, array('single-product/add-to-cart/simple.php', 'single-product/add-to-cart/grouped.php', 'single-product/add-to-cart/external.php', 'single-product/add-to-cart/variable.php'))) {
			$product_id = $product->id;
			if (($post->post_type == 'product' || $post->post_type == 'product_variation') && WC_Compare_Functions::check_product_activate_compare($product_id) && $woo_compare_product_page_settings['auto_add'] == 'yes' && WC_Compare_Functions::check_product_have_cat($product_id)) {
				
				$widget_compare_popup_view_button = '';
				if ( $woo_compare_comparison_page_global_settings['open_compare_type'] != 'new_page' ) $widget_compare_popup_view_button = 'woo_bt_view_compare_popup';
				
				$product_compare_custom_class = '';
				$product_compare_text = $woo_compare_product_page_button_style['product_compare_button_text'];
				$product_compare_class = 'woo_bt_compare_this_button';
				if ($woo_compare_product_page_button_style['product_compare_button_type'] == 'link') {
					$product_compare_custom_class = '';
					$product_compare_text = $woo_compare_product_page_button_style['product_compare_link_text'];
					$product_compare_class = 'woo_bt_compare_this_link';
				}
				
				$view_compare_html = '';
				if ($woo_compare_product_page_view_compare_style['disable_product_view_compare'] == 0) {
					$product_view_compare_custom_class = '';
					$product_view_compare_text = $woo_compare_product_page_view_compare_style['product_view_compare_link_text'];
					$product_view_compare_class = 'woo_bt_view_compare_link';
					if ($woo_compare_product_page_view_compare_style['product_view_compare_button_type'] == 'button') {
						$product_view_compare_custom_class = '';
						$product_view_compare_text = $woo_compare_product_page_view_compare_style['product_view_compare_button_text'];
						$product_view_compare_class = 'woo_bt_view_compare_button';
					}
					$product_compare_page = get_permalink($product_compare_id);
					if ($woo_compare_comparison_page_global_settings['open_compare_type'] != 'new_page') {
						$product_compare_page = '#';
					}
					$view_compare_html = '<div style="clear:both;"></div><a class="woo_bt_view_compare '.$widget_compare_popup_view_button.' '.$product_view_compare_class.' '.$product_view_compare_custom_class.'" href="'.$product_compare_page.'" target="_blank" alt="" title="" style="display:none;">'.$product_view_compare_text.'</a>';
				}
			
				$compare_html = '<div class="woo_compare_button_container"><a class="woo_bt_compare_this '.$product_compare_class.' '.$product_compare_custom_class.'" id="woo_bt_compare_this_'.$product_id.'">'.$product_compare_text.'</a>' . $view_compare_html . '<input type="hidden" id="input_woo_bt_compare_this_'.$product_id.'" name="product_compare_'.$product_id.'" value="'.$product_id.'" /></div>';
				echo $compare_html;
			}
		}
	}

	public static function add_compare_button($product_id='') {
		global $post;
		global $woo_compare_product_page_button_style;
		global $woo_compare_comparison_page_global_settings;
		global $woo_compare_product_page_view_compare_style;
		global $product_compare_id;
		extract($woo_compare_product_page_button_style);
		if (trim($product_id) == '') $product_id = $post->ID;
		$post_type = get_post_type($product_id);
		$html = '';
		if (($post_type == 'product' || $post_type == 'product_variation') && WC_Compare_Functions::check_product_activate_compare($product_id) && WC_Compare_Functions::check_product_have_cat($product_id)) {
			
			$widget_compare_popup_view_button = '';
			if ( $woo_compare_comparison_page_global_settings['open_compare_type'] != 'new_page' ) $widget_compare_popup_view_button = 'woo_bt_view_compare_popup';
				
			$product_compare_custom_class = '';
			$product_compare_text = $woo_compare_product_page_button_style['product_compare_button_text'];
			$product_compare_class = 'woo_bt_compare_this_button';
			if ($woo_compare_product_page_button_style['product_compare_button_type'] == 'link') {
				$product_compare_custom_class = '';
				$product_compare_text = $woo_compare_product_page_button_style['product_compare_link_text'];
				$product_compare_class = 'woo_bt_compare_this_link';
			}
			
			$view_compare_html = '';
			if ($woo_compare_product_page_view_compare_style['disable_product_view_compare'] == 0) {
				$product_view_compare_custom_class = '';
				$product_view_compare_text = $woo_compare_product_page_view_compare_style['product_view_compare_link_text'];
				$product_view_compare_class = 'woo_bt_view_compare_link';
				if ($woo_compare_product_page_view_compare_style['product_view_compare_button_type'] == 'button') {
					$product_view_compare_custom_class = '';
					$product_view_compare_text = $woo_compare_product_page_view_compare_style['product_view_compare_button_text'];
					$product_view_compare_class = 'woo_bt_view_compare_button';
				}
				$product_compare_page = get_permalink($product_compare_id);
				if ($woo_compare_comparison_page_global_settings['open_compare_type'] != 'new_page') {
					$product_compare_page = '#';
				}
				$view_compare_html = '<div style="clear:both;"></div><a class="woo_bt_view_compare '.$widget_compare_popup_view_button.' '.$product_view_compare_class.' '.$product_view_compare_custom_class.'" href="'.$product_compare_page.'" target="_blank" alt="" title="" style="display:none;">'.$product_view_compare_text.'</a>';
			}
			
			$html .= '<div class="woo_compare_button_container"><a class="woo_bt_compare_this '.$product_compare_class.' '.$product_compare_custom_class.'" id="woo_bt_compare_this_'.$product_id.'">'.$product_compare_text.'</a>' . $view_compare_html . '<input type="hidden" id="input_woo_bt_compare_this_'.$product_id.'" name="product_compare_'.$product_id.'" value="'.$product_id.'" /></div>';
		}

		return $html;
	}

	public static function show_compare_fields($product_id='', $use_wootheme_style=true) {
		global $wp_embed;
		add_shortcode( 'embed', array( $wp_embed, 'shortcode' ) );
		
		global $post, $woo_compare_table_content_style;
		if (trim($product_id) == '') $product_id = $post->ID;
		$html = '';
		$variations_list = WC_Compare_Functions::get_variations($product_id);
		if (is_array($variations_list) && count($variations_list) > 0) {
			foreach ($variations_list as $variation_id) {
				if (WC_Compare_Functions::check_product_activate_compare($variation_id) && WC_Compare_Functions::check_product_have_cat($variation_id)) {
					$compare_category = get_post_meta( $variation_id, '_woo_compare_category', true );
					$compare_fields = WC_Compare_Categories_Fields_Data::get_results( "cat_id='".$compare_category."'", 'field_order ASC' );
					if (is_array($compare_fields) && count($compare_fields)>0) {
						$html .= '<div class="compare_product_variation"><h2>'.WC_Compare_Functions::get_variation_name($variation_id).'</h2></div>';
						if ($use_wootheme_style) 
							$html .= '<table class="compare_featured_fields shop_attributes">'; 
						else 
							$html .= '<ul class="compare_featured_fields">';
						$fixed_width = ' width="60%"';
						foreach ( $compare_fields as $attribute ) {
							$field_unit = WC_Compare_Functions::get_compare_attribute_meta( $attribute->attribute_id, 'field_unit' );
							$field_type = WC_Compare_Functions::get_compare_attribute_meta( $attribute->attribute_id, 'field_type' );
							$attribute_terms = get_terms( wc_attribute_taxonomy_name( $attribute->attribute_name ) , array( 'parent' => 0, 'hide_empty' => 0, 'hierarchical' => 0 ) );
							
							$field_value = get_post_meta( $variation_id, '_woo_compare_attribute-'.$attribute->attribute_id, true );
							if ( empty( $field_value ) ) $field_value = $woo_compare_table_content_style['empty_text'];
							
							if ( $field_unit !== false && $field_unit != '' ) $field_unit = ' <span class="compare_featured_unit">('.trim(stripslashes($field_unit)).')</span>';
							else $field_unit = '';
							
							if ( $field_type == 'wp-video' ) {
								if ( trim( $field_value ) != '' && $field_value != $woo_compare_table_content_style['empty_text'] ) {
									$field_value = '<div class="woocp_video_type_container">'.do_shortcode( '[embed]'. trim( strip_shortcodes( $field_value ) ) . '[/embed]' ) .'</div>';
								}
							} elseif ( $field_type == 'wp-audio' ) {
								if ( trim( $field_value ) != '' && $field_value != $woo_compare_table_content_style['empty_text'] ) {
									$field_value = '<div class="woocp_audio_type_container">'.do_shortcode( '[audio src="'. trim( strip_shortcodes( $field_value ) ) . '" ]' ).'</div>';
								}
							} elseif ( in_array( $field_type, array( 'radio', 'drop-down' ) ) ) {
								$have_field_value = false;
								
								if ( is_array( $attribute_terms ) && count( $attribute_terms ) > 0 ) {
									foreach ( $attribute_terms as $term ) {
										if ( $term->term_id == $field_value ) {
											$have_field_value = true;
											$field_value = $term->name;
											break;
										}
									}
								}
								
								if ( $have_field_value == false ) $field_value = $woo_compare_table_content_style['empty_text'];
								
							} elseif ( in_array( $field_type, array( 'checkbox', 'multi-select' ) ) ) {
								if ( is_serialized( $field_value ) ) $field_value = maybe_unserialize( $field_value );
								$field_values = $field_value;
								if ( ! is_array( $field_values ) ) $field_values = array();
								
								$have_field_value = false;
								
								if ( is_array( $attribute_terms ) && count( $attribute_terms ) > 0 && is_array( $field_values ) && count( $field_values ) > 0 ) {
									$field_value = '';
									$common = '';
									foreach ( $attribute_terms as $term ) {
										if ( in_array( $term->term_id, $field_values ) ) {
											$have_field_value = true;
											$field_value .= $common . $term->name;
											$common = ', ';
										}
									}
								}
								
								if ( $have_field_value == false ) $field_value = $woo_compare_table_content_style['empty_text'];
								
							} else {
								if ( trim( $field_value ) == '' ) $field_value = $woo_compare_table_content_style['empty_text'];
							}
					
							if ($use_wootheme_style) 
								$html .= '<tr><th><span class="compare_featured_name">'.stripslashes( $attribute->attribute_label ).'</span>'.$field_unit.'</th><td '.$fixed_width.'><div class="compare_featured_value">'. $field_value .'</div></td></tr>';
							else
								$html .= '<li class="compare_featured_item"><span class="compare_featured_name"><strong>'.stripslashes( $attribute->attribute_label ).'</strong>'.$field_unit.'</span> : <span class="compare_featured_value">'. $field_value .'</span></li>';
							$fixed_width = '';
						}
						if ($use_wootheme_style) 
							$html .= '</table>';
						else 
							$html .= '</ul>';
					}
				}
			}
		}elseif (WC_Compare_Functions::check_product_activate_compare($product_id) && WC_Compare_Functions::check_product_have_cat($product_id)) {
			$compare_category = get_post_meta( $product_id, '_woo_compare_category', true );
			$compare_fields = WC_Compare_Categories_Fields_Data::get_results( "cat_id='".$compare_category."'", 'field_order ASC' );
			if (is_array($compare_fields) && count($compare_fields)>0) {
				if ($use_wootheme_style) 
					$html .= '<table class="compare_featured_fields shop_attributes">'; 
				else 
					$html .= '<ul class="compare_featured_fields">';
				$fixed_width = ' width="60%"';
				foreach ($compare_fields as $attribute) {
					$field_unit = WC_Compare_Functions::get_compare_attribute_meta( $attribute->attribute_id, 'field_unit' );
					$field_type = WC_Compare_Functions::get_compare_attribute_meta( $attribute->attribute_id, 'field_type' );
					$attribute_terms = get_terms( wc_attribute_taxonomy_name( $attribute->attribute_name ) , array( 'parent' => 0, 'hide_empty' => 0, 'hierarchical' => 0 ) );
							
					$field_value = get_post_meta( $product_id, '_woo_compare_attribute-'.$attribute->attribute_id, true );
					if ( empty( $field_value ) ) $field_value = $woo_compare_table_content_style['empty_text'];
					
					if ( $field_unit !== false && $field_unit != '' ) $field_unit = ' <span class="compare_featured_unit">('.trim(stripslashes($field_unit)).')</span>';
					else $field_unit = '';
					
					if ( $field_type == 'wp-video' ) {
						if ( trim( $field_value ) != '' && $field_value != $woo_compare_table_content_style['empty_text'] ) {
							$field_value = '<div class="woocp_video_type_container">'.do_shortcode( '[embed]'. trim( strip_shortcodes( $field_value ) ) . '[/embed]' ) .'</div>';
						}
					} elseif ( $field_type == 'wp-audio' ) {
						if ( trim( $field_value ) != '' && $field_value != $woo_compare_table_content_style['empty_text'] ) {
							$field_value = '<div class="woocp_audio_type_container">'.do_shortcode( '[audio src="'. trim( strip_shortcodes( $field_value ) ) . '" ]' ).'</div>';
						}
					} elseif ( in_array( $field_type, array( 'radio', 'drop-down' ) ) ) {
						$have_field_value = false;
						
						if ( is_array( $attribute_terms ) && count( $attribute_terms ) > 0 ) {
							foreach ( $attribute_terms as $term ) {
								if ( $term->term_id == $field_value ) {
									$have_field_value = true;
									$field_value = $term->name;
									break;
								}
							}
						}
						
						if ( $have_field_value == false ) $field_value = $woo_compare_table_content_style['empty_text'];
						
					} elseif ( in_array( $field_type, array( 'checkbox', 'multi-select' ) ) ) {
						if ( is_serialized( $field_value ) ) $field_value = maybe_unserialize( $field_value );
						$field_values = $field_value;
						if ( ! is_array( $field_values ) ) $field_values = array();
						
						$have_field_value = false;
						
						if ( is_array( $attribute_terms ) && count( $attribute_terms ) > 0 && is_array( $field_values ) && count( $field_values ) > 0 ) {
							$field_value = '';
							$common = '';
							foreach ( $attribute_terms as $term ) {
								if ( in_array( $term->term_id, $field_values ) ) {
									$have_field_value = true;
									$field_value .= $common . $term->name;
									$common = ', ';
								}
							}
						}
						
						if ( $have_field_value == false ) $field_value = $woo_compare_table_content_style['empty_text'];
						
					} else {
						if ( trim( $field_value ) == '' ) $field_value = $woo_compare_table_content_style['empty_text'];
					}
							
					if ($use_wootheme_style) 
						$html .= '<tr><th><span class="compare_featured_name">'.stripslashes( $attribute->attribute_label ).'</span>'.$field_unit.'</th><td '.$fixed_width.'><div class="compare_featured_value">'. $field_value .'</div></td></tr>';
					else
						$html .= '<li class="compare_featured_item"><span class="compare_featured_name"><strong>'.stripslashes( $attribute->attribute_label ).'</strong>'.$field_unit.'</span> : <span class="compare_featured_value">'. $field_value .'</span></li>';
					$fixed_width = '';
				}
				if ($use_wootheme_style) 
					$html .= '</table>';
				else 
					$html .= '</ul>';
			}
		}
		return $html;
	}

	public static function woocp_variable_ajax_add_to_cart() {
		check_ajax_referer( 'woocp-add-to-cart', 'security' );

		// Get product ID to add and quantity
		$variation_id   = (int) $_REQUEST['product_id'];
		$mypost = get_post($variation_id);
		$product_id   = (int) apply_filters('woocommerce_add_to_cart_product_id', $mypost->post_parent);
		$quantity    = (isset($_REQUEST['quantity'])) ? (int) $_REQUEST['quantity'] : 1;
		$attributes   = (array) maybe_unserialize(get_post_meta($product_id, '_product_attributes', true));
		$variations   = array();

		$my_variation = new WC_Product_Variation($variation_id, $product_id);
		$variation_data = $my_variation->variation_data;

		// Add to cart validation
		$passed_validation  = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);

		if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation_data)) {
			// Return html fragments
			$data = apply_filters('add_to_cart_fragments', array());
		} else {
			$data = array(
				'error' => true,
				'product_url' => get_permalink( $product_id )
			);
		}

		echo json_encode( $data );
		die();
	}

	public static function woocp_add_to_compare() {
		check_ajax_referer( 'woocp-compare-events', 'security' );

		$product_id  = $_REQUEST['product_id'];
		WC_Compare_Functions::add_product_to_compare_list($product_id);

		die();
	}

	public static function woocp_remove_from_popup_compare() {
		check_ajax_referer( 'woocp-compare-events', 'security' );

		$product_id  = $_REQUEST['product_id'];
		WC_Compare_Functions::delete_product_on_compare_list($product_id);

		die();
	}
	
	public static function woocp_update_compare_popup() {
		check_ajax_referer( 'woocp-compare-events', 'security' );
		global $post;
		global $product_compare_id;
		if ( $product_compare_id < 1 || $product_compare_id == NULL ) $product_compare_id = get_option('product_compare_id'); 
		$post = get_post( $product_compare_id );
		
		$result = WC_Compare_Functions::get_compare_list_html_popup();
		$result .= '<script src="'. WOOCP_JS_URL.'/fixedcolumntable/fixedcolumntable.js"></script>';
		echo json_encode( $result );
		die();
	}

	public static function woocp_update_compare_widget() {
		//check_ajax_referer( 'woocp-compare-events', 'security' );
		$result = WC_Compare_Functions::get_compare_list_html_widget();
		echo json_encode( $result );
		die();
	}

	public static function woocp_update_total_compare() {
		//check_ajax_referer( 'woocp-compare-events', 'security' );
		$result = WC_Compare_Functions::get_total_compare_list();
		echo json_encode( $result );
		die();
	}

	public static function woocp_remove_from_compare() {
		check_ajax_referer( 'woocp-compare-events', 'security' );
		$product_id  = $_REQUEST['product_id'];
		WC_Compare_Functions::delete_product_on_compare_list($product_id);
		die();
	}

	public static function woocp_clear_compare() {
		check_ajax_referer( 'woocp-compare-events', 'security' );
		WC_Compare_Functions::clear_compare_list();
		die();
	}

	public static function woocp_footer_script() {
		global $product_compare_id;
		global $woo_compare_comparison_page_global_settings;
		$woocp_compare_events = wp_create_nonce("woocp-compare-events");
		$woocp_compare_popup = wp_create_nonce("woocp-compare-popup");

		$script_add_on = '';
		$script_add_on .= '<script type="text/javascript">
				jQuery(document).ready(function($) {
						var ajax_url = "'.admin_url( 'admin-ajax.php', 'relative' ).'";
						woo_compare_widget_load();';

			$script_add_on .= '
						$(document).on("click", ".woo_compare_popup_button_go, .woo_bt_view_compare_popup", function (event){
							var compare_url = "'.get_permalink($product_compare_id).'";
							window.open(compare_url, "'.__('Product_Comparison', 'woo_cp').'", "scrollbars=1, width=980, height=650");
							event.preventDefault();
							return false;
					 
					  });';

		$script_add_on .= '
						$(document).on("click", ".woo_bt_compare_this", function(){
							var woo_bt_compare_current = $(this);
							var product_id = $("#input_"+$(this).attr("id")).val();
							$(".woo_compare_widget_loader").show();
							$(".woo_compare_widget_container").html("");
							var data = {
								action: 		"woocp_add_to_compare",
								product_id: 	product_id,
								security: 		"'.$woocp_compare_events.'"
							};
							$.post( ajax_url, data, function(response) {
								//woo_bt_compare_current.siblings(".woo_add_compare_success").show();
								woo_bt_compare_current.addClass("compared");
								woo_bt_compare_current.siblings(".woo_bt_view_compare").show();
								//setTimeout(function(){
								//	woo_bt_compare_current.siblings(".woo_add_compare_success").hide();
								//}, 3000);
								data = {
									action: 		"woocp_update_compare_widget",
									security: 		"'.$woocp_compare_events.'"
								};
								$.post( ajax_url, data, function(response) {
									result = $.parseJSON( response );
									$(".woo_compare_widget_loader").hide();
									$(".woo_compare_widget_container").html(result);
								});
								$("body").trigger("woo_add_product_to_compare_list", [product_id]);
								woo_update_total_compare_list();
							});
						});

						$(document).on("click", ".woo_compare_remove_product", function(){
							var remove_product_id = $(this).attr("rel");
							$(".woo_compare_widget_loader").show();
							$(".woo_compare_widget_container").html("");
							var data = {
								action: 		"woocp_remove_from_compare",
								product_id: 	remove_product_id,
								security: 		"'.$woocp_compare_events.'"
							};
							$.post( ajax_url, data, function(response) {
								data = {
									action: 		"woocp_update_compare_widget",
									security: 		"'.$woocp_compare_events.'"
								};
								$.post( ajax_url, data, function(response) {
									result = $.parseJSON( response );
									$(".woo_compare_widget_loader").hide();
									$(".woo_compare_widget_container").html(result);
								});
								$("body").trigger("woo_remove_product_from_compare_list", [remove_product_id]);
								woo_update_total_compare_list();
							});
						});
						$(document).on("click", ".woo_compare_clear_all", function(){
							$(".woo_compare_widget_loader").show();
							$(".woo_compare_widget_container").html("");
							var data = {
								action: 		"woocp_clear_compare",
								security: 		"'.$woocp_compare_events.'"
							};
							$.post( ajax_url, data, function(response) {
								data = {
									action: 		"woocp_update_compare_widget",
									security: 		"'.$woocp_compare_events.'"
								};
								$.post( ajax_url, data, function(response) {
									result = $.parseJSON( response );
									$(".woo_compare_widget_loader").hide();
									$(".woo_compare_widget_container").html(result);
								});
								$("body").trigger("woo_clear_compare_list");
								woo_update_total_compare_list();
							});
						});

						function woo_update_total_compare_list(){
							var data = {
								action: 		"woocp_update_total_compare",
								security: 		"'.$woocp_compare_events.'"
							};
							$.post( ajax_url, data, function(response) {
								total_compare = $.parseJSON( response );
								$("#total_compare_product").html(total_compare);
								$("body").trigger("woo_update_total_compare_list");
							});
						}
						
						function woo_compare_widget_load() {
							$(".woo_compare_widget_loader").show();
							$(".woo_compare_widget_container").html("");
							var data = {
								action: 		"woocp_update_compare_widget",
								security: 		"'.$woocp_compare_events.'"
							};
							$.post( ajax_url, data, function(response) {
								result = $.parseJSON( response );
								$(".woo_compare_widget_loader").hide();
								$(".woo_compare_widget_container").html(result);
							});
							$("body").trigger("woo_compare_widget_load");
							woo_update_total_compare_list();
						}

					});
				</script>';
		echo $script_add_on;
	}

	public static function woocp_variable_add_to_cart_script() {
		$woocp_add_to_cart_nonce = wp_create_nonce("woocp-add-to-cart");
		$script_add_on = '';
		$script_add_on .= '<script type="text/javascript">
				(function($){
					$(function(){
						if (woocommerce_params.option_ajax_add_to_cart=="yes") {

							// Ajax add to cart
							$(document).on("click", ".add_to_cart_button", function() {

								// AJAX add to cart request
								var $thisbutton = $(this);

								if ($thisbutton.is(".product_type_variation")) {
									if (!$thisbutton.attr("data-product_id")) return true;

									$thisbutton.removeClass("added");
									$thisbutton.addClass("loading");

									var data = {
										action: 		"woocp_variable_add_to_cart",
										product_id: 	$thisbutton.attr("data-product_id"),
										security: 		"'.$woocp_add_to_cart_nonce.'"
									};

									// Trigger event
									$("body").trigger("adding_to_cart");

									// Ajax action
									$.post( woocommerce_params.ajax_url, data, function(response) {

										$thisbutton.removeClass("loading");

										// Get response
										data = $.parseJSON( response );

										if (data.error && data.product_url) {
											window.location = data.product_url;
											return;
										}

										fragments = data;

										// Block fragments class
										if (fragments) {
											$.each(fragments, function(key, value) {
												$(key).addClass("updating");
											});
										}

										// Block widgets and fragments
										$(".widget_shopping_cart, .shop_table.cart, .updating, .cart_totals").fadeTo("400", "0.6").block({message: null, overlayCSS: {background: "transparent url(" + woocommerce_params.plugin_url + "/assets/images/ajax-loader.gif) no-repeat center", opacity: 0.6}});

										// Changes button classes
										$thisbutton.addClass("added");

										// Cart widget load
										if ($(".widget_shopping_cart").size()>0) {
											$(".widget_shopping_cart:eq(0)").load( window.location + " .widget_shopping_cart:eq(0) > *", function() {

												// Replace fragments
												if (fragments) {
													$.each(fragments, function(key, value) {
														$(key).replaceWith(value);
													});
												}

												// Unblock
												$(".widget_shopping_cart, .updating").css("opacity", "1").unblock();

												$("body").trigger("cart_widget_refreshed");
											} );
										} else {
											// Replace fragments
											if (fragments) {
												$.each(fragments, function(key, value) {
													$(key).replaceWith(value);
												});
											}

											// Unblock
											$(".widget_shopping_cart, .updating").css("opacity", "1").unblock();
										}

										// Cart page elements
										$(".shop_table.cart").load( window.location + " .shop_table.cart:eq(0) > *", function() {

											$("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").addClass("buttons_added").append("<input type=\"button\" value=\"+\" id=\"add1\" class=\"plus\" />").prepend("<input type=\"button\" value=\"-\" id=\"minus1\" class=\"minus\" />");

											$(".shop_table.cart").css("opacity", "1").unblock();

											$("body").trigger("cart_page_refreshed");
										});

										$(".cart_totals").load( window.location + " .cart_totals:eq(0) > *", function() {
											$(".cart_totals").css("opacity", "1").unblock();
										});

										// Trigger event so themes can refresh other areas
										$("body").trigger("added_to_cart");

									});

									return false;

								} else {
									return true;
								}

							});
						}
					});
				})(jQuery);
				</script>';
		echo $script_add_on;
	}

	public static function woocp_product_featured_tab() {
		global $post;
		global $woo_compare_product_page_tab;
		$compare_featured_tab = trim($woo_compare_product_page_tab['compare_featured_tab']);
		if ($compare_featured_tab == '') $compare_featured_tab = __('Technical Details', 'woo_cp');

		$show_compare_featured_tab = false;
		$product_id = $post->ID;
		$variations_list = WC_Compare_Functions::get_variations($product_id);
		if (is_array($variations_list) && count($variations_list) > 0) {
			foreach ($variations_list as $variation_id) {
				if (WC_Compare_Functions::check_product_activate_compare($variation_id) && WC_Compare_Functions::check_product_have_cat($variation_id)) {
					$compare_category = get_post_meta( $variation_id, '_woo_compare_category', true );
					$compare_fields = WC_Compare_Categories_Fields_Data::get_results("cat_id='".$compare_category."'", 'field_order ASC');
					if (is_array($compare_fields) && count($compare_fields)>0) {
						$show_compare_featured_tab = true;
						break;
					}
				}
			}
		} elseif ( WC_Compare_Functions::check_product_activate_compare($product_id) && WC_Compare_Functions::check_product_have_cat($product_id) ) {
			$compare_category = get_post_meta( $product_id, '_woo_compare_category', true );
			$compare_fields = WC_Compare_Categories_Fields_Data::get_results("cat_id='".$compare_category."'", 'field_order ASC');
			if (is_array($compare_fields) && count($compare_fields)>0) {
				$show_compare_featured_tab = true;
			}
		}

		if ($show_compare_featured_tab) echo '<li><a href="#tab-compare-featured">'.esc_attr( stripslashes( $compare_featured_tab ) ).'</a></li>';
	}
	
	public static function woocp_product_featured_tab_woo_2_0( $tabs = array() ) {
		global $product, $post;
		global $woo_compare_product_page_tab;
		
		$compare_featured_tab = trim($woo_compare_product_page_tab['compare_featured_tab']);
		if ($compare_featured_tab == '') $compare_featured_tab = __('Technical Details', 'woo_cp');

		$show_compare_featured_tab = false;
		$product_id = $post->ID;
		$variations_list = WC_Compare_Functions::get_variations($product_id);
		if (is_array($variations_list) && count($variations_list) > 0) {
			foreach ($variations_list as $variation_id) {
				if (WC_Compare_Functions::check_product_activate_compare($variation_id) && WC_Compare_Functions::check_product_have_cat($variation_id)) {
					$compare_category = get_post_meta( $variation_id, '_woo_compare_category', true );
					$compare_fields = WC_Compare_Categories_Fields_Data::get_results("cat_id='".$compare_category."'", 'field_order ASC');
					if (is_array($compare_fields) && count($compare_fields)>0) {
						$show_compare_featured_tab = true;
						break;
					}
				}
			}
		}elseif (WC_Compare_Functions::check_product_activate_compare($product_id) && WC_Compare_Functions::check_product_have_cat($product_id)) {
			$compare_category = get_post_meta( $product_id, '_woo_compare_category', true );
			$compare_fields = WC_Compare_Categories_Fields_Data::get_results("cat_id='".$compare_category."'", 'field_order ASC');
			if (is_array($compare_fields) && count($compare_fields)>0) {
				$show_compare_featured_tab = true;
			}
		}

		if ($show_compare_featured_tab) {
		
			$tabs['compare-featured'] = array(
				'title'    => esc_attr( stripslashes( $compare_featured_tab ) ),
				'priority' => $woo_compare_product_page_tab['auto_compare_featured_tab'],
				'callback' => array('WC_Compare_Hook_Filter', 'woocp_product_featured_panel_woo_2_0')
			);
		}
		
		return $tabs;
	}

	public static function woocp_product_featured_panel() {
		global $post;
?>
		<div class="panel entry-content" id="tab-compare-featured">
			<?php echo WC_Compare_Hook_Filter::show_compare_fields($post->ID); ?>
		</div>
        <?php
	}
	
	public static function woocp_product_featured_panel_woo_2_0() {
		global $post;
		echo WC_Compare_Hook_Filter::show_compare_fields($post->ID);
	}
	
	public static function woocp_set_selected_attributes($default_attributes) {
		if (isset($_REQUEST['variation_selected']) && $_REQUEST['variation_selected'] > 0) {
			$variation_id = $_REQUEST['variation_selected'];
			$mypost = get_post($variation_id);
			if ($mypost != NULL && $mypost->post_type == 'product_variation') {
				$attributes = (array) maybe_unserialize(get_post_meta($mypost->post_parent, '_product_attributes', true));
				$my_variation = new WC_Product_Variation($variation_id, $mypost->post_parent);
				$variation_data = $my_variation->variation_data;
				if (is_array($attributes) && count($attributes) > 0) {
					foreach ($attributes as $attribute) {
						if ( !$attribute['is_variation'] ) continue;
						$taxonomy = 'attribute_' . sanitize_title($attribute['name']);
						if (isset($variation_data[$taxonomy])) {
							$default_attributes[sanitize_title($attribute['name'])] = $variation_data[$taxonomy];							
						}
					}
				}
			}
		}
		return $default_attributes;
	}
			
	public static function woocp_add_wp_media_lib_scripts() {
		$site_url = str_replace( 'http:', '', str_replace( 'https:', '', get_option('siteurl') ) );
		?>
        <link rel="stylesheet" href="<?php echo $site_url; ?>/wp-includes/js/mediaelement/mediaelementplayer.min.css">
		<link rel="stylesheet" href="<?php echo $site_url; ?>/wp-includes/js/mediaelement/wp-mediaelement.css">
        <?php
	}
	
	public static function a3_wp_admin() {
		wp_enqueue_style( 'a3rev-wp-admin-style', WOOCP_CSS_URL . '/a3_wp_admin.css' );
	}
	
	public static function admin_sidebar_menu_css() {
		wp_enqueue_style( 'a3rev-wc-cp-admin-sidebar-menu-style', WOOCP_CSS_URL . '/admin_sidebar_menu.css' );
	}
	
}
?>
