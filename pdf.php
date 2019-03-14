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
	$html_delimiter = "</div>";

	//calculate column width
	$column_width = ($page_width -  (($number_of_columns-1) * $column_padding) - ($margin_size * 2)  ) / $number_of_columns;
	$column_height = $page_height - ($margin_size * 2);
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
	}//end of class 

	//create new PDF
	
	$pageLayout = array($page_width, $page_height); //  or array($height, $width)
	$pdf = new MYPDF("", PDF_UNIT, $pageLayout, true, 'UTF-8', false);
	$pdf->SetFont('helvetica', '', $font_size);

	$pdf->SetMargins($margin_size, $margin_size, $margin_size, true);
	$pdf->SetAutoPageBreak(TRUE, $margin_size);
	$pdf->AddPage();

	$current_column = 1;
	//starting x,y for the start of this column 
	$column_x = $margin_size + (($column_padding + $column_width) * ($current_column - 1));
	$column_y = $margin_size;
	
	// ==========================================================
	//                       Pre HTML  
	// =========================================================
	
	//loop through pre-html 
	if($intro_text != ""){
		$html_array = explode($html_delimiter, $intro_text );
		foreach ($html_array as $html_block) {
			$html_block .= $html_delimiter; //put back what we striped out 
			
			//get start page to see if adding this text would send it over the edge 
			$start_page = $pdf->getPage();
			
			//start a transaction, so if it goes over the edge of the page, we can rollback 
			$pdf->startTransaction();
			$pdf->MultiCell($column_width, 1,  $html_block, 0, 'J', 0, 2, $column_x, '', true , 0, true, true, 0, 'T', true);
			$end_page = $pdf->getPage();
			
			if ($end_page == $start_page) {
				//if we are still onthe same page 
				$pdf->commitTransaction();
			}else{ //we would have popped to a new page 
				$pdf = $pdf->rollbackTransaction();
				$current_column++;
				
				if($current_column > $number_of_columns){ //last column on the page 
					//need a new page 
					$pdf->AddPage();
					$current_column = 1;
				}
				$column_x = $margin_size + (($column_padding + $column_width) * ($current_column - 1));
				$column_y = $margin_size;
				$pdf->SetXY($column_x,$column_y, true);
				//write the text on the new column [and page]
				$pdf->MultiCell($column_width, 1, $html_block , 0, 'J', 0, 2, $column_x, '', true , 0, true, true, 0, 'T', true);
				
			}
	}//end of Loop
}//end of if
		
	// ==========================================================
	//                       meetings 
	// ==========================================================
	
	$current_day = "";
		//loop through meetings 
	foreach ($meetings as $meeting){
		$column_x = $margin_size + (($column_padding + $column_width) * ($current_column - 1));
		$column_y = $margin_size;
		// $meeting_header = "";
		// -------------------------------------------------------------------------
		if($meeting['formatted_day'] !== $current_day){
				$current_day = $meeting['formatted_day'];
				$meeting_header =  "<div align=\"center\"><font size=\"+2\">========" . $meeting['formatted_day'] . "========</font></div>" ;
		}else{
			$meeting_header = "<hr>";
		}
		
		$start_page = $pdf->getPage();
		
		$pdf->startTransaction();
		$pdf->MultiCell($column_width, 1,  $meeting_header . $meeting['text'], 0, 'J', 0, 2, $column_x, '', true , 0, true, true, 0, 'T', true);
		$end_page = $pdf->getPage();
		
		
		if ($end_page == $start_page) {
			//if we are still onthe same page, commit 
			$pdf->commitTransaction();
		}else{ //we would have popped to a new page 
			$pdf = $pdf->rollbackTransaction();
		
			if($meeting['formatted_day'] !== $current_day){
					$meeting_header =  "<div align=\"center\"><font size=\"+2\">========" . $meeting['formatted_day'] . "========</font></div>" ;
			}else{
				$meeting_header =  "<div align=\"center\"><font size=\"+2\">========" . $meeting['formatted_day'] . " (cont)========</font></div>" ;
			}
			$current_column++;
			
			if($current_column > $number_of_columns){ //last column on the page 
				//need a new page 
				$pdf->AddPage();
				$current_column = 1;
			}
			//reset X and Y to next column 
			$column_x = $margin_size + (($column_padding + $column_width) * ($current_column - 1));
			$column_y = $margin_size;
			$pdf->SetXY($column_x,$column_y, true);
			//write the text on the new column [and page ]
			$pdf->MultiCell($column_width, 1, $meeting_header . $meeting['text'] , 0, 'J', 0, 2, $column_x, $column_y, true , 0, true, true, 0, 'T', true);
			
		}
		
		//set $current_day to the day for the meeting we just printed 
		$current_day = $meeting['formatted_day'];

	}

	// ==========================================================
	//                       Post HTML  
	// ==========================================================
	if($outtro_text != ""){
		$html_array = explode($html_delimiter, $outtro_text );
		//$y = $column_y;
		foreach ($html_array as $html_block) {
			$html_block .= $html_delimiter; //put back what we striped out 
			
			$start_page = $pdf->getPage();
			
			$pdf->startTransaction();
			$pdf->MultiCell($column_width, 1,  $html_block, 0, 'J', 0, 2, $column_x, '', true , 0, true, true, 0, 'T', true);
			$end_page = $pdf->getPage();
			
			//if we are still onthe same page 
			if ($end_page == $start_page) {
			
				$pdf->commitTransaction();
			}else{ //we would have popped to a new page 
				$pdf = $pdf->rollbackTransaction();
				
				$current_column++;
					
				if($current_column > $number_of_columns){ //last column on the page 
					//need a new page 
					$pdf->AddPage();
					$current_column = 1;
				}
				$column_x = $margin_size + (($column_padding + $column_width) * ($current_column - 1));
				$column_y = $margin_size;
				$pdf->SetXY($column_x,$column_y, true);
				$pdf->MultiCell($column_width, 1, $html_block , 0, 'J', 0, 2, $column_x, '', true , 0, true, true, 0, 'T', true);
				
			}
		
		}//end of Loop
	}//end of if

	//this seems to make php happy:
	ob_end_clean();
	$pdf->Output('meeting_list.pdf', 'I');

	exit;
});
