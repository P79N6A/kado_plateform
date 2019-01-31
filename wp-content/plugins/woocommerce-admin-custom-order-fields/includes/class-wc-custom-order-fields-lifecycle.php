<?php
/**
 * WooCommerce Admin Custom Order Fields
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Admin Custom Order Fields to newer
 * versions in the future. If you wish to customize WooCommerce Admin Custom Order Fields for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-admin-custom-order-fields/ for more information.
 *
 * @package     WC-Admin-Custom-Order-Fields/Classes
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2018, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Admin_Custom_Order_Fields;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_3_0 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.11.0
 *
 * @method \WC_Admin_Custom_Order_Fields get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Handles default settings for new installs.
	 *
	 * @since 1.11.0
	 */
	protected function install() {

		add_option( 'wc_admin_custom_order_fields_next_field_id', 1 );
		add_option( 'wc_admin_custom_order_fields_welcome', 1 );
	}


	/**
	 * Handles plugin version upgrades.
	 *
	 * @since 1.11.0
	 *
	 * @param string $installed_version the currently installed version
	 */
	protected function upgrade( $installed_version ) {

		// upgrade to 1.1
		if ( version_compare( $installed_version, '1.1', '<' ) ) {

			delete_option( 'wc_admin_custom_order_fields_welcome' );

			$this->get_plugin()->log( 'Updated to version 1.1' );
		}
	}


}
