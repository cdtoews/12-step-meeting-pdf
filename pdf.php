<?php

	add_action('wp_ajax_step_pdf', function(){

	//must be a logged-in user to run this page (otherwise last_contact will be null)
	if (!is_user_logged_in()) {
		auth_redirect();
	} elseif (!current_user_can('edit_posts')) {
		die('you do not have access to view this page');
	}
	ob_start();
	ini_set('max_execution_time', 60);


	settings_fields('tsmp-settings-group');
	do_settings_sections( 'tsmp-settings-group' );

	//need to add variable validation for mortals
	$margin_size = get_option('tsmp_margin');
	$font_size = get_option('tsmp_font_size');
	$number_of_columns = get_option('tsmp_column_count');
	$column_padding = get_option('tsmp_column_padding');
	$outtro_text = get_option('tsmp_outtro_html');
	$intro_text = get_option('tsmp_intro_html');
	$page_width = get_option('tsmp_width');
	$page_height = get_option('tsmp_height');


	//calculate column width
	$column_width = ($page_width -  (($number_of_columns-1) * $column_padding) - ($margin_size * 2)  ) / $number_of_columns;

	//load libraries
	require_once('vendor/autoload.php');

	//run function to attach meeting data to $meetings
	$meetings = attachPdfMeetingData();

	// Extend the TCPDF class to create custom Header and Footer
	class MYPDF extends TCPDF {

    //Page header
    public function Header() {
			$header_text = get_option('tsmp_header');

			if ($header_text != "") {
				// Set font
				$this->SetFont('helvetica', 'B', 15);

				$this->Cell(0, 15, $header_text, 0, false, 'C', 0, '', 0, false, 'M', 'B');
			}


	    }

	    // Page footer
	    public function Footer() {
	        //nothing here for now
		  }
		}




	//create new PDF
	//$pdf = new MyTCPDF();
	$pageLayout = array($page_width, $page_height); //  or array($height, $width)
	$pdf = new MYPDF("L", PDF_UNIT, $pageLayout, true, 'UTF-8', false);
	$pdf->SetFont('helvetica', '', $font_size);

	$pdf->SetMargins($margin_size, $margin_size, $margin_size, true);
	$pdf->SetAutoPageBreak(TRUE, $margin_size);
	$pdf->AddPage();

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
		$this_column .=  "<hr>"; //"<div align=\"center\">--------------------------</div>" ;
	}


	//add this meeting text to column text
	$this_column .= $meeting['text'] ;

	}

	//add outtro html to column text
	$this_column .= $outtro_text;

	//setup coluns and write html
	$pdf->resetColumns();
	$pdf->setEqualColumns($number_of_columns, $column_width);
	$pdf->selectColumn();
	$pdf->writeHTML($this_column, true, false, true, false, 'J');

	// seems to make php happy:
	ob_end_clean();

	$pdf->Output('meeting_list.pdf', 'I');

	exit;
});
