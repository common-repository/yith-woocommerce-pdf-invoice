<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Class YITH_Document
 *
 * @package YITH\PDFInvoice
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_Document' ) ) {

	/**
	 * Implements features related to a PDF document
	 *
	 * @class   YITH_Document
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	abstract class YITH_Document {

		/**
		 * Document type
		 *
		 * @var string Current document type
		 */
		public $document_type = '';

		/**
		 * If a document of type $document_type exists
		 *
		 * @var bool
		 */
		public $exists = false;

		/**
		 * Order
		 *
		 * @var WC_Order
		 */
		public $order;

		/**
		 * Check if the document is a valid order
		 *
		 * @var bool If this document is a valid WooCommerce order
		 */
		public $is_valid = false;

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @param int $order_id Order ID.
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 * @access public
		 * @return void
		 */
		public function __construct( $order_id ) {
			/**
			 * Get the WooCommerce order for this order id
			 */
			$this->order = wc_get_order( $order_id );

			/**
			 * Check if an order exists for this order id
			 */
			$this->is_valid = isset( $this->order );
		}

		/**
		 * Save file
		 *
		 * @param string $file_path File path.
		 */
		public function save_file( $file_path ) {
			$pdf_content = $this->generate_template();
			file_put_contents( $file_path, $pdf_content ); // phpcs:ignore
		}

		/**
		 * Generate the template
		 */
		private function generate_template() {

			$this->init_template();
			do_action( 'ywpi_before_template_generation' );

			ob_start();
			wc_get_template( 'template.php', null, YITH_YWPI_INVOICE_TEMPLATE_DIR, YITH_YWPI_INVOICE_TEMPLATE_DIR );

			$html = ob_get_contents();
			ob_end_clean();

			require_once YITH_YWPI_LIB_DIR . 'dompdf/autoload.inc.php';

			$options = new \Dompdf\Options();
			$options->setIsRemoteEnabled( true );

			$dompdf = new Dompdf\Dompdf();

			$dompdf->setOptions( $options );

			$dompdf->load_html( $html );

			$dompdf->render();

			// The next call will store the entire PDF as a string in $pdf.
			$pdf = $dompdf->output();
			$this->flush_template();

			return $pdf;
		}

		/**
		 * Get formatted date
		 *
		 * @return string
		 */
		public function get_formatted_date() {
			$format = get_option( 'ywpi_invoice_date_format' );

			return gmdate( $format, strtotime( yit_get_prop( $this->order, 'order_date' ) ) );
		}

		/**
		 * Init template
		 */
		public function init_template() {
			add_action( 'yith_ywpi_invoice_template_head', array( $this, 'add_invoice_style' ) );
			add_action( 'yith_ywpi_invoice_template_content', array( $this, 'add_invoice_content' ) );
		}

		/**
		 * Flush template
		 */
		public function flush_template() {
			remove_all_filters( 'yith_ywpi_invoice_template_head' );
			remove_all_filters( 'yith_ywpi_invoice_template_content' );

			remove_all_filters( 'yith_ywpi_invoice_template_sender' );
			remove_all_filters( 'yith_ywpi_invoice_template_company_logo' );
			remove_all_filters( 'yith_ywpi_invoice_template_customer_data' );
			remove_all_filters( 'yith_ywpi_invoice_template_invoice_data' );
			remove_all_filters( 'yith_ywpi_invoice_template_products_list' );
			remove_all_filters( 'yith_ywpi_invoice_template_footer' );
		}

		/**
		 * Add invoice style
		 */
		public function add_invoice_style() {
			$template_filename = $this->document_type . '.css';
			$template_path     = YITH_YWPI_INVOICE_TEMPLATE_DIR . $this->document_type . '.css';
			if ( file_exists( $template_path ) ) {
				ob_start();

				wc_get_template(
					'/invoice/' . $template_filename,
					null,
					'',
					YITH_YWPI_TEMPLATE_DIR
				);

				$content = ob_get_contents();
				ob_end_clean();

				if ( $content ) {
					?>
					<style type="text/css">
						<?php echo $content; // phpcs:ignore ?>
					</style>
					<?php
				}
			}
		}

		/**
		 * Add invoice content
		 */
		public function add_invoice_content() {

			global $ywpi_document;
			$template_filename = $this->document_type . '.php';
			$template_path     = YITH_YWPI_INVOICE_TEMPLATE_DIR . $template_filename;

			if ( file_exists( $template_path ) ) {
				wc_get_template( $template_filename, array( $ywpi_document ), YITH_YWPI_INVOICE_TEMPLATE_DIR, YITH_YWPI_INVOICE_TEMPLATE_DIR );
			}
		}
	}
}
