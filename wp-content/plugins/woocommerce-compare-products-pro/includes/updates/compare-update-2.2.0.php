<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( get_option( 'a3rev_woocp_is_updating_2_2_0' ) ) return;

update_option( 'a3rev_woocp_is_updating_2_2_0', true );

@set_time_limit(10800);
@ini_set( "memory_limit" , "512M" );
@ini_set('max_execution_time',10800);

WC_Compare_Data::install_database();

global $wpdb;
$sql = "ALTER TABLE " . $wpdb->prefix . "woo_compare_cat_fields CHANGE `cat_id` `cat_id` BIGINT( 20 ) NOT NULL ;";
$wpdb->query( $sql );

$sql = "ALTER TABLE " . $wpdb->prefix . "woo_compare_cat_fields CHANGE `field_id` `field_id` BIGINT( 20 ) NOT NULL ;";
$wpdb->query( $sql );

$sql = "ALTER TABLE " . $wpdb->prefix . "woo_compare_cat_fields ADD `old_field_id` BIGINT NOT NULL AFTER `field_id` ;";
$wpdb->query( $sql );

$sql = "UPDATE " . $wpdb->prefix . "woo_compare_cat_fields SET `old_field_id` = `field_id` ;";
$wpdb->query( $sql );

/**
 * Convert old Compare Category to Product Category
 *
 */
$sql = "SELECT * FROM " . $wpdb->prefix . "woo_compare_categories ORDER BY category_order ASC";
$old_categories = $wpdb->get_results( $sql );
if ( $old_categories && is_array( $old_categories ) && count( $old_categories ) > 0 ) {
	foreach ( $old_categories as $compare_category ) {
		$have_cat = get_term_by( 'name', $compare_category->category_name, 'product_cat' );
		$new_cat_id = 0;
		if ( ! $have_cat ) {
			$category = wp_insert_term( $compare_category->category_name, 'product_cat' );
			if ( is_array( $category ) && isset( $category['term_id'] ) ) $new_cat_id = (int) $category['term_id'];
		} else {
			$new_cat_id = (int) $have_cat->term_id;
		}
		
		if ( $new_cat_id > 0 ) {
			WC_Compare_Functions::update_compare_category_meta( $new_cat_id, 'is_compare_cat', 'yes' );
			
			// Update all products to Product category id
			$sql = "UPDATE " . $wpdb->postmeta . " SET meta_value = '" . $new_cat_id . "' WHERE meta_key = '_woo_compare_category' AND meta_value = '" . $compare_category->id . "' ";
			$wpdb->query( $sql );
			
			// Update all features that has select Compare Category to Product Category Id
			$sql = "UPDATE " . $wpdb->prefix . "woo_compare_cat_fields SET cat_id = '" . $new_cat_id . "' WHERE cat_id = '" . $compare_category->id . "' ";
			$wpdb->query( $sql );
		}
	}
}

/**
 * Convert old Compare Attributes to Product Attributes & Terms
 *
 */
$sql = "SELECT * FROM " . $wpdb->prefix . "woo_compare_fields ORDER BY field_order ASC";
$old_features = $wpdb->get_results( $sql );
if ( $old_features && is_array( $old_features ) && count( $old_features ) > 0 ) {
	foreach ( $old_features as $feature ) {
		$attribute_type = 'select';
		$have_terms = true;
		$compare_field_option = array();
		
		if ( in_array( $feature->field_type, array( 'input-text', 'text-area', 'wp-video', 'wp-audio' ) ) ) {
			$attribute_type = 'text';
			$have_terms = false;
		}
		
		$sql = "SELECT COUNT(*) FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_label = '" . $feature->field_name . "' ";
		$have_attribute = $wpdb->get_var( $sql );
		
		// if attribute is not existed then create new Product attribute and terms for that attribute
		if ( $have_attribute < 1 ) {
			
			// Create new Product Attribute
			$attribute_slug = 'compare-' . sanitize_title( $feature->field_name );
			$wpdb->query( $wpdb->prepare( "
					INSERT INTO {$wpdb->prefix}woocommerce_attribute_taxonomies ( attribute_name, attribute_label, attribute_type, attribute_orderby )
					VALUES ( %s, %s, %s, 'menu_order' );
				", $attribute_slug, $feature->field_name, $attribute_type ) );
			
			$new_attribute_id =	$wpdb->insert_id;
			WC_Compare_Functions::update_compare_attribute_meta( $new_attribute_id, 'field_unit', $feature->field_unit );
			WC_Compare_Functions::update_compare_attribute_meta( $new_attribute_id, 'field_type', $feature->field_type );
			
			// Create terms for this attribute if it has terms
			if ( $have_terms ) {
				$taxonomy = 'pa_' . $attribute_slug;
				$default_value = nl2br( $feature->default_value );
				$field_option = explode( '<br />', $default_value );
				if ( is_array( $field_option ) && count( $field_option ) > 0 ) {
					foreach ( $field_option as $option_value ) {
						$att_term_id = 0;
						$option_value = trim( stripslashes( $option_value ) );
						if ( $option_value == '' ) continue;
						
						$sql = "SELECT * FROM " . $wpdb->prefix . "terms WHERE name LIKE '".$option_value."' LIMIT 0,1 ";
						$att_term = $wpdb->get_row( $sql );
						if ( $att_term ) {
							$att_term_id = (int) $att_term->term_id;
						} else {
							$wpdb->query( $wpdb->prepare( "
								INSERT INTO {$wpdb->prefix}terms ( name, slug )
								VALUES ( %s, %s );
							", $option_value, 'compare-' . sanitize_title( $option_value ) ) );
							
							$att_term_id =	$wpdb->insert_id;
						}
						
						$wpdb->query( "
							INSERT INTO {$wpdb->prefix}term_taxonomy ( term_id, taxonomy )
							VALUES ( '".$att_term_id."', '".$taxonomy."' );
						" );
						
						// use to compare and update for value on product meta
						if ( $att_term_id > 0 ) $compare_field_option[$att_term_id] = $option_value;
					}
				} else {
					$have_terms = false;	
				}
			}
		
		// If attribute is existed then just check to create terms for that attribute	
		} else {
			$sql = "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_label = '" . $feature->field_name . "' LIMIT 0,1 ";
			$attribute_data = $wpdb->get_row( $sql );
			
			$new_attribute_id = $attribute_data->attribute_id;
			WC_Compare_Functions::update_compare_attribute_meta( $new_attribute_id, 'field_unit', $feature->field_unit );
			WC_Compare_Functions::update_compare_attribute_meta( $new_attribute_id, 'field_type', $feature->field_type );
			
			// Create terms for this attribute if it has terms
			if ( $have_terms ) {
				$taxonomy = 'pa_' . $attribute_data->attribute_name;
				$default_value = nl2br( $feature->default_value );
				$field_option = explode( '<br />', $default_value );
				if ( is_array( $field_option ) && count( $field_option ) > 0 ) {
					foreach ( $field_option as $option_value ) {
						$att_term_id = 0;
						$option_value = trim( stripslashes( $option_value ) );
						if ( $option_value == '' ) continue;
						
						$have_attr_term = get_term_by( 'name', $option_value, $taxonomy );
						if ( ! $have_attr_term ) {
							$att_term = wp_insert_term( $option_value, $taxonomy );
							if ( is_array( $att_term ) && isset( $att_term['term_id'] ) ) $att_term_id = (int) $att_term['term_id'];
						} else {
							$att_term_id = (int) $have_attr_term->term_id;
						}
						
						// use to compare and update for value on product meta
						if ( $att_term_id > 0 ) $compare_field_option[$att_term_id] = $option_value;
					}
				} else {
					$have_terms = false;	
				}
			}
			
		}
		
		// Update all feature id to new attribute id
		$sql = "UPDATE " . $wpdb->prefix . "woo_compare_cat_fields SET field_id=" . $new_attribute_id . " WHERE old_field_id=" . $feature->id . " ; ";
		$wpdb->query( $sql );
		
		// Update product meta_key to new key
		$sql = "UPDATE " . $wpdb->postmeta . " SET meta_key = '_woo_compare_attribute-" . $new_attribute_id . "' WHERE meta_key = '_woo_compare_" . $feature->field_key . "'; ";
		$wpdb->query( $sql );
		
		// Update product meta_value for attributes has terms
		if ( $have_terms && is_array( $compare_field_option ) && count( $compare_field_option ) > 0 ) {
			$sql = "SELECT DISTINCT post_id FROM " . $wpdb->postmeta . " WHERE meta_key = '_woo_compare_attribute-" . $new_attribute_id . "' ";
			$all_product_ids = $wpdb->get_results( $sql );
			if ( $all_product_ids && is_array( $all_product_ids ) && count( $all_product_ids ) > 0 ) {
				foreach ( $all_product_ids as $my_product ) {
					$product_id = (int) $my_product->post_id;
					$have_update_meta = false;
					
					$field_value = get_post_meta( $product_id, '_woo_compare_attribute-' . $new_attribute_id, true );
					if ( is_serialized( $field_value ) ) $field_value = maybe_unserialize( $field_value );
					if ( ! is_array( $field_value ) || count( $field_value ) < 1 ) {
						$key = array_search( (string) $field_value, $compare_field_option );
						
						if ( $key !== FALSE ) {
							$have_update_meta = true;
							$new_field_value = $key;
						} else {
							$new_field_value = $option_value;
						}
							
					} else {
						$new_field_value = array();
						foreach ( $field_value as $option_value ) {
							$option_value = trim(stripslashes($option_value));
							$key = array_search( $option_value, $compare_field_option );
							
							if ( $key !== FALSE ) {
								$have_update_meta = true;
								$new_field_value[] = $key;
							} else {
								$new_field_value = $option_value;
							}
						}
					}
					
					if ( $have_update_meta ) {
						update_post_meta( $product_id, '_woo_compare_attribute-'.$new_attribute_id, $new_field_value );
					}
				}
			}
		}
		
	}	
}

delete_transient( 'wc_attribute_taxonomies' );

// Delete compare product meta
//$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = '_woo_compare_category_name';");
//$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_woo_compare_field-%';");

//$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'woo_compare_fields');
//$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'woo_compare_categories');