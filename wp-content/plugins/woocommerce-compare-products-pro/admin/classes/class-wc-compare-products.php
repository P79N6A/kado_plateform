<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * WooCommerce Compare Products Manager
 *
 * Table Of Contents
 *
 * woocp_get_products()
 * woocp_popup_features()
 * woocp_products_manager()
 * woocp_compare_products_script()
 */
class WC_Compare_Products_Class 
{
	public static function woocp_get_products() {
		check_ajax_referer( 'woocp-products-manager', 'security' );

		$paged = isset($_POST['page']) ? $_POST['page'] : 1;
		$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
		$cp_show_variations = isset($_POST['cp_show_variations']) ? $_POST['cp_show_variations'] : 0;
		$start = ($paged-1)*$rp;
		$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'title';
		$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
		$query = isset($_POST['query']) ? $_POST['query'] : false;
		$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

		$data_a = array();
		$data_a['s'] = $query;
		$data_a['numberposts'] = $rp;
		$data_a['offset'] = $start;
		if ($sortname == 'title') {
			$data_a['orderby'] = $sortname;
		}else {
			$data_a['orderby'] = 'meta_value';
			$data_a['meta_key'] = $sortname;
		}
		$data_a['order'] = strtoupper($sortorder);
		$data_a['post_type'] = 'product';
		$data_a['post_status'] = array('private', 'publish');

		$all_data = array();
		$all_data['s'] = $query;
		$all_data['posts_per_page'] = 1;
		$all_data['post_type'] = 'product';
		$all_data['post_status'] = array('private', 'publish');

		//$all_products = get_posts($all_data);
		//$total = count($all_products);
		$query = new WP_Query($all_data);
		$total = $query->found_posts;
		//$total = $wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_title LIKE '%{$query}%' AND post_type='wpsc-product' AND post_status IN ('private', 'publish') ;");
		$products = get_posts($data_a);

		$jsonData = array('page'=>$paged, 'total'=>$total, 'rows'=>array());
		$number = $start;

		foreach ($products as $product) {
			$number++;
			//If cell's elements have named keys, they must match column names
			//Only cell's with named keys and matching columns are order independent.
			$terms = get_the_terms( $product->ID, 'product_cat' );
			$on_cats = '';
			if ( $terms && ! is_wp_error( $terms ) ) {
				$cat_links = array();
				foreach ( $terms as $term ) {
					$cat_links[] = $term->name;
				}
				$on_cats = join( ", ", $cat_links );
			}
			
			$cat = false;
			$cat_id = get_post_meta( $product->ID, '_woo_compare_category', true );
			if ( $cat_id ) $cat = get_term( $cat_id, 'product_cat' );
			
			$deactivate_compare_feature = get_post_meta( $product->ID, '_woo_deactivate_compare_feature', true );
			if ( $deactivate_compare_feature == 'no' && $cat ) $status = '<font style="color:green">'.__( "Activated", 'woo_cp' ).'</font>';
			else $status = '<font style="color:red">'.__( "Deactivated", 'woo_cp' ).'</font>';

			$entry = array(
				'id' => $product->ID,
				'cell' => array(
					'number' => $number,
					'title' => $product->post_title,
					'cat' => $on_cats,
					'_woo_compare_category_name' => ( ( $cat ) ? stripslashes( $cat->name ) : '' ),
					'_woo_deactivate_compare_feature' => $status,
					'edit' => '<span rel="'.$product->ID.'|'.$paged.'|'.$rp.'|'.$sortname.'|'.$sortorder.'|'.$cp_show_variations.'|'.$qtype.'" class="edit_product_compare">'.__( "Edit", 'woo_cp' ).'</span>'
				),
			);
			$jsonData['rows'][] = $entry;
			if ($cp_show_variations == 1) {
				$args = array(
					'post_type' => 'product_variation',
					'post_status' => array('publish'),
					'numberposts' => -1,
					'orderby' => 'id',
					'order' => 'asc',
					'post_parent' => $product->ID
				);
				$variations = get_posts($args);
				if ($variations && is_array($variations) && count($variations) > 0) {
					$sub = 0;
					foreach ($variations as $variation) {
						$sub++;
						$cat = false;
						$cat_id = get_post_meta( $variation->ID, '_woo_compare_category', true );
						if ( $cat_id ) $cat = get_term( $cat_id, 'product_cat' );
			
						$deactivate_compare_feature = get_post_meta( $variation->ID, '_woo_deactivate_compare_feature', true );
						if ($deactivate_compare_feature == 'no' && $cat ) $status = '<font style="color:green">'.__( "Activated", 'woo_cp' ).'</font>';
						else $status = '<font style="color:red">'.__( "Deactivated", 'woo_cp' ).'</font>';

						$entry = array(
							'id' => $variation->ID,
							'cell' => array(
								'number' => '',
								'title' => '-- '.WC_Compare_Functions::get_variation_name($variation->ID),
								'cat' => $on_cats,
								'_woo_compare_category_name' => ( ( $cat ) ? stripslashes( $cat->name ) : '' ),
								'_woo_deactivate_compare_feature' => $status,
								'edit' => '<span rel="'.$variation->ID.'|'.$paged.'|'.$rp.'|'.$sortname.'|'.$sortorder.'|'.$cp_show_variations.'|'.$qtype.'" class="edit_product_compare">'.__( "Edit", 'woo_cp' ).'</span>'
							),
						);
						$jsonData['rows'][] = $entry;
					}
				}
			}
		}
		echo json_encode($jsonData);
		die();
	}

	public static function woocp_popup_features() {
		check_ajax_referer( 'woocp-popup-features', 'security' );
		$post_id = 0;
		$paged = 1;
		$rp = 10;
		$sortname = 'title';
		$sortorder = 'asc';
		$cp_show_variations = 0;
		$query = false;
		$qtype = false;
		$product_data = explode('|', $_REQUEST['product_data']);
		if (is_array($product_data) && count($product_data) > 0) {
			$post_id = $product_data[0];
			$paged = $product_data[1];
			$rp = $product_data[2];
			$sortname = $product_data[3];
			$sortorder = $product_data[4];
			$cp_show_variations = $product_data[5];
			$qtype = $product_data[6];
		}
		if (isset($_REQUEST['search_string']) && trim($_REQUEST['search_string']) != '') $query = trim($_REQUEST['search_string']);

		$woocp_product_compare = wp_create_nonce("woocp-product-compare");
		$deactivate_compare_feature = get_post_meta( $post_id, '_woo_deactivate_compare_feature', true );
		$compare_category = get_post_meta( $post_id, '_woo_compare_category', true );
?>
		<style>
		#compare_cat_fields table td input[type="text"], #compare_cat_fields table td textare, #compare_cat_fields table td select{ width:250px !important; }
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
				$('#deactivate_compare_feature').click(function(){
					if ($(this).is(':checked')) {
						$(".compare_feature_activate_form").show();
					} else {
						$(".compare_feature_activate_form").hide();
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
		<div id="TB_iframeContent" style="position:relative;width:100%;">
        <div style="padding:10px 25px;">
        <form action="admin.php?page=woo-compare-products" method="post" name="form_product_features">
        	<input type="hidden" name="paged" value="<?php echo $paged; ?>" />
            <input type="hidden" name="rp" value="<?php echo $rp; ?>" />
            <input type="hidden" name="sortname" value="<?php echo $sortname; ?>" />
            <input type="hidden" name="sortorder" value="<?php echo $sortorder; ?>" />
            <input type="hidden" name="cp_show_variations" value="<?php echo $cp_show_variations; ?>" />
            <input type="hidden" name="qtype" value="<?php echo $qtype; ?>" />
        	<input type="hidden" name="query" value="<?php echo $query; ?>" />
        	<h3 style="margin-top:0; padding-top:20px;"><?php echo WC_Compare_Functions::get_variation_name($post_id); ?></h3>
            <input type="hidden" name="productid" value="<?php echo $post_id; ?>" />
            <p><input id='deactivate_compare_feature' type='checkbox' value='no' <?php checked ( $deactivate_compare_feature, 'no' ) ; ?> name='_woo_deactivate_compare_feature' style="float:none; width:auto; display:inline-block;" />
            <label style="display:inline-block" for='deactivate_compare_feature' class='small'><?php _e( "Activate Compare Attribute for this Product", 'woo_cp' ); ?></label></p>
            <div class="compare_feature_activate_form" style=" <?php if ( $deactivate_compare_feature == 'yes') { echo 'display:none;';} ?>">
                <label style="display:inline-block; float:left;" for='compare_category' class='small'><?php _e( "Select a  Compare Category for this Product", 'woo_cp' ); ?> :</label>
                <p style="margin:0; padding:0; "><select name="_woo_compare_category" id="compare_category" style="width:180px; margin-top:-2px;">
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
                <div style="clear:both; margin-bottom:10px;"></div>
                <div id="compare_cat_fields" style=""><?php WC_Compare_MetaBox::woo_show_field_of_cat($post_id, $compare_category); ?></div>
			</div>
            <div style="text-align:left; display:inline-block; padding:10px 0 30px 0;">
            	<input type="submit" name="bt_update_product_features" id="bt_update_product_features" class="button button-primary" value="<?php _e( "Update", 'woo_cp' ); ?>" /> 
                <input type="button" class="button" onclick="tb_remove(); return false;" style="margin-left:10px;" value="<?php _e( "Cancel", 'woo_cp' ); ?>"  />
            </div>
        </form>
        </div>
        </div>
		<?php
		die();
	}

	public static function woocp_products_manager() {
		$compare_product_message = '';
		$paged = isset($_POST['paged']) ? $_POST['paged'] : 1;
		$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
		$cp_show_variations = isset($_POST['cp_show_variations']) ? $_POST['cp_show_variations'] : 0;
		$sortname = isset( $_POST['sortname'] ) ? $_POST['sortname'] : 'title';
		$sortorder = isset( $_POST['sortorder'] ) ? $_POST['sortorder'] : 'asc';
		$query = isset( $_POST['query'] ) ? $_POST['query'] : '';
		$qtype = isset( $_POST['qtype'] ) ? $_POST['qtype'] : '';
		if ( isset( $_REQUEST['bt_update_product_features'] ) ) {
			if ( isset( $_REQUEST['productid'] ) && $_REQUEST['productid'] > 0 ) {
				$post_id = $_REQUEST['productid'];
				$post_status = get_post_status($post_id);
				$post_type = get_post_type($post_id);
				if ( ( $post_type == 'product' || $post_type == 'product_variation' ) && $post_status != false ) {
					if ( isset( $_REQUEST['_woo_deactivate_compare_feature'] ) && $_REQUEST['_woo_deactivate_compare_feature'] == 'no' ) {
						update_post_meta( $post_id, '_woo_deactivate_compare_feature', 'no' );
					} else {
						update_post_meta( $post_id, '_woo_deactivate_compare_feature', 'yes' );
					}
					
					$compare_category = $_REQUEST['_woo_compare_category'];
					update_post_meta( $post_id, '_woo_compare_category', $compare_category );

					WC_Compare_Functions::empty_attribute_data_product( $post_id );
					if ( $compare_category > 0 ) {

						$compare_fields = WC_Compare_Categories_Fields_Data::get_results( "cat_id='".$compare_category."'", 'field_order ASC' );
						if ( is_array( $compare_fields ) && count( $compare_fields ) > 0 ) {
							foreach ( $compare_fields as $attribute ) {
								if ( isset( $_REQUEST['_woo_compare_attribute-'.$attribute->attribute_id] ) ) {
									add_post_meta( $post_id, '_woo_compare_attribute-'.$attribute->attribute_id, $_REQUEST['_woo_compare_attribute-'.$attribute->attribute_id] );
								}
							}
						}
					}
				}
				$compare_product_message = '<div class="updated" id="result_msg"><p>'.__('Compare Product Attribute Fields Successfully updated.', 'woo_cp').'.</p></div>';
			}
		}
?>
<style>
	.update_message{padding:10px; background-color:#FFFFCC;border:1px solid #DDDDDD;margin-bottom:15px;}
	body .flexigrid div.sDiv{display:block;}
	.flexigrid div.sDiv .sDiv2 select{display:none;}
	.flexigrid div.sDiv .cp_search, .flexigrid div.sDiv .cp_reset{cursor:pointer;}
	.edit_product_compare{cursor:pointer; text-decoration:underline; color:#06F;}
	.icon32-compare-product {
		background:url(<?php echo WOOCP_IMAGES_URL; ?>/a3-plugins.png) no-repeat left top !important;
	}
</style>
<div id="htmlForm">
<div style="clear:both"></div>
<div class="wrap a3rev_manager_panel_container">
	<div class="icon32 icon32-compare-product" id="icon32-compare-product"><br></div>
	<h2><?php _e('WooCommerce Compare Products Manager', 'woo_cp'); ?></h2>
    <?php echo $compare_product_message; ?>
    <div style="clear:both; margin-bottom:20px;"></div>
    <table id="woocp_products_manager" style="display:none"></table>
    <?php
		$woocp_products_manager = wp_create_nonce("woocp-products-manager");
		$woocp_popup_features = wp_create_nonce("woocp-popup-features");
?>
    <script type="text/javascript">
	(function($){
		$(function(){
			$("#woocp_products_manager").flexigrid({
				url: '<?php echo admin_url( 'admin-ajax.php', 'relative' ) .'?action=woocp_get_products&security='.$woocp_products_manager; ?>',
				dataType: 'json',
				width: 'auto',
				resizable: false,
				colModel : [
					{display: '<?php _e( "No", 'woo_cp' ); ?>', name : 'number', width : 30, sortable : false, align: 'right'},
					{display: '<?php _e( "Product Name", 'woo_cp' ); ?>', name : 'title', width : 380, sortable : true, align: 'left'},
					{display: '<?php _e( "Product Category", 'woo_cp' ); ?>', name : 'cat', width : 160, sortable : false, align: 'left'},
					{display: '<?php _e( "Compare Category", 'woo_cp' ); ?>', name : '_woo_compare_category_name', width : 160, sortable : true, align: 'left'},
					{display: '<?php _e( "Activated / Deactivated", 'woo_cp' ) ;?>', name : '_woo_deactivate_compare_feature', width : 110, sortable : false, align: 'center'},
					{display: '<?php _e( "Edit", 'woo_cp' ); ?>', name : 'edit', width : 50, sortable : false, align: 'center'}
					],
				searchitems : [
					{display: '<?php _e( "Product Name", 'woo_cp' ); ?>', name : 'title', isdefault: true}
					],
				sortname: "title",
				sortorder: "asc",
				usepager: true,
				title: '<?php _e( "Products", 'woo_cp' ); ?>',
				findtext: '<?php _e( "Find Product Name", 'woo_cp' ); ?>',
				useRp: true,
				rp: <?php echo $rp; ?>, //results per page
				newp: <?php echo $paged; ?>,
				page: <?php echo $paged; ?>,
				query: '<?php echo $query; ?>',
				qtype: '<?php echo $qtype; ?>',
				sortname: '<?php echo $sortname; ?>',
				sortorder: '<?php echo $sortorder; ?>',
				rpOptions: [10, 15, 20, 30, 50, 100], //allowed per-page values
				showToggleBtn: false, //show or hide column toggle popup
				showTableToggleBtn: false,
				height: 'auto',
				variations: '<?php echo $cp_show_variations; ?>'
			});
			$(document).on("click", ".edit_product_compare", function(ev){
				var product_data = $(this).attr('rel');
				var search_string = $(".qsbox").val();
				$.fancybox({
					content: "<?php echo admin_url("admin-ajax.php"); ?>?action=woocp_popup_features&product_data="+ product_data +"&security=<?php echo $woocp_popup_features; ?>&search_string="+search_string,
					type: "ajax",
					title: "<?php _e( "Product Compare Attribute Fields", 'woo_cp' ); ?>",
					width: "90%",
					height: "90%",
					maxWidth: "95%",
					maxHeight: "90%",
					autoScale: true,
					autoDimensions: false,
					openEffect	: "none",
					closeEffect	: "none"
				});
				ev.preventDefault();
			});
		});
	})(jQuery);
	</script>
</div>
</div>
<?php
	}

	public static function woocp_compare_products_script() {
		echo'<style>
			#TB_ajaxContent{padding-bottom:0 !important; padding-right:0 !important; height:auto !important; width:auto !important;}
			#TB_iframeContent{width:auto !important; padding-right:10px !important; margin-bottom:0px !important; max-height:480px !important;}
		</style>';
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.pack';
		$woo_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		// validate
		wp_enqueue_script('woocp_flexigrid_script', WOOCP_JS_URL . '/flexigrid/js/flexigrid'.$suffix.'.js');
		wp_enqueue_style( 'woocp_flexigrid_style', WOOCP_JS_URL . '/flexigrid/css/flexigrid'.$suffix.'.css' );

		wp_enqueue_style( 'woocommerce_fancybox_styles', WOOCP_JS_URL . '/fancybox/fancybox.css' );
		wp_enqueue_script( 'fancybox', WOOCP_JS_URL . '/fancybox/fancybox'.$woo_suffix.'.js');
	}
}
?>
