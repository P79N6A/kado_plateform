<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * WooCommerce Compare Features Panel
 *
 * Table Of Contents
 *
 * admin_screen()
 */
class WC_Compare_Features_Panel 
{
	
	public static function admin_screen () {
		?>
	<style>
		.chosen-container{margin-right:2px;}
		.field_title{width:205px; padding:0 8px 0 10px; float:left;}
		.help_tip{cursor: help;line-height: 1;margin: -4px 0 0 5px;padding: 0;vertical-align: middle;}
		.compare_set_1{width:46%; float:left; margin-right:5%; margin-bottom:15px;}
		.compare_set_2{width:46%; float:left; margin-bottom:15px;}
		.ui-state-highlight{background:#F6F6F6; height:24px; padding:8px 0 0; border:1px dotted #DDD; margin-bottom:20px;}
		ul.compare_orders{float:left; margin:0; width:100%}
		ul.compare_orders li{padding-top:8px; border-top:1px solid #DFDFDF; margin:5px 0; line-height:20px;}
		ul.compare_orders li.first_record{border-top:none; padding-top:0;}
		ul.compare_orders .compare_sort{float:left; width:60px;}
		.c_field_name{padding-left:20px; background:url(<?php echo WOOCP_IMAGES_URL; ?>/icon_sort.png) no-repeat 0 center;}
		.c_openclose_table{cursor:pointer;}
		.c_openclose_none{width:16px; height:16px; display:inline-block;}
		.c_close_table{background:url(<?php echo get_option('siteurl'); ?>/wp-admin/images/arrows.png) no-repeat center 0px; width:16px; height:16px; display:inline-block;}
		.c_open_table{background:url(<?php echo get_option('siteurl'); ?>/wp-admin/images/arrows.png) no-repeat center -35px; width:16px; height:16px; display:inline-block;}
		ul.compare_orders .c_field_type{width:120px; float:left;}
		ul.compare_orders .c_field_manager{background:url(<?php echo WOOCP_IMAGES_URL; ?>/icon_fields.png) no-repeat 0 0; width:16px; height:16px; display:inline-block;}
		.tablenav-pages{float:right;}
		.c_field_edit, .c_field_delete{cursor:pointer;}
		.widefat th input {
			vertical-align:middle;
			padding:3px 8px;
			margin:auto;
		}
		.widefat th, .widefat td {
			overflow: inherit !important;	
		}
		.chosen-container-multi .chosen-choices {
			min-height:100px;	
		}

		ul.feature_compare_orders .compare_sort{margin-right:10px; float:none; width:auto;}
		ul.feature_compare_orders .c_field_name{margin-right:10px;padding:5px 0 5px 20px; float:none; width:auto;}
		ul.feature_compare_orders .c_field_action{float:right;}
		ul.feature_compare_orders .c_field_type{float:right; margin-right:10px; width:70px;}
		
		.icon32-compare-product {
			background:url(<?php echo WOOCP_IMAGES_URL; ?>/a3-plugins.png) no-repeat left top !important;
		}
		@media screen and ( max-width: 782px ) {
			.a3rev_manager_panel_container table {
				width:100% !important;	
			}
			.a3rev_manager_panel_container td.search_features_td {
				text-align:left !important;	
			}
		}
	</style>
        <div id="htmlForm">
        <div style="clear:both"></div>
		<div class="wrap a3rev_panel_container a3rev_manager_panel_container">
        
        <?php 
			echo self::init_features_actions();
		?>
		<?php
		if ( isset( $_REQUEST['act'] ) && isset( $_REQUEST['cat_id'] ) && $_REQUEST['act'] == 'view-cat') {
			$cat_id = trim( $_REQUEST['cat_id'] );
			self::woocp_category( $cat_id );
		} else {
			self::woocp_features_orders();
		}
		?>
        </div>
        </div>
		<?php
	}
	
	public static function init_features_actions() {
		$result_msg = '';

		if ( isset($_REQUEST['event']) && $_REQUEST['event'] == 'field-remove') {
			
			if ( isset( $_REQUEST['field_id'] ) && $_REQUEST['field_id'] > 0 && isset( $_REQUEST['cat_id'] ) && $_REQUEST['cat_id'] > 0 ) {
				$field_id = trim( $_REQUEST['field_id'] );
				$cat_id = trim( $_REQUEST['cat_id'] );
				WC_Compare_Categories_Fields_Data::delete_row( "field_id='".$field_id."' AND cat_id='".$_REQUEST['cat_id']."'" );
				$result_msg = '<div class="updated" id="result_msg"><p>'.__('Compare Attribute successfully removed', 'woo_cp').'.</p></div>';
				
			}
		}
		
		return $result_msg;
	}
	
	public static function get_woocp_category( $cat_id, $expand_features = false ) {
		global $wc_admin_compare_features;
		
		if ( is_object( $cat_id ) ) $cat = $cat_id;
		else $cat = get_term( $cat_id, 'product_cat' );
		
		$compare_fields = WC_Compare_Categories_Fields_Data::get_results( "cat_id='".$cat->term_id."'", 'field_order ASC' );
		
		$openclose_table_class = 'c_close_table';
		if ( $expand_features ) $openclose_table_class = 'c_open_table';
		
		ob_start();
	?>
    	<input type="hidden" name="compare_orders_<?php echo $cat->term_id; ?>" class="compare_category_id" value="<?php echo $cat->term_id; ?>"  />
		<table cellspacing="0" class="widefat post fixed sorttable" id="compare_orders_<?php echo $cat->term_id; ?>" style="width:535px; margin-bottom:20px;">
			<thead>
				<tr>
              		<th width="25" style="white-space: nowrap;"><span class="c_field_name">&nbsp;</span></th>
                    <th><strong><?php echo stripslashes( $cat->name ) ;?></strong> :</th>
                    <th width="90"></th>
                    <th width="100" style="text-align:right; font-size:12px;white-space: nowrap;">
                    <a href="edit-tags.php?action=edit&taxonomy=product_cat&tag_ID=<?php echo $cat->term_id; ?>&post_type=product" class="c_field_edit" title="<?php _e( 'Edit', 'woo_cp' ) ?>"><?php _e( 'Edit', 'woo_cp' ) ?></a>
                    <?php if ( is_array( $compare_fields ) && count( $compare_fields ) > 0 ) { ?> | <span class="c_openclose_table <?php echo $openclose_table_class; ?>" id="expand_<?php echo $cat->term_id; ?>">&nbsp;</span><?php } else {?> | <span class="c_openclose_none">&nbsp;</span><?php } ?></th>
            	</tr>
			</thead>
            <tbody class="expand_<?php echo $cat->term_id; ?>">
               	<?php
				if ( is_array( $compare_fields ) && count( $compare_fields ) > 0 ) {
					$i= 0;
					foreach ( $compare_fields as $attribute ) {
						$field_type = WC_Compare_Functions::get_compare_attribute_meta( $attribute->attribute_id, 'field_type' );
						$remove_feature_url = add_query_arg( array( 'event' => 'field-remove', 'field_id' => $attribute->attribute_id, 'cat_id' => $cat->term_id ) );
						$i++;
?>
                <tr id="recordsArray_<?php echo $attribute->attribute_id; ?>" style=" <?php if ( ! $expand_features ) echo 'display:none'; ?>">
                	<td><span class="compare_sort"><?php echo $i; ?></span>.</td>
                    <td><div class="c_field_name"><?php echo stripslashes( $attribute->attribute_label ); ?></div></td>
                    <td align="right"><?php echo $wc_admin_compare_features->default_types[$field_type]['name']; ?></td>
                    <td align="right">
                    <a href="admin.php?page=product_attributes&edit=<?php echo $attribute->attribute_id; ?>" class="c_field_edit" title="<?php _e('Edit', 'woo_cp') ?>" ><?php _e('Edit', 'woo_cp') ?></a> | 
                    <a href="<?php echo $remove_feature_url; ?>" class="c_field_delete" onclick="javascript:return confirmation('<?php _e('Are you sure you want to remove', 'woo_cp') ; ?> #<?php echo htmlspecialchars( $attribute->attribute_label ); ?> <?php _e( 'from', 'woo_cp' ) ; ?> #<?php echo htmlspecialchars( $cat->name ); ?>');" title="<?php _e( 'Remove', 'woo_cp' ) ?>" ><?php _e('Remove', 'woo_cp') ?></a>
                    </td>
                </tr>
                <?php
					}
				}else {
					echo '<tr><td colspan="4">'.__( 'You have not assigned any Attributes to this category yet. No Hurry!', 'woo_cp' ).'</td></tr>';
				}
?>
            </tbody>
		</table>
    <?php
	
		$ouput = ob_get_clean();
		
		return $ouput;
	}
	
	public static function woocp_features_orders() {
		
		$compare_cats = WC_Compare_Functions::get_all_compare_cats();
		?>
        <h3><?php _e('Manage Compare Categories and Attributes', 'woo_cp'); ?></h3>
        <?php self::woocp_features_panel_help_box(); ?>
        <?php
		if ( is_array( $compare_cats ) && count( $compare_cats ) > 0 ) {
		?>
        <div class="updated below-h2 update_feature_order_message" style="display:none"><p></p></div>
        <div style="clear:both"></div>
        <ul style="margin:0; padding:0;" class="sorttable">
        <?php
			foreach ( $compare_cats as $cat ) {
		?>
        	<li id="recordsArray_<?php echo $cat->term_id; ?>"><?php echo self::get_woocp_category( $cat ); ?></li>
        <?php
			}
		?>
        </ul>
        <?php
			self::include_script();
		}
	}
	
	public static function woocp_category( $cat_id ) {
	?>
    	<h3><?php _e('Compare Category Attributes', 'woo_cp'); ?></h3>
        <?php self::woocp_features_panel_help_box() ; ?>
        <div class="updated below-h2 update_feature_order_message" style="display:none"><p></p></div>
        <div style="clear:both"></div>
    <?php	
		echo self::get_woocp_category( $cat_id, true );
		self::include_script();
	}
	
	public static function include_script() {
	
		wp_enqueue_script('jquery-ui-sortable');
        $woocp_update_order = wp_create_nonce("woocp-update-order");
        $woocp_update_cat_order = wp_create_nonce("woocp-update-cat-order");
    ?>
		<script type="text/javascript">
            (function($){
                $(function(){
                    $(".c_openclose_table").click( function() {
                        if ( $(this).hasClass('c_close_table') ) {
                            $(this).removeClass("c_close_table");
                            $(this).addClass("c_open_table");
                            $("tbody."+$(this).attr('id')+" tr").css('display', '');
                        } else {
                            $(this).removeClass("c_open_table");
                            $(this).addClass("c_close_table");
                            $("tbody."+$(this).attr('id')+" tr").css('display', 'none');
                        }
                    });
    
                    var fixHelper = function(e, ui) {
                        ui.children().each(function() {
                            $(this).width($(this).width());
                        });
                        return ui;
                    };
    
                    $(".sorttable tbody").sortable({ helper: fixHelper, placeholder: "ui-state-highlight", opacity: 0.8, cursor: 'move', update: function() {
							var cat_id = $(this).parent('table').siblings(".compare_category_id").val();
							var order = $(this).sortable("serialize") + '&action=woocp_update_orders&security=<?php echo $woocp_update_order; ?>&cat_id='+cat_id;
							$.post("<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", order, function(theResponse){
								$(".update_feature_order_message p").html(theResponse);
								$(".update_feature_order_message").show();
								$("#compare_orders_"+cat_id).find(".compare_sort").each(function(index){
									$(this).html(index+1);
								});
							});
                    	}
                    });
                });
            })(jQuery);
			
			function confirmation(text) {
				var answer = confirm(text)
				if (answer){
					return true;
				}else{
					return false;
				}
			}
        </script>
    <?php
	}
	
	public static function woocp_update_orders() {
		check_ajax_referer( 'woocp-update-order', 'security' );
		$updateRecordsArray  = $_REQUEST['recordsArray'];
		$cat_id = $_REQUEST['cat_id'];
		$listingCounter = 1;
		foreach ($updateRecordsArray as $recordIDValue) {
			WC_Compare_Categories_Fields_Data::update_order($cat_id, $recordIDValue, $listingCounter);
			$listingCounter++;
		}
		
		_e('You just save the order for Compare Attributes.', 'woo_cp');
		die();
	}
	
	public static function woocp_features_panel_help_box() {
		global $wc_compare_admin_init;
		
		$help_blue_message = '<div>
		<div>* '.__( 'All Product Categories that have the Comparison Feature turned ON are auto shown below.', 'woo_cp' ) .'</div>
		<div>* '.__( 'Use the dropdown arrow on each category to view all Product Attributes that have been assigned to that Product Category.', 'woo_cp' ) .'</div>
		<div>* '.__( 'Drag and drop to sort the Attributes display order in each category. This is the order that Attributes will show on the front end in the Comparison table.', 'woo_cp' ) .'</div>
		</div>
		<div style="clear:both"></div>
		<a class="help_blue_message_dontshow" style="float:left;" href="javascript:void(0);">'.__( "Don't show again", 'woo_cp' ).'</a>
		<a class="help_blue_message_dismiss" style="float:right;" href="javascript:void(0);">'.__( "Dismiss", 'woo_cp' ).'</a>
		<div style="clear:both"></div>';
		echo '<div class="help_blue_message_container">'.$wc_compare_admin_init->blue_message_box( $help_blue_message, '537px' ) .'</div>'; 
	?>
<style>
.a3rev_blue_message_box_container {
	margin-bottom:20px;
}
.a3rev_panel_container .help_blue_message_container {
<?php if ( get_option( 'wccp_help_blue_message_dontshow', 0 ) == 1 ) echo 'display: none !important;'; ?>
<?php if ( ! isset( $_SESSION ) ) { @session_start(); } if ( isset( $_SESSION['wccp_help_blue_message_dismiss'] ) ) echo 'display: none !important;'; ?>
}
</style>
<script>
(function($) {
$(document).ready(function() {
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.show_email_inquiry_button_after_login', function( event, value, status ) {
		if ( status == 'true' ) {
			$(".help_blue_message_container").slideDown();
		} else if( $("input.show_email_inquiry_button_before_login").prop( "checked" ) == false ) {
			$(".help_blue_message_container").slideUp();
		}
	});
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.show_email_inquiry_button_before_login', function( event, value, status ) {
		if ( status == 'true' ) {
			$(".help_blue_message_container").slideDown();
		} else if( $("input.show_email_inquiry_button_after_login").prop( "checked" ) == false ) {
			$(".help_blue_message_container").slideUp();
		}
	});
	
	$(document).on( "click", ".help_blue_message_dontshow", function(){
		$(".help_blue_message_tr").slideUp();
		$(".help_blue_message_container").slideUp();
		var data = {
				action: 		"wccp_help_blue_message_dontshow",
				option_name: 	"wccp_help_blue_message_dontshow",
				security: 		"<?php echo wp_create_nonce("wccp_help_blue_message_dontshow"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
	
	$(document).on( "click", ".help_blue_message_dismiss", function(){
		$(".help_blue_message_tr").slideUp();
		$(".help_blue_message_container").slideUp();
		var data = {
				action: 		"wccp_help_blue_message_dismiss",
				session_name: 	"wccp_help_blue_message_dismiss",
				security: 		"<?php echo wp_create_nonce("wccp_help_blue_message_dismiss"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
});
})(jQuery);
</script>
    <?php	
	}
}
?>