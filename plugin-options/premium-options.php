<?php
/**
 * Premium tab
 *
 * @package YITH\PDFInvoice
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly



return array(
	'premium' => array(
		'home' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_pdf_invoice_premium',
		),
	),
);
