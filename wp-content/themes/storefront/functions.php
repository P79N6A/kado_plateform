<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];


/** 
	mes fonction personnellles
	@author mvondoyannick@gmail.com
**/

add_action('admin_init', 'custom_metabox');
function custom_metabox(){
    add_meta_box('custom_id', 'RAYON BOX', 'custom_metabox_label', 'post', 'side', 'hight');
}

function custom_metabox_label(){
    echo '<input type="text" name="custom_input" id="custom_input" value="" />';
}

//end


//creation d'un custom postype rayons
function kado_custom_post_type(){
	$labels = array(
		// Le nom au pluriel
		'name'                => _x( 'Rayons KADO', 'Post Type General Name'),
		// Le nom au singulier
		'singular_name'       => _x( 'Rayon KADO', 'Post Type Singular Name'),
		// Le libellé affiché dans le menu
		'menu_name'           => __( 'Rayons KADO'),
		// Les différents libellés de l'administration
		'all_items'           => __( 'Tous les rayons'),
		'view_item'           => __( 'Voir les rayons'),
		'add_new_item'        => __( 'Ajouter un rayon'),
		'add_new'             => __( 'Ajouter'),
		'edit_item'           => __( 'Editer un rayon'),
		'update_item'         => __( 'Modifier un rayon'),
		'search_items'        => __( 'Rechercher un rayon'),
		'not_found'           => __( 'Non trouvée'),
		'not_found_in_trash'  => __( 'Non trouvée dans la corbeille'),
	);


	// On peut définir ici d'autres options pour notre custom post type
	
	$args = array(
		'label'               => __( 'Rayons KADO'),
		'description'         => __( 'Tous sur les rayons'),
		'labels'              => $labels,
		// On définit les options disponibles dans l'éditeur de notre custom post type ( un titre, un auteur...)
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
		/* 
		* Différentes options supplémentaires
		*/	
		'hierarchical'        => false,
		'public'              => true,
		'has_archive'         => true,
		'rewrite'			  => array( 'slug' => 'rayons-kado'),

	);
	
	// On enregistre notre custom post type qu'on nomme ici "serietv" et ses arguments
	register_post_type( 'rayonskado', $args );
}

add_action( 'init', 'kado_custom_post_type', 0 );


//other adding


// Add the custom field "favorite_color"
add_action( 'woocommerce_edit_account_form', 'add_favorite_color_to_edit_account_form' );
function add_favorite_color_to_edit_account_form() {
    $user = wp_get_current_user();
    ?>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="favorite_color"><?php _e( 'Carte de fidelité', 'woocommerce' ); ?>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="favorite_color" id="favorite_color" placeholder="N° Téléphone carte fidelité" value="<?php echo esc_attr( $user->favorite_color ); ?>" />
    </p>
    <?php
}

//ajout d'une nouvelle ligne
/**
 * @snippet       Add Row to Order Totals Table - WooCommerce
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=21589
 * @author        Rodolfo Melogli
 * @compatible    WC 2.6.14, WP 4.7.2, PHP 5.5.9
 */

//ajout de la ligne de point dans la carte de fidelité de user
add_action('woocommerce_edit_account_form', 'add_customer_point');
function add_customer_point(){
    $user = wp_get_current_user();
    ?>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="fid_point"><?php _e('Point de fidelité cumulés', 'woocommerce' ); ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="fid_point" id="fid_point" value="<?php echo esc_attr($user->fidelity_point) ?>" readonly>
        </p>
    <?php
}
 
//ajout d'une ligne supplementaire dans le tableau
add_filter( 'woocommerce_get_order_item_totals', 'bbloomer_add_recurring_row_email', 10, 2 );
 
function bbloomer_add_recurring_row_email( $total_rows, $myorder_obj ) {
 $user = wp_get_current_user();
$total_rows['recurr_not'] = array(
    'label' => __( 'Numero carte fidelité', 'woocommerce' ),
    'value' => $user->favorite_color
);
 
return $total_rows;
}


//reduction suivant la carte de fidelité

function prefix_add_discount_line( $cart ) {

  //$discount = $cart->subtotal * 0.1;
    //$user = wp_get_current_user();

    //obtention des points de l'utilisateur
    $point = $user->fidelity_point;

  if (WC()->cart->cart_contents_total >= '60000'){
  	if (0 == $user->ID){
  		$user = wp_get_current_user();
  		$discount = $cart->subtotal * $user->rabaissement;
  	}
  }
  else {
  	$discount = 0;
  }

  $cart->add_fee( __( 'Réduction carte fidélité', 202, true, '' ) , -$discount );

}
add_action( 'woocommerce_cart_calculate_fees', 'prefix_add_discount_line' );

// Save the custom field 'favorite_color' 
add_action( 'woocommerce_save_account_details', 'save_favorite_color_account_details', 12, 1 );
function save_favorite_color_account_details( $user_id ) {
    // For Favorite color
    if( isset( $_POST['favorite_color'] ) )
        update_user_meta( $user_id, 'favorite_color', sanitize_text_field( $_POST['favorite_color'] ) );

    // For Billing email (added related to your comment)
    if( isset( $_POST['account_email'] ) )
        update_user_meta( $user_id, 'billing_email', sanitize_text_field( $_POST['account_email'] ) );
}


//add woocommer percent phone detail field
add_action('woocommerce_edit_account_form', 'add_rabaissement_account_detail');
function add_rabaissement_account_detail(){
	$user = wp_get_current_user();
	?>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="rabaissement"><?php _e( 'Pourcentage actuel', 'woocommerce' ); ?>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="rabaissement" id="rabaissement" placeholder="% rabaissement fidelité" value="<?php echo esc_attr( $user->rabaissement ); ?>" readonly />
    </p>
	<?php
}


//add woo residence detail
add_action('woocommerce_edit_account_form', 'add_residence_account_detail');
function add_residence_account_detail(){
	$user = wp_get_current_user();
	?>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="residence"><?php _e( 'Rédidence', 'woocommerce' ); ?>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="residence" id="residence" placeholder="Votre résidence" value="<?php echo esc_attr( $user->residence ); ?>" />
	<?php
}

//save woocommerce residence field
/*add_action('woocommerce_save_account_details', 'save_residence_account_detail');
function save_residence_account_detail( $user_id ){
	if (isset($_POST['residence'])) {
		update_user_meta( $user_id, 'residence', sanitize_text_field( $_POST['residence']) );
	}
}*/

//envoyer un SMS au client lorsque le paiement est terminé
add_action('woocommerce_payment_complete', 'send_user_sms');
function send_user_sms(){
    $user = wp_get_current_user();
    //$url='https://www.agis-as.com/epolice/index.php?telephone='.$user->phone.'&message=bonjour test';
    //file_get_contents($url);
    http_get("https://www.agis-as.com/epolice/index.php?telephone=691451189&message=send from ecommerce bonjour test", 0, $info);
}

//select box user genre
add_action( 'woocommerce_edit_account_form', 'bbloomer_extra_register_select_field' );
 
function bbloomer_extra_register_select_field() {
	$user = wp_get_current_user();
	  ?>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="genre"><?php _e( 'Genre ('.$user->genre.')', 'woocommerce' ); ?>  </label>
			<select name="genre" id="genre" />
		    <option value="Feminin">Feminin</option>
		    <option value="Masculin">Masculin</option>
			</select>
			</p>
			 
		<?php 
}



//add woo profession detail to user
add_action('woocommerce_edit_account_form', 'add_profession_account_detail');
function add_profession_account_detail(){
	$user = wp_get_current_user();
	?>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="profession"><?php _e( 'Profession', 'woocommerce' ); ?>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="profession" id="profession" placeholder="Votre profession" value="<?php echo esc_attr( $user->profession ); ?>" required />
	<?php
}

//enregistrement de la profession au profile de l'utilisateur
/**
@detail permet de sauvegarder les informations apportés sur un utilisateur concernant sa professions
**/
/*add_action('woocommerce_save_account_details', 'save_profession_account_detail');
function save_profession_account_detail($user_id){
	if (isset($_POST['profession'])) {
		update_user_meta( $user_id, 'profession', sanitize_text_field( $_POST['profession']) );
	}
}*/

//ceci esy in test
add_action('woocommerce_after_checkout_billing_form', 'after_form');
function after_form(){
	?>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="time"><?php _e( 'Votre commande sera disponible à partir du ', 'woocommerce' ); ?>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="time" id="time" placeholder="Heure de retrait" value="<?php echo date("d M Y - H:i:s", strtotime('+2 hours')); ?>" readonly />
		</p>
	<?php
}

// Save the custom field 'rabaissement' 
add_action( 'woocommerce_save_account_details', 'save_rabaissement_account_details', 12, 1 );
function save_rabaissement_account_details( $user_id ) {
    // For Favorite color
    if( isset( $_POST['rabaissement'] ) )
      update_user_meta( $user_id, 'rabaissement', sanitize_text_field( $_POST['rabaissement'] ) );

    // For Billing email (added related to your comment)
    if( isset( $_POST['account_email'] ) )
      update_user_meta( $user_id, 'billing_email', sanitize_text_field( $_POST['account_email'] ) );

    //permet d'enregistrer la profession
    if (isset($_POST['profession'])) {
			update_user_meta( $user_id, 'profession', sanitize_text_field( $_POST['profession']) );
		}

		//permet d'enregistrer la residence
		if (isset($_POST['residence'])) {
			update_user_meta( $user_id, 'residence', sanitize_text_field( $_POST['residence']) );
		}

		//permet d'enregistrer le genre
		if (isset($_POST['genre'])) {
		update_user_meta( $user_id, 'genre', sanitize_text_field( $_POST['genre']) );
	}
}
/*  fin des fonctions personeele*/
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';

	if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
		require 'inc/nux/class-storefront-nux-starter-content.php';
	}
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */
