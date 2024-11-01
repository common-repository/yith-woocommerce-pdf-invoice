<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Class YITH_YWPI_Plugin_FW_Loader
 *
 * @package YITH\PDFInvoice
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_YWPI_Plugin_FW_Loader' ) ) {

	/**
	 * Implements features related to an invoice document
	 *
	 * @class YITH_YWPI_Plugin_FW_Loader
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_YWPI_Plugin_FW_Loader {

		/**
		 * Panel Object
		 *
		 * @var object
		 */
		protected $panel;

		/**
		 * Premium tab template file name
		 *
		 * @var string
		 */
		protected $premium = 'premium.php';

		/**
		 * Premium version landing link
		 *
		 * @var string
		 */
		protected $premium_landing_url = 'http://yithemes.com/themes/plugins/yith-woocommerce-pdf-invoice/';

		/**
		 * Plugin official documentation
		 *
		 * @var string
		 */
		protected $official_documentation = 'http://yithemes.com/docs-plugins/yith-woocommerce-pdf-invoice/';

		/**
		 * YITH WooCommerce PDF Invoice panel page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith_woocommerce_pdf_invoice_panel';

		/**
		 * Single instance of the class
		 *
		 * @var YITH_YWPI_Plugin_FW_Loader
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			/**
			 * Register actions and filters to be used for creating an entry on YIT Plugin menu
			 */
			add_action( 'admin_init', array( $this, 'register_pointer' ) );

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			// Add stylesheets and scripts files.
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			// Show plugin premium tab.
			add_action( 'yith_pdf_invoice_premium', array( $this, 'premium_tab' ) );

			/**
			 * Register plugin to licence/update system
			 */
			$this->licence_activation();
		}


		/**
		 * Load YIT core plugin
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			if ( defined( 'YITH_YWPI_PREMIUM' ) ) {
				$admin_tabs['general'] = esc_html__( 'General', 'yith-woocommerce-pdf-invoice' );
			}

			$admin_tabs['documents'] = esc_html__( 'Documents', 'yith-woocommerce-pdf-invoice' );
			$admin_tabs['template']  = esc_html__( 'Template', 'yith-woocommerce-pdf-invoice' );

			if ( ! defined( 'YITH_YWPI_PREMIUM' ) ) {
				$admin_tabs['premium'] = esc_html__( 'Premium Version', 'yith-woocommerce-pdf-invoice' );
			}

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'plugin_slug'      => YITH_YWPI_SLUG,
				'page_title'       => 'Pdf Invoice',
				'menu_title'       => 'Pdf Invoice',
				'capability'       => apply_filters( 'yith_ywpi_settings_panel_capability', 'manage_options' ),
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_YWPI_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),

			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {

				require_once 'plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );

			if ( defined( 'YITH_YWPI_VERSION' ) ) {
				add_action( 'woocommerce_admin_field_ywpi_logo', array( $this->panel, 'yit_upload' ), 10, 1 );
			}
		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_YWPI_TEMPLATE_DIR . '/admin/' . $this->premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once $premium_tab_template;
			}
		}

		/**
		 * Register pointer
		 */
		public function register_pointer() {
			if ( ! class_exists( 'YIT_Pointers' ) ) {
				include_once 'plugin-fw/lib/yit-pointers.php';
			}

			$premium_message = defined( 'YITH_YWPI_PREMIUM' )
				? ''
				: esc_html__( 'YITH WooCommerce PDF Invoice and Shipping List is available in an outstanding PREMIUM version with many new options, discover it now.', 'yith-woocommerce-pdf-invoice' ) .
				' <a href="' . $this->get_premium_landing_uri() . '">' . esc_html__( 'Premium version', 'yith-woocommerce-pdf-invoice' ) . '</a>';

			$args[] = array(
				'screen_id'  => 'plugins',
				'pointer_id' => 'yith_woocommerce_pdf_invoice',
				'target'     => '#toplevel_page_yit_plugin_panel',
				'content'    => sprintf(
					'<h3> %s </h3> <p> %s </p>',
					esc_html__( 'YITH WooCommerce PDF Invoice and Shipping List', 'yith-woocommerce-pdf-invoice' ),
					esc_html__( 'In YIT Plugins tab you can find YITH WooCommerce PDF Invoice and Shipping List options.<br> From this menu you can access all settings of YITH plugins activated.', 'yith-woocommerce-pdf-invoice' ) . '<br>' . $premium_message
				),
				'position'   => array(
					'edge'  => 'left',
					'align' => 'center',
				),
				'init'       => defined( 'YITH_YWPI_PREMIUM' ) ? YITH_YWPI_INIT : YITH_YWPI_FREE_INIT,
			);

			YIT_Pointers()->register( $args );
		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return $this->premium_landing_url;
		}

		// region    ****    licence related methods ****.

		/**
		 * Add actions to manage licence activation and updates
		 */
		public function licence_activation() {
			if ( ! defined( 'YITH_YWPI_PREMIUM' ) ) {
				return;
			}

			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_YWPI_DIR . '/plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_YWPI_DIR . '/plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_YWPI_INIT, YITH_YWPI_SECRET_KEY, YITH_YWPI_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_YWPI_SLUG, YITH_YWPI_INIT );
		}
		// endregion.
	}
}
