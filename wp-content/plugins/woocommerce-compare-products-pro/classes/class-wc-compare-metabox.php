<?php
/**
 * WooCommerce Compare Meta Box
 *
 * Add Meta box into Product Edit screen page
 *
 * Table Of Contents
 *
 * compare_meta_boxes()
 * woocp_product_get_fields()
 * woo_compare_feature_box()
 * woo_show_field_of_cat()
 * woo_variations_compare_feature_box()
 * woo_variation_show_field_of_cat()
 * woocp_get_variation_compare()
 * woocp_variation_get_fields()
 * variable_compare_meta_boxes()
 * save_compare_meta_boxes()
 */
class WC_Compare_MetaBox 
{
	public static function compare_meta_boxes() {
		global $post;
		$pagename = 'product';
		add_meta_box( 'woo_compare_feature_box', __('Compare Attribute Fields', 'woo_cp'), array('WC_Compare_MetaBox', 'woo_compare_feature_box'), $pagename, 'normal', 'high' );
	}

	public static function woocp_product_get_fields() {
		check_ajax_referer( 'woocp-product-compare', 'security' );
		$cat_id = $_REQUEST['cat_id'];
		$post_id = $_REQUEST['post_id'];
		WC_Compare_MetaBox::woo_show_field_of_cat($post_id, $cat_id);
		die();
	}

	public static function woo_compare_feature_box() {
		$woocp_product_compare = wp_create_nonce("woocp-product-compare");
		global $post;
		$post_id = $post->ID;
		$deactivate_compare_feature = get_post_meta( $post_id, '_woo_deactivate_compare_feature', true );
		$compare_category = get_post_meta( $post_id, '_woo_compare_category', true );
?>
		<style>
			.comparison_category_features_data th{padding-left:0px !important;}
			@media screen and ( max-width: 782px ) {
				.comparison_category_features_data textarea, .comparison_category_features_data input[type="text"], .comparison_category_features_data input[type="email"], .comparison_category_features_data input[type="number"], .comparison_category_features_data input[type="password"], .comparison_category_features_data select {
					width: 100% !important;	
				}
			}
		</style>
        <script type="text/javascript">
		(function($){
			$(function(){
				$(document).on('click', '.deactivate_compare_feature', function(){
					if ($(this).is(':checked')) {
						$(this).siblings(".compare_feature_activate_form").show();
					} else {
						$(this).siblings(".compare_feature_activate_form").hide();
					}
				});
				$("#compare_category").change(function(){
					var cat_id = $(this).val();
					var post_id = <?php echo $post_id; ?>;
					$(".compare_widget_loader").show();
					var data = {
                        action: 'woocp_product_get_fields',
                        cat_id: cat_id,
                        post_id: post_id,
                        security: '<?php echo $woocp_product_compare; ?>'
                    };
                    $.post('<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>', data, function(response) {
						$(".compare_widget_loader").hide();
						$("#compare_cat_fields").html(response);
					});
				});
			});
		})(jQuery);
		</script>
        <input id='deactivate_compare_feature' type='checkbox' value='no' <?php checked ( $deactivate_compare_feature, 'no' ) ; ?> name='_woo_deactivate_compare_feature' class="deactivate_compare_feature" style="float:none; width:auto; display:inline-block;" />
		<label style="display:inline-block" for='deactivate_compare_feature' class='small'><?php _e( "Activate Compare Attribute for this Product", 'woo_cp' ); ?></label>
        <div class="compare_feature_activate_form" style=" <?php if ( $deactivate_compare_feature == 'yes') { echo 'display:none;';} ?>">
            <p><label style="display:inline-block" for='compare_category' class='small'><?php _e( "Select a  Compare Category for this Product", 'woo_cp' ); ?></label> :
                <select name="_woo_compare_category" id="compare_category" style="width:200px;">
                    <option value="0"><?php _e('Select...', 'woo_cp'); ?></option>
            <?php
            $compare_cats = WC_Compare_Functions::get_all_compare_cats();
            if ( is_array( $compare_cats ) && count( $compare_cats ) > 0 ) {
                foreach ( $compare_cats as $cat ) {
				?>
                	<option value="<?php echo $cat->term_id; ?>" <?php selected( $cat->term_id, $compare_category ) ; ?> ><?php echo esc_html( $cat->name ); ?></option>
                <?php
                }
            }
    		?>
                </select> <img class="compare_widget_loader" style="display:none;" src="<?php echo WOOCP_IMAGES_URL; ?>/ajax-loader.gif" border=0 />
            </p>
            <div id="compare_cat_fields"><?php WC_Compare_MetaBox::woo_show_field_of_cat( $post_id, $compare_category ); ?></div>
		</div>                
	<?php
	}

	public static function woo_show_field_of_cat( $post_id=0, $cat_id=0 ) {
		if ( $cat_id < 1 ) return;
		
		$is_compare_cat = WC_Compare_Functions::get_compare_category_meta( $cat_id, 'is_compare_cat' );
		if ( $is_compare_cat == 'yes' ) {
?>
        <table cellspacing="0" cellpadding="5" style="width: 100%;" class="form-table comparison_category_features_data">
            <tbody>
        <?php
			$compare_fields = WC_Compare_Categories_Fields_Data::get_results( "cat_id='".$cat_id."'", 'field_order ASC' );
			if ( is_array( $compare_fields ) && count( $compare_fields ) > 0 ) {

				foreach ( $compare_fields as $attribute ) {
					$field_unit = WC_Compare_Functions::get_compare_attribute_meta( $attribute->attribute_id, 'field_unit' );
					$field_type = WC_Compare_Functions::get_compare_attribute_meta( $attribute->attribute_id, 'field_type' );
					$attribute_terms = get_terms( wc_attribute_taxonomy_name( $attribute->attribute_name ) , array( 'parent' => 0, 'hide_empty' => 0, 'hierarchical' => 0 ) );
?>
                <tr class="form-field">
                    <th valign="top" scope="row"><label style="display:inline-block" for="attribute-<?php echo $attribute->attribute_id; ?>"><strong><?php echo stripslashes( $attribute->attribute_label ); ?> : </strong></label><?php if ( $field_unit !== false && $field_unit != '' ) { ?><br />(<?php echo trim( stripslashes( $field_unit ) ); ?>)<?php } ?></th>
                    <td>
               	<?php
					$field_value = get_post_meta( $post_id, '_woo_compare_attribute-'.$attribute->attribute_id, true );
					switch ( $field_type ) {
					case "text-area":
						echo '<textarea style="width:400px" name="_woo_compare_attribute-'.$attribute->attribute_id.'" id="attribute-'.$attribute->attribute_id.'">'.$field_value.'</textarea>';
						break;
						
					case "wp-video":
						echo '<textarea style="width:400px" name="_woo_compare_attribute-'.$attribute->attribute_id.'" id="attribute-'.$attribute->attribute_id.'">'.$field_value.'</textarea>';
						echo '<div class="description">'.__( 'Input Video URL - Supported formats Youtube, Vimeo, WordPressTV', 'woo_cp' ).'</div>';
						break;
						
					case "wp-audio":
						echo '<textarea style="width:400px" name="_woo_compare_attribute-'.$attribute->attribute_id.'" id="attribute-'.$attribute->attribute_id.'">'.$field_value.'</textarea>';
						echo '<div class="description">'.__( 'Input Audio URL - Supported formats .mp3, .m4a, .ogg, .wav file', 'woo_cp' ).'</div>';
						break;

					case "checkbox":
						if ( is_serialized( $field_value ) ) $field_value = maybe_unserialize( $field_value );
						if ( ! is_array( $field_value ) ) $field_value = array();
						if ( is_array( $attribute_terms ) && count( $attribute_terms ) > 0 ) {
							foreach ( $attribute_terms as $term ) {
								if ( in_array( $term->term_id, $field_value ) ) {
									echo '<input type="checkbox" name="_woo_compare_attribute-'.$attribute->attribute_id.'[]" value="'.esc_attr( $term->term_id ).'" checked="checked" style="float:none; width:auto; display:inline-block;" /> '. esc_html( $term->name ) .' &nbsp;&nbsp;&nbsp;';
								} else {
									echo '<input type="checkbox" name="_woo_compare_attribute-'.$attribute->attribute_id.'[]" value="'.esc_attr( $term->term_id ).'" style="float:none; width:auto; display:inline-block;" /> '. esc_html( $term->name ) .' &nbsp;&nbsp;&nbsp;';
								}
							}
						}
						break;

					case "radio":
						if ( is_array( $attribute_terms ) && count( $attribute_terms ) > 0 ) {
							foreach ( $attribute_terms as $term ) {
								echo '<input type="radio" name="_woo_compare_attribute-'.$attribute->attribute_id.'" value="'.esc_attr( $term->term_id ).'" '.checked( $field_value, $term->term_id , false ).' style="float:none; width:auto; display:inline-block;" /> '. esc_html( $term->name ) .' &nbsp;&nbsp;&nbsp;';
							}
						}
						break;

					case "drop-down":
						echo '<select name="_woo_compare_attribute-'.$attribute->attribute_id.'" id="attribute-'.$attribute->attribute_id.'" style="width:400px">';
						echo '<option value="">'.__( "Select value", 'woo_cp' ).'</option>';
						if ( is_array( $attribute_terms ) && count( $attribute_terms ) > 0 ) {
							foreach ( $attribute_terms as $term ) {
								echo '<option value="'.esc_attr( $term->term_id ).'" '. selected( $field_value, $term->term_id , false ) .'>'. esc_html( $term->name ) .'</option>';
							}
						}
						echo '</select>';
						break;

					case "multi-select":
						if ( is_serialized( $field_value ) ) $field_value = maybe_unserialize( $field_value );
						if ( ! is_array( $field_value ) ) $field_value = array();
						echo '<select multiple="multiple" name="_woo_compare_attribute-'.$attribute->attribute_id.'[]" id="attribute-'.$attribute->attribute_id.'" style="width:400px">';
						if ( is_array( $attribute_terms ) && count( $attribute_terms ) > 0 ) {
							foreach ( $attribute_terms as $term ) {
								if ( in_array( $term->term_id, $field_value ) ) {
									echo '<option value="'.esc_attr( $term->term_id ).'" selected="selected">'. esc_html( $term->name ) .'</option>';
								}else {
									echo '<option value="'.esc_attr( $term->term_id ).'">'. esc_html( $term->name ) .'</option>';
								}
							}
						}
						echo '</select>';
						break;

					default:
						echo '<input style="width:400px" type="text" name="_woo_compare_attribute-'.$attribute->attribute_id.'" id="attribute-'.$attribute->attribute_id.'" value="'.esc_attr( $field_value ).'" />';
						break;
					}
?>
                    </td>
                </tr>
        <?php
				}
			} else {
?>
        		<tr><td><i style="text-decoration:blink"><?php _e('There are no Attributes created for this category, please add some.', 'woo_cp'); ?> <a href="edit.php?post_type=product&page=product_attributes" target="_blank"><?php _e('This page', 'woo_cp'); ?></a></i></td></tr>
        <?php
			}
?>
        	</tbody>
        </table>
		<?php
		}
	}

	public static function woo_variations_compare_feature_box( $post_id ) {
		$deactivate_compare_feature = get_post_meta( $post_id, '_woo_deactivate_compare_feature', true );
		$compare_category = get_post_meta( $post_id, '_woo_compare_category', true );
?>
		<br />
        <input id='deactivate_compare_feature_<?php echo $post_id; ?>' type='checkbox' value='no' <?php checked ( $deactivate_compare_feature, 'no' ); ?> class="deactivate_compare_feature" name='variable_woo_deactivate_compare_feature[<?php echo $post_id; ?>]' style="float:none; width:auto; display:inline-block;" />
		<label style="display:inline-block" for='deactivate_compare_feature_<?php echo $post_id; ?>' class='small'><?php _e( "Activate Compare Attribute for this Product", 'woo_cp' ); ?></label>
        <div class="compare_feature_activate_form" style=" <?php if ( $deactivate_compare_feature == 'yes') { echo 'display:none;';} ?>">
            <p><label style="display:inline-block" for='variable_woo_compare_category_<?php echo $post_id; ?>' class='small'><?php _e( "Select a  Compare Category for this Product", 'woo_cp' ); ?></label> :
                <select name="variable_woo_compare_category[<?php echo $post_id; ?>]" class="variable_compare_category" id="variable_woo_compare_category_<?php echo $post_id; ?>" style="width:200px;" rel="<?php echo $post_id; ?>">
                    <option value="0"><?php _e('Select...', 'woo_cp'); ?></option>
            <?php
			$compare_cats = WC_Compare_Functions::get_all_compare_cats();
            if ( is_array( $compare_cats ) && count( $compare_cats ) > 0 ) {
                foreach ( $compare_cats as $cat ) {
				?>
                	<option value="<?php echo $cat->term_id; ?>" <?php selected( $cat->term_id, $compare_category ) ; ?> ><?php echo esc_html( $cat->name ); ?></option>
                <?php
                }
            }
    		?>
                </select> <img id="variable_compare_widget_loader_<?php echo $post_id; ?>" style="display:none;" src="<?php echo WOOCP_IMAGES_URL; ?>/ajax-loader.gif" border=0 />
            </p>
            <div id="variable_compare_cat_fields_<?php echo $post_id; ?>"><?php WC_Compare_MetaBox::woo_variation_show_field_of_cat($post_id, $compare_category); ?></div>
		</div>
	<?php
	}

	public static function woo_variation_show_field_of_cat( $post_id=0, $cat_id=0 ) {
		if ( $cat_id < 1 ) return;
		
		$is_compare_cat = WC_Compare_Functions::get_compare_category_meta( $cat_id, 'is_compare_cat' );
		if ( $is_compare_cat == 'yes' ) {
?>
        <table cellspacing="0" cellpadding="5" style="width: 100%;" class="form-table comparison_category_features_data">
            <tbody>
        <?php
			$compare_fields = WC_Compare_Categories_Fields_Data::get_results( "cat_id='".$cat_id."'", 'field_order ASC' );
			if ( is_array( $compare_fields ) && count( $compare_fields ) > 0 ) {

				foreach ( $compare_fields as $attribute ) {
					$field_unit = WC_Compare_Functions::get_compare_attribute_meta( $attribute->attribute_id, 'field_unit' );
					$field_type = WC_Compare_Functions::get_compare_attribute_meta( $attribute->attribute_id, 'field_type' );
					$attribute_terms = get_terms( wc_attribute_taxonomy_name( $attribute->attribute_name ) , array( 'parent' => 0, 'hide_empty' => 0, 'hierarchical' => 0 ) );
?>
                <tr class="form-field">
                    <th valign="top" scope="row"><label style="display:inline-block" for="attribute-<?php echo $attribute->attribute_id; ?>_<?php echo $post_id; ?>"><strong><?php echo stripslashes( $attribute->attribute_label ); ?> : </strong></label><?php if ( $field_unit !== false && $field_unit != '' ) { ?><br />(<?php echo trim( stripslashes( $field_unit ) ); ?>)<?php } ?></th>
                    <td>
               	<?php
					$field_value = get_post_meta( $post_id, '_woo_compare_attribute-'.$attribute->attribute_id, true );
					switch ( $field_type ) {
					case "text-area":
						echo '<textarea style="width:400px" name="variable_woo_compare_attribute-'.$attribute->attribute_id.'['.$post_id.']" id="attribute-'.$attribute->attribute_id.'_'.$post_id.'">'.$field_value.'</textarea>';
						break;
						
					case "wp-video":
						echo '<textarea style="width:400px" name="variable_woo_compare_attribute-'.$attribute->attribute_id.'['.$post_id.']" id="attribute-'.$attribute->attribute_id.'_'.$post_id.'">'.$field_value.'</textarea>';
						echo '<div class="description">'.__( 'Input Video URL - Supported formats Youtube, Vimeo, WordPressTV', 'woo_cp' ).'</div>';
						break;
						
					case "wp-audio":
						echo '<textarea style="width:400px" name="variable_woo_compare_attribute-'.$attribute->attribute_id.'['.$post_id.']" id="attribute-'.$attribute->attribute_id.'_'.$post_id.'">'.$field_value.'</textarea>';
						echo '<div class="description">'.__( 'Input Audio URL - Supported formats .mp3, .m4a, .ogg, .wav file', 'woo_cp' ).'</div>';
						break;

					case "checkbox":
						if ( is_serialized( $field_value ) ) $field_value = maybe_unserialize( $field_value );
						if ( ! is_array( $field_value ) ) $field_value = array();
						if ( is_array( $attribute_terms ) && count( $attribute_terms ) > 0 ) {
							foreach ( $attribute_terms as $term ) {
								if ( in_array( $term->term_id, $field_value ) ) {
									echo '<input type="checkbox" name="variable_woo_compare_attribute-'.$attribute->attribute_id.'['.$post_id.'][]" value="'.esc_attr( $term->term_id ).'" checked="checked" style="float:none; width:auto; display:inline-block;" /> '. esc_html( $term->name ) .' &nbsp;&nbsp;&nbsp;';
								}else {
									echo '<input type="checkbox" name="variable_woo_compare_attribute-'.$attribute->attribute_id.'['.$post_id.'][]" value="'.esc_attr( $term->term_id ).'" style="float:none; width:auto; display:inline-block;" /> '. esc_html( $term->name ) .' &nbsp;&nbsp;&nbsp;';
								}
							}
						}
						break;

					case "radio":
						if ( is_array( $attribute_terms ) && count( $attribute_terms ) > 0 ) {
							foreach ( $attribute_terms as $term ) {
								echo '<input type="radio" name="variable_woo_compare_attribute-'.$attribute->attribute_id.'['.$post_id.']" value="'.esc_attr( $term->term_id ).'" '.checked( $field_value, $term->term_id , false ).' style="float:none; width:auto; display:inline-block;" /> '. esc_html( $term->name ) .' &nbsp;&nbsp;&nbsp;';
							}
						}
						break;

					case "drop-down":
						echo '<select name="variable_woo_compare_attribute-'.$attribute->attribute_id.'['.$post_id.']" id="attribute-'.$attribute->attribute_id.'_'.$post_id.'" style="width:400px">';
						echo '<option value="">'.__( "Select value", 'woo_cp' ).'</option>';
						if ( is_array( $attribute_terms ) && count( $attribute_terms ) > 0 ) {
							foreach ( $attribute_terms as $term ) {
								echo '<option value="'.esc_attr( $term->term_id ).'" '. selected( $field_value, $term->term_id , false ) .'>'. esc_html( $term->name ) .'</option>';
							}
						}
						echo '</select>';
						break;

					case "multi-select":
						if ( is_serialized( $field_value ) ) $field_value = maybe_unserialize( $field_value );
						if ( ! is_array( $field_value ) ) $field_value = array();
						echo '<select multiple="multiple" name="variable_woo_compare_attribute-'.$attribute->attribute_id.'['.$post_id.'][]" id="attribute-'.$attribute->attribute_id.'_'.$post_id.'" style="width:400px">';
						if ( is_array( $attribute_terms ) && count( $attribute_terms ) > 0 ) {
							foreach ( $attribute_terms as $term ) {
								if ( in_array( $term->term_id, $field_value ) ) {
									echo '<option value="'.esc_attr( $term->term_id ).'" selected="selected">'. esc_html( $term->name ) .'</option>';
								}else {
									echo '<option value="'.esc_attr( $term->term_id ).'">'. esc_html( $term->name ) .'</option>';
								}
							}
						}
						echo '</select>';
						break;

					default:
						echo '<input style="width:400px" type="text" name="variable_woo_compare_attribute-'.$attribute->attribute_id.'['.$post_id.']" id="attribute-'.$attribute->attribute_id.'_'.$post_id.'" value="'.esc_attr( $field_value ).'" />';
						break;
					}
?>
                    </td>
                </tr>
        <?php
				}
			} else {
?>
        		<tr><td><i style="text-decoration:blink"><?php _e('There are no Attributes created for this category, please add some.', 'woo_cp'); ?> <a href="edit.php?post_type=product&page=product_attributes" target="_blank"><?php _e('This page', 'woo_cp'); ?></a></i></td></tr>
        <?php
			}
?>
        	</tbody>
        </table>
<?php
		}
	}

	public static function woocp_get_variation_compare() {
		check_ajax_referer( 'woocp-variable-compare', 'security' );
		$variation_id = $_REQUEST['variation_id'];
		echo WC_Compare_MetaBox::woo_variations_compare_feature_box($variation_id);
		die();
	}

	public static function woocp_variation_get_fields() {
		check_ajax_referer( 'woocp-variable-compare', 'security' );
		$cat_id = $_REQUEST['cat_id'];
		$post_id = $_REQUEST['post_id'];
		WC_Compare_MetaBox::woo_variation_show_field_of_cat($post_id, $cat_id);
		die();
	}

	public static function variable_compare_meta_boxes() {
		global $post;
		$current_db_version = get_option( 'woocommerce_db_version', null );
		$post_status = get_post_status($post->ID);
		$post_type = get_post_type($post->ID);
		if ($post_type == 'product' && $post_status != false) {
			$woocp_variable_compare = wp_create_nonce("woocp-variable-compare");
			$attributes = (array) maybe_unserialize( get_post_meta($post->ID, '_product_attributes', true) );

			// See if any are set
			$variation_attribute_found = false;
			if ($attributes) foreach ($attributes as $attribute) {
					if (isset($attribute['is_variation'])) :
						$variation_attribute_found = true;
					break;
					endif;
				}
			if ($variation_attribute_found) {
				if ( version_compare( $current_db_version, '2.1.0', '<' ) && null !== $current_db_version ) {
					$colspan = 7;
				} else {
					$colspan = 3;
				}
				$args = array(
					'post_type' => 'product_variation',
					'post_status' => array('private', 'publish'),
					'numberposts' => -1,
					'orderby' => 'id',
					'order' => 'asc',
					'post_parent' => $post->ID
				);
				$variations = get_posts($args);
				$loop = 0;
				ob_start();
?>
			jQuery(function(){
			<?php
				if ($variations && count($variations) > 0) {?>
				jQuery('#variable_product_options .woocommerce_variation').each(function(){
					var current_variation = jQuery(this);
					if(current_variation.hasClass('have_compare_feature') == false){
						var variation_id = jQuery(this).find('.remove_variation').attr('rel');
						var data = {
							action: 'woocp_get_variation_compare',
							variation_id: variation_id,
							security: '<?php echo $woocp_variable_compare; ?>'
						};
						jQuery.post('<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>', data, function(response) {
							current_variation.find('table.woocommerce_variable_attributes').append('<tr><td colspan="<?php echo $colspan; ?>">'+response+'</td></tr>');
						});
						current_variation.addClass('have_compare_feature');
					}
				});
			<?php } ?>
				jQuery('#variable_product_options').on('click', 'button.add_variation', function(){
					setTimeout(function(){
						jQuery('#variable_product_options .woocommerce_variation').each(function(){
							var current_variation = jQuery(this);
							if(current_variation.hasClass('have_compare_feature') == false){
								var variation_id = jQuery(this).find('.remove_variation').attr('rel');
								var data = {
									action: 'woocp_get_variation_compare',
									variation_id: variation_id,
									security: '<?php echo $woocp_variable_compare; ?>'
								};
								jQuery.post('<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>', data, function(response) {
									current_variation.find('table.woocommerce_variable_attributes').append('<tr><td colspan="<?php echo $colspan; ?>">'+response+'</td></tr>');
								});
								current_variation.addClass('have_compare_feature');
							}
						});
					}, 3000);
				});
				jQuery('#variable_product_options').on('click', 'button.link_all_variations', function(){
					setTimeout(function(){
						jQuery('#variable_product_options .woocommerce_variation').each(function(){
							var current_variation = jQuery(this);
							if(current_variation.hasClass('have_compare_feature') == false){
								var variation_id = jQuery(this).find('.remove_variation').attr('rel');
								var data = {
									action: 'woocp_get_variation_compare',
									variation_id: variation_id,
									security: '<?php echo $woocp_variable_compare; ?>'
								};
								jQuery.post('<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>', data, function(response) {
									current_variation.find('table.woocommerce_variable_attributes').append('<tr><td colspan="<?php echo $colspan; ?>">'+response+'</td></tr>');
								});
								current_variation.addClass('have_compare_feature');
							}
						});
					}, 5000);
				});
				jQuery(document).on("change", ".variable_compare_category", function(){
						var cat_id = jQuery(this).val();
						var post_id = jQuery(this).attr("rel");
						jQuery("#variable_compare_widget_loader_"+post_id).show();
						var data = {
							action: 'woocp_variation_get_fields',
							cat_id: cat_id,
							post_id: post_id,
							security: '<?php echo $woocp_variable_compare; ?>'
						};
						jQuery.post('<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>', data, function(response) {
							jQuery("#variable_compare_widget_loader_"+post_id).hide();
							jQuery("#variable_compare_cat_fields_"+post_id).html(response);
						});
				});
			});

	<?php
				$javascript = ob_get_clean();
				if ( version_compare( $current_db_version, '2.1.0', '<' ) && null !== $current_db_version ) {
					global $woocommerce;
					$woocommerce->add_inline_js( $javascript );
				} else {
					wc_enqueue_js( $javascript );
				}
			}
		}
	}

	public static function save_compare_meta_boxes($post_id) {
		$post_status = get_post_status($post_id);
		$post_type = get_post_type($post_id);
		if ($post_type == 'product' && $post_status != false) {
			if (isset($_REQUEST['_woo_deactivate_compare_feature']) && $_REQUEST['_woo_deactivate_compare_feature'] == 'no' ) {
				update_post_meta($post_id, '_woo_deactivate_compare_feature', 'no');
			} else {
				update_post_meta($post_id, '_woo_deactivate_compare_feature', 'yes');
			}
			
			$compare_category = 0;
			if ( isset($_REQUEST['_woo_compare_category']) ) {
				$compare_category = $_REQUEST['_woo_compare_category'];
				update_post_meta( $post_id, '_woo_compare_category', $compare_category );
			}
	
			WC_Compare_Functions::empty_attribute_data_product( $post_id );
			$compare_fields = WC_Compare_Categories_Fields_Data::get_results( "cat_id='".$compare_category."'", 'field_order ASC' );
			if ( is_array( $compare_fields ) && count( $compare_fields ) > 0 ) {
				foreach ( $compare_fields as $attribute ) {
					if ( isset( $_REQUEST['_woo_compare_attribute-'.$attribute->attribute_id] ) )
						add_post_meta( $post_id, '_woo_compare_attribute-'.$attribute->attribute_id, $_REQUEST['_woo_compare_attribute-'.$attribute->attribute_id] );
				}
			}

			if ( isset( $_REQUEST['variable_post_id'] ) ) {
				$variable_ids = $_REQUEST['variable_post_id'];
				foreach ( $variable_ids as $variation_id ) {
					$post_type = get_post_type($variation_id);
					if ($post_type == 'product_variation') {
						if ( isset( $_REQUEST['variable_woo_deactivate_compare_feature'][$variation_id] ) && $_REQUEST['variable_woo_deactivate_compare_feature'][$variation_id] == 'no' ) {
							update_post_meta($variation_id, '_woo_deactivate_compare_feature', 'no');
						} else {
							update_post_meta($variation_id, '_woo_deactivate_compare_feature', 'yes');
						}
						
						$variation_compare_category = 0;
						if ( isset($_REQUEST['variable_woo_compare_category'][$variation_id]) ) {
							$variation_compare_category = $_REQUEST['variable_woo_compare_category'][$variation_id];
							update_post_meta( $variation_id, '_woo_compare_category', $variation_compare_category );
						}

						WC_Compare_Functions::empty_attribute_data_product( $variation_id );
						$compare_fields = WC_Compare_Categories_Fields_Data::get_results( "cat_id='".$variation_compare_category."'", 'field_order ASC' );
						if ( is_array( $compare_fields ) && count( $compare_fields ) > 0 ) {
							foreach ( $compare_fields as $attribute ) {
								if ( isset( $_REQUEST['variable_woo_compare_attribute-'.$attribute->attribute_id][$variation_id] ) )
									add_post_meta( $variation_id, '_woo_compare_attribute-'.$attribute->attribute_id, $_REQUEST['variable_woo_compare_attribute-'.$attribute->attribute_id][$variation_id] );
							}
						}
					}
				}
			}
		}
	}
}
?>
