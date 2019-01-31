<?php
/**
 * Plugin Name:        Storefront Pricing Tables
 * Plugin URI:         https://woocommerce.com/products/storefront-pricing-tables/
 * Description:        Add attractive pricing tables to your posts and pages.
 * Version:            1.1.0
 * Author:             WooCommerce
 * Author URI:         https://woocommerce.com/
 * Requires at least:  4.0.0
 * Tested up to:       4.9.3
 *
 * Text Domain: storefront-pricing-tables
 * Domain Path: /languages/
 *
 * @package Storefront_Pricing_Tables
 * @category Core
 * @author James Koster
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Sold On Woo - Start
/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '1288747890506a7bee024d42112d84c8', '754239' );
// Sold On Woo - End

/**
 * Returns the main instance of Storefront_Pricing_Tables to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Storefront_Pricing_Tables
 */
function Storefront_Pricing_Tables() {
	return Storefront_Pricing_Tables::instance();
} // End Storefront_Pricing_Tables()

Storefront_Pricing_Tables();

/**
 * Main Storefront_Pricing_Tables Class
 *
 * @class Storefront_Pricing_Tables
 * @version	1.0.0
 * @since 1.0.0
 * @package	Storefront_Pricing_Tables
 */
final class Storefront_Pricing_Tables {
	/**
	 * Storefront_Pricing_Tables The single instance of Storefront_Pricing_Tables.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->token 			= 'storefront-pricing-tables';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.1.0';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'spt_load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'spt_setup' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'spt_plugin_links' ) );

		/**
		 * Custom Classes
		 */
		require_once dirname( __FILE__ ) . '/includes/class-storefront-pricing-tables-shortcode-generator.php';
	}

	/**
	 * Main Storefront_Pricing_Tables Instance
	 *
	 * Ensures only one instance of Storefront_Pricing_Tables is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Storefront_Pricing_Tables()
	 * @return Main Storefront_Pricing_Tables instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function spt_load_plugin_textdomain() {
		load_plugin_textdomain( 'storefront-pricing-tables', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Plugin page links
	 *
	 * @since  1.0.0
	 */
	public function spt_plugin_links( $links ) {
		$plugin_links = array(
			'<a href="https://woocommerce.com/contact-us/">' . __( 'Support', 'storefront-pricing-tables' ) . '</a>',
			'<a href="https://docs.woocommerce.com/document/storefront-pricing-tables/">' . __( 'Docs', 'storefront-pricing-tables' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();

		// get theme customizer url
		$url = admin_url() . 'customize.php?';
		$url .= 'url=' . urlencode( site_url() . '?storefront-customizer=true' ) ;
		$url .= '&return=' . urlencode( admin_url() . 'plugins.php' );
		$url .= '&storefront-customizer=true';

		$notices 		= get_option( 'spt_activation_notice', array() );
		$notices[]		= sprintf( __( '%sThanks for installing the Storefront Pricing Tables extension. To get started, visit the %sCustomizer%s.%s %sOpen the Customizer%s', 'storefront-pricing-tables' ), '<p>', '<a href="' . esc_url( $url ) . '">', '</a>', '</p>', '<p><a href="' . esc_url( $url ) . '" class="button button-primary">', '</a></p>' );

		update_option( 'spt_activation_notice', $notices );
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Setup all the things.
	 * Only executes if Storefront or a child theme using Storefront as a parent is active and the extension specific filter returns true.
	 * Child themes can disable this extension using the storefront_extension_boilerplate_enabled filter
	 * @return void
	 */
	public function spt_setup() {
		$theme = wp_get_theme();

		if ( 'Storefront' == $theme->name || 'storefront' == $theme->template && apply_filters( 'storefront_extension_boilerplate_supported', true ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'spt_styles' ), 999 );
			add_action( 'customize_register', array( $this, 'spt_customize_register' ) );
			add_action( 'customize_preview_init', array( $this, 'spt_customize_preview_js' ) );
			add_filter( 'body_class', array( $this, 'spt_body_class' ) );
			add_action( 'admin_notices', array( $this, 'spt_customizer_notice' ) );

			// Hide the 'More' section in the customizer
			add_filter( 'storefront_customizer_more', '__return_false' );

			// Setup the shortcodes
			add_shortcode( 'pricing_column', array( $this, 'spt_column' ) );
			add_shortcode( 'pricing_table', array( $this, 'spt_pricing_table' ) );
		}
	}

	/**
	 * Admin notice
	 * Checks the notice setup in install(). If it exists display it then delete the option so it's not displayed again.
	 * @since   1.0.0
	 * @return  void
	 */
	public function spt_customizer_notice() {
		$notices = get_option( 'spt_activation_notice' );

		if ( $notices = get_option( 'spt_activation_notice' ) ) {

			foreach ( $notices as $notice ) {
				echo '<div class="updated">' . $notice . '</div>';
			}

			delete_option( 'spt_activation_notice' );
		}
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function spt_customize_register( $wp_customize ) {

		/**
		 * Custom controls
		 * Load custom control classes
		 */
		require_once dirname( __FILE__ ) . '/includes/class-storefront-pricing-tables-images-control.php';

		/**
	     * Add a new section
	     */
        $wp_customize->add_section( 'spt_section' , array(
		    'title'      	=> __( 'Pricing Tables', 'storefront-extention-boilerplate' ),
		    'priority'   	=> 55,
		) );

		/**
		 * Image selector radios
		 * See class-control-images.php
		 */
		$wp_customize->add_setting( 'spt_alignment', array(
			'default'    		=> 'left',
			'sanitize_callback'	=> 'esc_attr'
		) );

		$wp_customize->add_control( new Storefront_Pricing_Tables_Layout_Control( $wp_customize, 'spt_alignment', array(
			'label'    => __( 'Content alignment', 'storefront' ),
			'section'  => 'spt_section',
			'settings' => 'spt_alignment',
			'priority' => 10,
		) ) );

		/**
		 * Add a divider.
		 * Type can be set to 'text' or 'heading' to display a title or description.
		 */
		if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
			$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'spt_divider', array(
				'section'  	=> 'spt_section',
				'type'		=> 'divider',
				'priority' 	=> 15,
			) ) );
		}

		/**
		 * Colors
		 */
		$wp_customize->add_setting( 'spt_header_background_color', array(
			'default'			=> apply_filters( 'spt_default_header_background', '#2c2d33' ),
			'sanitize_callback'	=> 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'spt_header_background_color', array(
			'label'			=> __( 'Headings', 'storefront-pricing-tables' ),
			'description'	=> __( 'Column header background color', 'storefront-pricing-tables' ),
			'section'		=> 'spt_section',
			'settings'		=> 'spt_header_background_color',
			'priority'		=> 30,
		) ) );

		$wp_customize->add_setting( 'spt_header_text_color', array(
			'default'			=> apply_filters( 'spt_default_header_text_color', '#ffffff' ),
			'sanitize_callback'	=> 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'spt_header_text_color', array(
			'description'	=> __( 'Column header text color', 'storefront-pricing-tables' ),
			'section'		=> 'spt_section',
			'settings'		=> 'spt_header_text_color',
			'priority'		=> 40,
		) ) );

		$wp_customize->add_setting( 'spt_header_highlight_background_color', array(
			'default'			=> apply_filters( 'spt_default_header_highlight_background_color', '#96588a' ),
			'sanitize_callback'	=> 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'spt_header_highlight_background_color', array(
			'description'	=> __( 'Highlighted column header background color', 'storefront-pricing-tables' ),
			'section'		=> 'spt_section',
			'settings'		=> 'spt_header_highlight_background_color',
			'priority'		=> 50,
		) ) );

		$wp_customize->add_setting( 'spt_header_highlight_text_color', array(
			'default'			=> apply_filters( 'spt_default_header_highlight_text_color', '#ffffff' ),
			'sanitize_callback'	=> 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'spt_header_highlight_text_color', array(
			'description'	=> __( 'Highlighted column header text color', 'storefront-pricing-tables' ),
			'section'		=> 'spt_section',
			'settings'		=> 'spt_header_highlight_text_color',
			'priority'		=> 60,
		) ) );

		/**
		 * Pricin table columns
		 */
		$wp_customize->add_setting( 'spt_columns', array(
			'default' 			=> '3',
			'sanitize_callback'	=> 'storefront_sanitize_choices',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'spt_columns', array(
			'label'			=> __( 'Pricing table columns', 'storefront-pricing-tables' ),
			'section'		=> 'spt_section',
			'settings'		=> 'spt_columns',
			'type'			=> 'select', // To add a radio control, switch this to 'radio'.
			'priority'		=> 70,
			'choices'		=> array(
				'2'		=> '2',
				'3'		=> '3',
				'4'		=> '4',
				'5'		=> '5',
				'6'		=> '6',
			),
		) ) );
	}

	/**
	 * Enqueue CSS and custom styles.
	 * @since   1.0.0
	 * @return  void
	 */
	public function spt_styles() {
		wp_enqueue_style( 'spt-styles', plugins_url( '/assets/css/style.css', __FILE__ ) );

		$header_background_color 			= get_theme_mod( 'spt_header_background_color', apply_filters( 'spt_default_header_background', '#2c2d33' ) );
		$header_text_color 					= get_theme_mod( 'spt_header_text_color', apply_filters( 'spt_default_header_text_color', '#ffffff' ) );
		$header_highlight_background_color 	= get_theme_mod( 'spt_header_highlight_background_color', apply_filters( 'spt_default_header_highlight_background_color', '#96588a' ) );
		$header_highlight_text_color 		= get_theme_mod( 'spt_header_highlight_text_color', apply_filters( 'spt_default_header_highlight_text_color', '#ffffff' ) );

		$spt_style = '
		.storefront-pricing-column h2.column-title {
			background-color: ' . $header_background_color . ';
			color: ' . $header_text_color . ';
		}

		.storefront-pricing-column.highlight h2.column-title {
			background-color: ' . $header_highlight_background_color . ';
			color: ' . $header_highlight_text_color . ';
		}';

		wp_add_inline_style( 'spt-styles', $spt_style );
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 *
	 * @since  1.0.0
	 */
	public function spt_customize_preview_js() {
		wp_enqueue_script( 'spt-customizer', plugins_url( '/assets/js/customizer.min.js', __FILE__ ), array( 'customize-preview' ), '1.1', true );
	}

	/**
	 * Storefront Pricing Tables Body Class
	 * Adds a class based on the extension name and any relevant settings.
	 */
	public function spt_body_class( $classes ) {
		$classes[] = 'storefront-pricing-tables-active';

		return $classes;
	}

	/**
	 * Build a list from an array
	 */
	function spt_build_list( $features ) {
		$items = explode( '|', $features );

		echo '<ul class="features">';
	    foreach ( $items as $item ) {
	        echo '<li>' . $item . '</li>';
	    }
	    echo '</ul>';
	}

	/**
	 * Display pricing table wrapper
	 */
	public function spt_pricing_table( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'columns'		=> '',
			'alignment'		=> '',
		), $atts ) );

		if ( '' == $columns ) {
			$columns = get_theme_mod( 'spt_columns', '3' );
		}

		if ( '' == $alignment ) {
			$alignment = get_theme_mod( 'spt_alignment', 'left' );
		}

		return '<div class="storefront-pricing-table align-' . esc_attr( $alignment ). ' columns-' . esc_attr( $columns ) . '">' . do_shortcode( $content ) . '</div>';
	}

	/**
	 * Display pricing table column
	 */
	public function spt_column( $atts ) {
		extract( shortcode_atts( array(
			'title'			=> '',
			'id'			=> '',
			'features'		=> '',
			'highlight'		=> '',
			'image'			=> 'true',
		), $atts ) );

		$product = wc_get_product( $id );

		if ( ! $product ) {
			return;
		}

		if ( $title ) {
			$title_output = $title;
		} elseif ( ! $title && $id ) {
			$title_output = $product->get_title();
		} else {
			$title_output = '';
		}

		if ( 'true' == $highlight ) {
			$highlight_class = 'highlight';
		} else {
			$highlight_class = '';
		}

		if ( 'true' == $image && $id ) {
			$image_output = $product->get_image( 'shop_single' );
		} elseif ( 'false' == $image ) {
			$image_output = '';
		} else {
			$image_output = '<img src="' . $image . '" alt="' . $title_output . '" />';
		}

		ob_start();

		?>
		<div class="storefront-pricing-column <?php echo $highlight_class; ?>">
			<?php if ( '' != $title_output ) { ?>
				<h2 class="column-title"><?php echo esc_attr( $title_output ); ?></h2>
			<?php } ?>

			<?php
				echo wp_kses_post( $image_output );
				Storefront_Pricing_Tables::spt_build_list( $features );
				echo do_shortcode( '[add_to_cart id="' . $id . '"]' );
			?>
		</div>
		<?php

		return ob_get_clean();
	}


} // End Class