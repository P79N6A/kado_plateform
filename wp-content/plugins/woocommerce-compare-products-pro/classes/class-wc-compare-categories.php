<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handles compare categories in admin
 *
 */
class WC_Admin_Compare_Categories {

	/**
	 * Constructor
	 */
	public function __construct() {
		
		// Include script
		$this->include_script();

		// Delete Compare Category meta
		add_action( 'delete_term', array( $this, 'delete_term' ), 5 );

		// Add form
		add_action( 'product_cat_add_form_fields', array( $this, 'add_category_fields' ) );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_category_fields' ), 10, 2 );
		add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );

		// Add columns
		add_filter( 'manage_edit-product_cat_columns', array( $this, 'product_cat_columns' ) );
		add_filter( 'manage_product_cat_custom_column', array( $this, 'product_cat_column' ), 10, 3 );

	}
	
	/**
	 * Include script and style to show plugin framework for Category page.
	 *
	 */
	public function include_script( ) {
		if ( ! in_array( basename( $_SERVER['PHP_SELF'] ), array( 'edit-tags.php' ) ) ) return;
		if ( ! isset( $_REQUEST['taxonomy'] ) || ! in_array( $_REQUEST['taxonomy'], array( 'product_cat' ) ) ) return;
		
		global $wc_compare_admin_interface;
		add_action( 'admin_footer', array( $wc_compare_admin_interface, 'admin_script_load' ) );
		add_action( 'admin_footer', array( $wc_compare_admin_interface, 'admin_css_load' ) );
		add_action( 'admin_footer', array( $this, 'include_style' ) );
	}
	
	/**
	 * Include script to append the Compare Feature meta fields into Product Attribute page.
	 *
	 */
	public function include_style( ) {
		?>
        <style>
		div.a3rev_panel_container {
			border-top:1px dotted #666;
			border-bottom:1px dotted #666;
			margin-bottom:20px;
		}
		tr.compare-category-field-start {
			border-top:1px dotted #666;
		}
		tr.compare-category-field-end {
			border-bottom:1px dotted #666;
		}
		.a3rev_panel_container label {
			padding: 0 !important;	
		}
		</style>
	<?php
    }

	/**
	 * When a term is deleted, delete its meta.
	 *
	 * @access public
	 * @param mixed $term_id
	 * @return void
	 */
	public function delete_term( $term_id ) {

		$term_id = (int) $term_id;

		if ( ! $term_id )
			return;

		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->woo_compare_categorymeta} WHERE `woo_compare_category_id` = " . $term_id );
	}

	/**
	 * Add Compare Category fields.
	 *
	 * @access public
	 * @return void
	 */
	public function add_category_fields() {
		?>
        <div class="a3rev_panel_container">
            <h3><?php _e( 'Comparison Category', 'woo_cp' ); ?></h3>
            <div class="form-field">
                <input type="hidden" name="have_compare_category_field" value="yes"  />
                <input type="checkbox" class="a3rev-ui-onoff_checkbox" checked="checked" id="is_compare_cat" name="is_compare_cat" value="yes" style="width:auto;a" /> <label for="is_compare_cat"><?php _e( 'ON to activate as a Compare Category', 'woo_cp' ); ?></label>
            </div>
        </div>
		<?php
	}

	/**
	 * Edit Compare Category field.
	 *
	 * @access public
	 * @param mixed $term Term (category) being edited
	 * @param mixed $taxonomy Taxonomy of the term being edited
	 */
	public function edit_category_fields( $term, $taxonomy ) {

		$is_compare_cat = WC_Compare_Functions::get_compare_category_meta( $term->term_id, 'is_compare_cat' );
		?>
        <tr class="form-field compare-category-field-start">
			<th scope="row" valign="top" colspan="2"><h3><?php _e( 'Comparison Category', 'woo_cp' ); ?></h3></th>
		</tr>
		<tr class="form-field a3rev_panel_container compare-category-field-end">
			<th scope="row" valign="top"><label for="is_compare_cat"><?php _e( 'Compare Category', 'woo_cp' ); ?></label></th>
			<td>
            	<input type="hidden" name="have_compare_category_field" value="yes"  />
				<input type="checkbox" class="a3rev-ui-onoff_checkbox" name="is_compare_cat" id="is_compare_cat" value="yes" <?php checked( $is_compare_cat, 'yes' ); ?> style="width:auto;" /><label for="is_compare_cat"><?php _e( 'ON to activate as a Compare Category', 'woo_cp' ); ?></label>
            </td>
		</tr>
		<?php
	}

	/**
	 * save_category_fields function.
	 *
	 * @access public
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param mixed $taxonomy Taxonomy of the term being saved
	 * @return void
	 */
	public function save_category_fields( $term_id, $tt_id, $taxonomy ) {
		if ( isset( $_POST['have_compare_category_field'] ) ) {
			if ( isset( $_POST['is_compare_cat'] ) )
				WC_Compare_Functions::update_compare_category_meta( $term_id, 'is_compare_cat', 'yes' );
			else
				WC_Compare_Functions::update_compare_category_meta( $term_id, 'is_compare_cat', 'no' );
		}

	}

	/**
	 * Compare column added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function product_cat_columns( $columns ) {
		$have_description_column = false;
		$new_columns          = array();
		if ( is_array( $columns ) && count( $columns ) > 0 ) {
			foreach ( $columns as $column_key => $column_name ) {
				$new_columns[$column_key] = $column_name;
				if ( $column_key == 'description' ) {
					$have_description_column = true;
					$new_columns['compare_cat'] = __( 'Compare', 'woo_cp' );
				}
			}
			if ( ! $have_description_column ) $new_columns['compare_cat'] = __( 'Compare', 'woo_cp' );
			$columns = $new_columns;
		}

		return $columns;
	}

	/**
	 * Compare column value added to category admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @param mixed $column
	 * @param mixed $id
	 * @return array
	 */
	public function product_cat_column( $columns, $column, $id ) {

		if ( $column == 'compare_cat' ) {

			$is_compare_cat = WC_Compare_Functions::get_compare_category_meta( $id, 'is_compare_cat' );
			if ( $is_compare_cat === 'yes' )
				$columns .= '<a href="admin.php?page=woo-compare-features&act=view-cat&cat_id='.$id.'">'.__( 'View', 'woo_cp' ).'</a>';

		}

		return $columns;
	}
}

new WC_Admin_Compare_Categories();
