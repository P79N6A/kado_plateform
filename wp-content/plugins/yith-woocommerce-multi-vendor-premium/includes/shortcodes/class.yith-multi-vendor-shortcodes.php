<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WPV_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Multi_Vendor_Shortcodes
 * @package    Yithemes
 * @since      Version 2.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Multi_Vendor_Shortcodes' ) ) {
    /**
     * Class YITH_Multi_Vendor_Shortcodes
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     */
    class YITH_Multi_Vendor_Shortcodes {

        /**
         * Add Shortcodes
         *
         * @return void
         * @since  1.7
         * @author andrea Grillo <andrea.grillo@yithemes.com>
         */
        public static function load() {
            $shortcodes = array(
                'yith_wcmv_list'            => 'YITH_Multi_Vendor_Shortcodes::vendors_list',
                'yith_wcmv_become_a_vendor' => 'YITH_Multi_Vendor_Shortcodes::become_a_vendor',
                'yith_wcmv_vendor_name'     => 'YITH_Multi_Vendor_Shortcodes::vendor_name'
            );



            foreach ( $shortcodes as $shortcode => $callback ) {
                add_shortcode( $shortcode, $callback );
            }
        }

        /**
         * Print vendors list shortcodes
         *
         * @param array $sc_args The Shortcode args
         *
         * @return mixed ob_get_clean();
         * @since  1.7
         * @author andrea Grillo <andrea.grillo@yithemes.com>
         */
        public static function vendors_list( $sc_args = array() ) {
            $default = array(
                'per_page'                => -1,
                'hide_no_products_vendor' => 'false',
                'show_description'        => 'false',
                'description_lenght'      => 40,
                'vendor_image'            => 'store',
                'orderby'                 => 'name', //Allowed values: 'name', 'slug', 'term_group', 'term_id', 'id', 'description'
                'order'                   => 'ASC'   //Allowed values: ASC, DESC
            );

            if( isset( $sc_args['hide_no_products_vendor'] ) ){
	            $sc_args['hide_no_products_vendor'] = 'true' == $sc_args['hide_no_products_vendor'];
            }

            $sc_args      = wp_parse_args( $sc_args, $default );
            $vendors_args = array( 'enabled_selling' => true, 'order' => $sc_args['order'], 'orderby' => $sc_args['orderby'] );
            $paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
            $per_page     = intval( $sc_args['per_page'] );
            $total        = ceil( count( YITH_Vendors()->get_vendors( array( 'enabled_selling' => true, 'fields' => 'ids' ) ) ) / $per_page );
            $per_page     = -1 == $per_page ? 0 : $per_page;

            if ( ! empty( $sc_args['per_page'] ) ) {
                $pagination_args = array(
                    'pagination' => array(
                        'offset' => ( $paged - 1 ) * absint( $sc_args['per_page'] ),
                        'number' => $per_page,
                        'type'   => 'list'
                    )
                );
                $vendors_args = array_merge( $vendors_args, $pagination_args );
            }

            $vendors = YITH_Vendors()->get_vendors( $vendors_args );

            if ( empty( $vendors ) ) {
                return false;
            }

            $args = array(
                'vendors'          => $vendors,
                'paginate'         => array(
                    'current' => $paged,
                    'total'   => $total,
                ),
                'show_total_sales' => 'yes' == get_option( 'yith_wpv_vendor_total_sales' ) ? true : false,
                'sc_args'          => $sc_args,
                'icons'            => yith_wcmv_get_font_awesome_icons(),
                'socials_list'     => YITH_Vendors()->get_social_fields(),
            );
            ob_start();
            yith_wcpv_get_template( 'vendors-list', $args, 'shortcodes' );
            return ob_get_clean();
        }

	    /*
		 * Show vendor name
		 *
		 * @param array $sc_args The Shortcode args
		 *
		 * @return void
		 * @since  2.2.3
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
	    public static function become_a_vendor( $sc_args = array() ) {
		    $user   = wp_get_current_user();
		    $vendor = yith_get_vendor( $user->ID, 'user' );

		    $is_customer_or_subscriber = in_array( 'subscriber', $user->roles ) || in_array( 'customer', $user->roles ) ;
		    $have_no_roles = empty( $user->roles );

		    ob_start();

		    if ( is_user_logged_in() && ( ( ! $vendor->is_valid() && ( $is_customer_or_subscriber || $have_no_roles ) ) || $vendor->is_super_user() ) ) {
			    $args = array(
				    'is_vat_require'                  => YITH_Vendors()->is_vat_require(),
				    'is_terms_and_conditions_require' => YITH_Vendors()->is_terms_and_conditions_require(),
				    'is_paypal_email_required'        => YITH_Vendors()->is_paypal_email_required(),
				    'is_paypal_email_enabled'         => YITH_Vendors()->is_paypal_email_enabled(),
				    'become_a_vendor_label'           => apply_filters( 'yith_wcmv_become_a_vendor_button_label', esc_attr__( 'Become a vendor', 'yith-woocommerce-product-vendors' ) )
			    );
			    yith_wcpv_get_template( 'become-a-vendor', $args, 'shortcodes' );
		    } else {
			    echo do_shortcode( '[woocommerce_my_account]' );
		    }

		    return ob_get_clean();
	    }


	    /*
		 * Show vendor name
		 *
		 * @param array $sc_args The Shortcode args
		 *
		 * @return void
		 * @since  2.2.3
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
	    public static function vendor_name( $sc_args = array() ) {
		    $default = array(
			    'show_by' => 'vendor',
			    'value'   => 0,
			    'type'    => 'link',
		    );

		    $sc_args = wp_parse_args( $sc_args, $default );

		    $vendor = yith_get_vendor( $sc_args['value'], $sc_args['show_by'] );

		    ob_start();

		    if ( $vendor->is_valid() ) {
			    $use_link = 'link' == $sc_args['type'];
			    ?>
			    <span class="by-vendor-name">
		            <?php if( $use_link ) : ?>
				    <a class="by-vendor-name-link" href="<?php echo $vendor->get_url() ?>">
				    <?php endif; ?>

				    <?php echo $vendor->name ?>

				    <?php if( $use_link ) : ?>
				        </a>
			    <?php endif; ?>
				</span>
			    <br>
			    <?php
		    }

		    return ob_get_clean();
	    }
    }
}
