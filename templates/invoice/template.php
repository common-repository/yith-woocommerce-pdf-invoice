<?php
/**
 * The Template for invoice
 *
 * Override this template by copying it to yourtheme/invoice/template.php
 *
 * @package YITH\PDFInvoice
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<style type="text/css">
		body {
			color: #000;
			font-family: "dejavu sans", "open sans", "sans-serif";
		}
	</style>
	<?php
	/**
	 * Hook yith_ywpi_invoice_template_head
	 *
	 * @hooked add_style_files - 10 (add css file based on type of current document
	 */
	do_action( 'yith_ywpi_invoice_template_head' );
	?>
</head>

<body>
<?php
/**
<script type="text/php">
if ( isset($pdf) ) { $font = Font_Metrics::get_font("verdana", "bold");
	$pdf->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0,0,0));
}
</script> */
?>
<?php
/**
 * Hook yith_ywpi_invoice_template_content
 *
 * @hooked add_invoice_content - 10 (add invoice template content
 */
do_action( 'yith_ywpi_invoice_template_content' );
?>

</body>
</html>
