<?php

add_action('wp_ajax_step_pdf', function(){
	global $wpdb, $margins, $font_table_rows, $page_width, $page_height, $table_padding, $font_header, $header_top, $font_footer, $footer_bottom, $first_column_width, $table_border_width, $font_table_rows, $table_padding, $font_table_header, $first_column_width, $day_column_width, $table_border_width, $font_table_rows, $table_padding, $first_column_width, $day_column_width, $table_border_width, $inner_page_height, $font_table_rows, $index, $exclude_from_indexes, $zip_codes, $table_padding, $line_height_ratio;

	//must be a logged-in user to run this page (otherwise last_contact will be null)
	if (!is_user_logged_in()) {
		auth_redirect();
	} elseif (!current_user_can('edit_posts')) {
		die('you do not have access to view this page');
	}

	ini_set('max_execution_time', 60);

	/*
			values from form:
	header_text

	font_size
	column_count
	column_padding
	outtro_html

	*/
//we are getting in function
	//$header_text = $_GET['header_text'];

	if (isset($_GET['margin'])) {
		$margin_size = $_GET['margin'];
	}else{
		$margin_size = 10;
	}

	if (isset($_GET['font_size'])) {
		$font_size = $_GET['font_size'];
	}else{
		$font_size = 8;
	}

	if (isset($_GET['column_count'])) {
		$number_of_columns = $_GET['column_count'];
	}else{
		$number_of_columns = 4;
	}

	if (isset($_GET['column_padding'])) {
		$column_padding = $_GET['column_padding'];
	}else{
		$column_padding = 5;
	}

	if (isset($_GET['cover_post_id'])) {
		$post_id = $_GET['cover_post_id'];
		$queried_post = get_post($post_id);
		$post_content = $queried_post->post_content;
		$outtro_text = $post_content;
	}else{
		$outtro_text = "";
	}


	$page_width = 279.4; //11 inches
	$page_height = 215.9; //8.5 inches

	//get column width
$column_width = ($page_width -  (($number_of_columns-1) * $column_padding)  ) / $number_of_columns;

	//convert dimensions to mm
	$inch_converter			= 25.4; //25.4mm to an inch


	$line_break = "<br>";


	//load libraries
	require_once('vendor/autoload.php');
	//require_once('mytcpdf.php');

	//run function to attach meeting data to $regions
	$meetings = attachPdfMeetingData();

	// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {

			if (isset($_GET['header_text'])) {
				// Set font
				$this->SetFont('helvetica', 'B', 15);
				// Title

				$header_text = $_GET['header_text'];
				$this->Cell(0, 15, $header_text, 0, false, 'C', 0, '', 0, false, 'M', 'B');
			}


    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        // $this->SetY(-15);
        // Set font
        // $this->SetFont('helvetica', 'I', 9);
        // Page number
        // $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}




	//create new PDF
	//$pdf = new MyTCPDF();
	$pdf = new MYPDF("L", PDF_UNIT, "Letter", true, 'UTF-8', false);
	$pdf->SetFont('helvetica', '', $font_size);
	$pdf->SetTitle('SLAA NEI Meeting List');

	$pdf->SetMargins($margin_size, $margin_size, $margin_size, true);
	$pdf->SetAutoPageBreak(TRUE, $margin_size);
	$pdf->AddPage();


	// set intro


$this_column = "";
$current_day = "";

//$this_column .= $header_text;
$this_column .= $intro_text;

	foreach ($meetings as $meeting){

		if($meeting['formatted_day'] !== $current_day){
				$current_day = $meeting['formatted_day'];
				//$pdf->Write(0, '------ ' . $current_day . ' -------', '', 0, 'L', true, 0, false, false, 0);
				$this_column .=  "<div align=\"center\"><font size=\"+2\">========" . $current_day . "========</font></div>" ;

		}else{
			//add the divider
			$this_column .=  "<div align=\"center\">--------------------------</div>" ;
		}



		$this_column .= $meeting['text'] ;
		// $pdf->Write(0, $meeting['text'], '', 0, 'L', true, 0, false, false, 0);
		// $pdf->Write(0, '-----------------------------', '', 0, 'L', true, 0, false, false, 0);


	}


	$this_column .= $outtro_text;
	$pdf->resetColumns();
	$pdf->setEqualColumns($number_of_columns, $column_width);
	$pdf->selectColumn();
	$pdf->writeHTML($this_column, true, false, true, false, 'J');
	//$pdf->Write(0, $this_column, '', 0, 'J', true, 0, false, true, 0);


	// $pdf->MultiCell($column_width, 0, $this_column, 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
 //ob_end_clean();

	$pdf->Output('meeting_list.pdf', 'I');

	exit;
});
