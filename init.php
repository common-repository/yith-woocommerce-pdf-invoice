<?php
/**
 * Plugin Name: YITH WooCommerce PDF Invoice and Shipping List
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-pdf-invoice/
 * Description: <code><strong>YITH WooCommerce PDF Invoice and Shipping List</strong></code> generate PDF invoices, credit notes, pro-forma invoice and packing slip for WooCommerce orders. Set manual or automatic invoice generation, fully customizable document template and sync with your DropBox account. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 1.3.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-pdf-invoice
 * Domain Path: /languages/
 * WC requires at least: 5.3
 * WC tested up to: 5.7
 *
 * @package YITH\PDFInvoice
 **/

/*
Copyright 2018  Your Inspiration Themes  (email : plugins@yithemes.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Print a notice if WooCommerce is not installed.
 */
function yith_ywpi_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'YITH WooCommerce PDF Invoice and Shipping List is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-pdf-invoice' ); ?></p>
	</div>
	<?php
}

/**
 * Print a notice if the premium version is activated.
 */
function yith_ywpi_install_free_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'You can\'t activate the free version of YITH WooCommerce PDF Invoice and Shipping List while you are using the premium one.', 'yith-woocommerce-pdf-invoice' ); ?></p>
	</div>
	<?php
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

// region    ****    Define constants.

if ( ! defined( 'YITH_YWPI_FREE_INIT' ) ) {
	define( 'YITH_YWPI_FREE_INIT', plugin_basename( __FILE__ ) );
}

defined( 'YITH_YWPI_SLUG' ) || define( 'YITH_YWPI_SLUG', 'yith-woocommerce-pdf-invoice' );

if ( ! defined( 'YITH_YWPI_VERSION' ) ) {
	define( 'YITH_YWPI_VERSION', '1.3.0' );
}

if ( ! defined( 'YITH_YWPI_FILE' ) ) {
	define( 'YITH_YWPI_FILE', __FILE__ );
}

if ( ! defined( 'YITH_YWPI_DIR' ) ) {
	define( 'YITH_YWPI_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_YWPI_URL' ) ) {
	define( 'YITH_YWPI_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YITH_YWPI_ASSETS_URL' ) ) {
	define( 'YITH_YWPI_ASSETS_URL', YITH_YWPI_URL . 'assets' );
}

if ( ! defined( 'YITH_YWPI_TEMPLATE_DIR' ) ) {
	define( 'YITH_YWPI_TEMPLATE_DIR', YITH_YWPI_DIR . 'templates' );
}

if ( ! defined( 'YITH_YWPI_INVOICE_TEMPLATE_URL' ) ) {
	define( 'YITH_YWPI_INVOICE_TEMPLATE_URL', YITH_YWPI_URL . 'templates/invoice/' );
}

if ( ! defined( 'YITH_YWPI_INVOICE_TEMPLATE_DIR' ) ) {
	define( 'YITH_YWPI_INVOICE_TEMPLATE_DIR', YITH_YWPI_DIR . 'templates/invoice/' );
}

if ( ! defined( 'YITH_YWPI_ASSETS_IMAGES_URL' ) ) {
	define( 'YITH_YWPI_ASSETS_IMAGES_URL', YITH_YWPI_ASSETS_URL . '/images/' );
}

if ( ! defined( 'YITH_YWPI_LIB_DIR' ) ) {
	define( 'YITH_YWPI_LIB_DIR', YITH_YWPI_DIR . 'lib/' );
}

if ( ! defined( 'YITH_YWPI_DOMPDF_DIR' ) ) {
	define( 'YITH_YWPI_DOMPDF_DIR', YITH_YWPI_LIB_DIR . 'dompdf/' );
}

$wp_upload_dir = wp_upload_dir();

if ( ! defined( 'YITH_YWPI_DOCUMENT_SAVE_DIR' ) ) {
	define( 'YITH_YWPI_DOCUMENT_SAVE_DIR', $wp_upload_dir['basedir'] . '/ywpi-pdf-invoice/' );
}

if ( ! defined( 'YITH_YWPI_SAVE_INVOICE_URL' ) ) {
	define( 'YITH_YWPI_SAVE_INVOICE_URL', $wp_upload_dir['baseurl'] . '/ywpi-pdf-invoice/' );
}

// endregion.

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWPI_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_YWPI_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_YWPI_DIR );

/**
 * Load text domain and start plugin
 */
function yith_ywpi_init() {
	load_plugin_textdomain( 'yith-woocommerce-pdf-invoice', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once YITH_YWPI_LIB_DIR . 'class.yith-ywpi-plugin-fw-loader.php';
	require_once YITH_YWPI_LIB_DIR . 'class.yith-woocommerce-pdf-invoice.php';
	require_once YITH_YWPI_LIB_DIR . 'class.yith-document.php';
	require_once YITH_YWPI_LIB_DIR . 'class.yith-invoice.php';
	require_once YITH_YWPI_LIB_DIR . 'class.yith-shipping.php';
	require_once YITH_YWPI_DIR . 'functions.php';

	YITH_YWPI_Plugin_FW_Loader::get_instance();

	global $YWPI_Instance; // phpcs:ignore
	$YWPI_Instance = new YITH_WooCommerce_Pdf_Invoice(); // phpcs:ignore
}

add_action( 'yith_ywpi_init', 'yith_ywpi_init' );

if ( ! function_exists( 'yith_ywpi_protect_folder' ) ) {
	/**
	 * Create files/directories to protect upload folders
	 */
	function yith_ywpi_protect_folder() {

		$files = array(
			array(
				'base'    => YITH_YWPI_DOCUMENT_SAVE_DIR,
				'file'    => 'index.html',
				'content' => '',
			),
			array(
				'base'    => YITH_YWPI_DOCUMENT_SAVE_DIR,
				'file'    => '.htaccess',
				'content' => 'deny from all',
			),
		);

		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				// phpcs:disable
				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
				// phpcs:enable
			}
		}

		// Updating the option not to execute the function 'yith_ywpi_protect_folder' again.
		update_option( 'yith_wc_pdf_invoice_check_folder_already_protected', true );

	}
}

/**
 * Check WC installation
 */
function yith_ywpi_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_ywpi_install_woocommerce_admin_notice' );
	} elseif ( defined( 'YITH_YWPI_PREMIUM' ) ) {
		add_action( 'admin_notices', 'yith_ywpi_install_free_admin_notice' );
		deactivate_plugins( plugin_basename( __FILE__ ) );
	} else {
		do_action( 'yith_ywpi_init' );
	}

	if ( ! get_option( 'yith_wc_pdf_invoice_check_folder_already_protected' ) ) {
		yith_ywpi_protect_folder();
	}

}

add_action( 'plugins_loaded', 'yith_ywpi_install', 11 );
