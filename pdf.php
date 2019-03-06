<?php

add_action('wp_ajax_pdf', function(){
	global $wpdb, $margins, $font_table_rows, $page_width, $page_height, $table_padding, $font_header, $header_top, $font_footer, $footer_bottom, $first_column_width, $table_border_width, $font_table_rows, $table_padding, $font_table_header, $first_column_width, $day_column_width, $table_border_width, $font_table_rows, $table_padding, $first_column_width, $day_column_width, $table_border_width, $inner_page_height, $font_table_rows, $index, $exclude_from_indexes, $zip_codes, $table_padding, $line_height_ratio;

	//must be a logged-in user to run this page (otherwise last_contact will be null)
	if (!is_user_logged_in()) {
		auth_redirect();
	} elseif (!current_user_can('edit_posts')) {
		die('you do not have access to view this page');
	} elseif (!isset($_GET['start']) || !isset($_GET['index']) || !isset($_GET['size'])) {
		die('variables missing');
	}

	ini_set('max_execution_time', 60);

	//output PDF of NYC meeting list using the TCPDF library

	//don't show these in indexes
	$exclude_from_indexes	= array('Beginner', 'Candlelight', 'Closed', 'Grapevine', 'Literature', 'Open', 'Topic Discussion');

	//config dimensions, in inches
	$table_border_width		= .1;

	//convert dimensions to mm
	$inch_converter			= 25.4; //25.4mm to an inch

	if ($_GET['size'] == 'letter') {
		$table_padding		= 1.8; //in mm
		$header_top			= 9;
		$footer_bottom 		= -15;
		$font_header			= array('helvetica', 'b', 18);
		$font_footer			= array('helvetica', 'r', 10);
		$font_table_header	= array('helvetica', 'b', 8);
		$font_table_rows		= array('dejavusans', 'r', 6.4); //for the unicode character
		$font_index_header	= array('helvetica', 'b', 9);
		$font_index_rows		= array('helvetica', 'r', 6);
		$margins = array(
			'left'			=> .5,
			'right'			=> .5,
			'top'			=> .8, //include header
			'bottom'			=> .5, //include footer
		);
		$page_width			= 8.5 * $inch_converter;
		$page_height			= 11 * $inch_converter;
		$line_height_ratio	= 2.87;
		$index_width			= 57; // in mm
		$table_gap			= .25 * $inch_converter; //gap between tables
	} elseif ($_GET['size'] == 'book') {
		$table_padding		= 1.4; //in mm
		$header_top			= 6;
		$footer_bottom 		= -10;
		$font_header			= array('helvetica', 'b', 16);
		$font_footer			= array('helvetica', 'r', 8);
		$font_table_header	= array('helvetica', 'b', 6);
		$font_table_rows		= array('dejavusans', 'r', 5.4); //for the unicode character
		$font_index_header	= array('helvetica', 'b', 7);
		$font_index_rows		= array('helvetica', 'r', 5.4);
		$margins = array(
			'left'				=> .25,
			'right'				=> .25,
			'top'				=> .65, //include header
			'bottom'				=> .5, //include footer
		);
		$page_width			= 6.5 * $inch_converter;
		$page_height			= 9.5 * $inch_converter;
		$line_height_ratio	= 2.4;
		$index_width			= 47; // in mm
		$table_gap			= .2 * $inch_converter; //gap between tables
	}

	foreach ($margins as $key => $value) $margins[$key] *= $inch_converter;
	$inner_page_width		= $page_width - $margins['left'] - $margins['right'];
	$inner_page_height		= $page_height - $margins['top'] - $margins['bottom'];
	$column_width		= $inner_page_width / 4;
	//$day_column_width		= ($inner_page_width - $first_column_width) / 7;
	$page_threshold			= .5 * $inch_converter; //amount of space to start a new section
	$index = $zip_codes		= array();

	//main sections are here manually to preserve book order
//	print "inside the pdf.php about to do regions array<br>";
/*
	$regions = array();
	foreach (array(
				"ma",
				"me",
				"nh",
				"ri",
				"vt"
	) as $region) {
		// live table: wp_8ngygs8ysn_terms
		$region_id = $wpdb->get_var('SELECT term_id FROM wp_terms where name = "' . $region . '"');
		if (!$region_id) die('could not find region with name ' . $region);
		$regions[$region_id] = array();
	}
	*/
//	print "finished the regions array thingie<br>";
	//symbols used in the book, in the order in which they're applied
	$symbols = array(
		'*',   '^',   '#',   '!',   '+',   '@',   '%',
		'**',  '^^',  '##',  '!!',  '++',  '@@',  '%%',
		'***', '^^^', '###', '!!!', '+++', '@@@', '%%%',
	);

	//load libraries
	require_once('vendor/autoload.php');
	require_once('mytcpdf.php');

	//run function to attach meeting data to $regions
	$meetings = attachPdfMeetingData();

	//create new PDF
	$pdf = new MyTCPDF();
	//$pdf->SetAuthor('Nicola Asuni');
	$pdf->SetTitle('SLAA NEI Meeting List');
	//$pdf->SetSubject('TCPDF Tutorial');

	$pdf->NewPage();
	//$pdf->Write(0, print_r($meetings), '', 0, 'L', true, 0, false, false, 0);
	$current_day = "";
	foreach ($meetings as $meeting){
		//lets check if we have a new day

		if($meeting['formatted_day'] !== $current_day){
			$current_day = $meeting['formatted_day'];
			$pdf->Write(0, '------ ' . $current_day . ' -------', '', 0, 'L', true, 0, false, false, 0);
		}

		$pdf->Write(0, $meeting['text'], '', 0, 'L', true, 0, false, false, 0);
		$pdf->Write(0, '-----------------------------', '', 0, 'L', true, 0, false, false, 0);

	}

 ob_end_clean();
	$pdf->Output($_GET['size'] . '.pdf', 'I');

	exit;
});
