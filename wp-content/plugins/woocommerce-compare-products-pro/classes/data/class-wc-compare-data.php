<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * WooCommerce Compare Meta Data
 *
 * Table Of Contents
 *
 * install_database()
 */
class WC_Compare_Data 
{
	public static function install_database() {
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if( ! empty($wpdb->charset ) ) $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			if( ! empty($wpdb->collate ) ) $collate .= " COLLATE $wpdb->collate";
		}
		
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		
		$table_woo_compare_categorymeta = $wpdb->prefix. "woo_compare_categorymeta";
		$table_woo_compare_attributemeta = $wpdb->prefix. "woo_compare_attributemeta";
		
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_woo_compare_categorymeta'") != $table_woo_compare_categorymeta) {
			$sql = "CREATE TABLE IF NOT EXISTS `{$table_woo_compare_categorymeta}` (
				  meta_id bigint(20) NOT NULL auto_increment,
				  woo_compare_category_id bigint(20) NOT NULL,
				  meta_key varchar(255) NULL,
				  meta_value longtext NULL,
				  PRIMARY KEY  (meta_id),
				  KEY woo_compare_category_id (woo_compare_category_id),
				  KEY meta_key (meta_key)
				) $collate; ";
			
			dbDelta($sql);
		}
		
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_woo_compare_attributemeta'") != $table_woo_compare_attributemeta) {
			$sql = "CREATE TABLE IF NOT EXISTS `{$table_woo_compare_attributemeta}` (
				  meta_id bigint(20) NOT NULL auto_increment,
				  woo_compare_attribute_id bigint(20) NOT NULL,
				  meta_key varchar(255) NULL,
				  meta_value longtext NULL,
				  PRIMARY KEY  (meta_id),
				  KEY woo_compare_attribute_id (woo_compare_attribute_id),
				  KEY meta_key (meta_key)
				) $collate; ";
			
			dbDelta($sql);
		}
	}
}
?>
