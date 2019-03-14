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

	$column_text = "";
	$current_column = 1;
	$column_x = $margin_size + (($column_padding + $column_width) * ($current_column - 1));
	$column_y = $margin_size;
	//loop through pre-html 
	$leftover_html = "";
	$html_array = explode($html_delimiter, $intro_text );
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
			//make column prior to adding new block, and reset $column_text
			// ** if a single block is larger than a page, we are in trouble here 
			$column_text = $html_block;
			$current_column++;
			//reset X and Y to next column 
			$column_x = $margin_size + (($column_padding + $column_width) * ($current_column - 1));
			$column_y = $margin_size;
			$pdf->SetXY($column_x,$column_y, true);
			
			if($current_column > $number_of_columns){ //last column on the page 
				//need a new page 
				$pdf->AddPage();
				$current_column = 1;
			}
			$pdf->MultiCell($column_width, 1, $html_block , 0, 'J', 0, 2, $column_x, $column_y, true , 0, true, true, 0, 'T', true);
			
		}
		//let's push it up a tiny bit 
//		$y = $pdf->getY() -3;

	
	}
		
	//for debug:
	$pdf->AddPage();
		
	
	//loop through meetings 
	$current_day = "";
	$skip_splitter_line = true;
	foreach ($meetings as $meeting){

		$meeting_header = "";
		
		
	
		
		$text_height = $pdf->getStringHeight($column_width, $column_text . $meeting_header . $meeting['text']);
		
		//write_log('text height:' . $text_height )	;

			
		if($text_height > $column_height ) { //if the text will be to big if we add the next meeting
			//write column and move to new column 
			$column_x = $margin_size + (($column_padding + $column_width) * ($current_column - 1));
			$column_y = $margin_size;
			//               width       ,   height      ,  txt             , border, align,fill ,ln, x,       y          ,reseth,stretch,ishtml,autopad,maxh,
			$pdf->MultiCell($column_width, $column_height, $column_text      , 0     , 'J', 0    , 1, $column_x, $column_y, true , 0     , true, true   , $column_height, 'T', true);
	//  $pdf->MultiCell(55,             60,            '[FIT CELL] '."\n", 1     , 'J', 1    , 1, 125      , 145      , true , 0     , false, true  , 60            , 'M', true);
			$column_text = "";
			$current_column++;
			$skip_splitter_line = true;
			if($current_column > $number_of_columns){
				//need a new page 
				$pdf->AddPage();
				$current_column = 1;
			}
		}
		
		if($meeting['formatted_day'] !== $current_day){
				$current_day = $meeting['formatted_day'];
				//$pdf->Write(0, '------ ' . $current_day . ' -------', '', 0, 'L', true, 0, false, false, 0);
				$meeting_header =  "<div align=\"center\"><font size=\"+2\">========" . $current_day . "========</font></div>" ;
				$skip_splitter_line = true;
		}elseif(	$skip_splitter_line ){
			$meeting_header =  "<div align=\"center\"><font size=\"+2\">========" . $current_day . " (cont)========</font></div>" ;
			
		}else{
			$meeting_header = "<hr>";
		}

		
		//add this meeting text to column text
		$column_text .= $meeting_header  . $meeting['text']  ;
		$skip_splitter_line = false;

	}

	$column_x = $margin_size + (($column_padding + $column_width) * ($current_column - 1));
	$column_y = $margin_size;
	$pdf->MultiCell($column_width, $column_height, $column_text      , 0     , 'J', 0    , 1, $column_x, $column_y, true , 0     , true, true   , $column_height, 'T', true);



	//add outtro html to column text
	//$column_text .= $outtro_text;

	//setup coluns and write html
	// $pdf->resetColumns();
	// $pdf->setEqualColumns($number_of_columns, $column_width);
	// $pdf->selectColumn();
	// $pdf->writeHTML($column_text, true, false, true, false, 'J');

	// seems to make php happy:
	ob_end_clean();

	$pdf->Output('meeting_list.pdf', 'I');

	exit;
});
