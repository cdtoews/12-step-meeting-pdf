<?php


	add_action('wp_ajax_step_pdf', function(){

	//must be a logged-in user to run this page (otherwise last_contact will be null)
	if (!is_user_logged_in()) {
		auth_redirect();
	} elseif (!current_user_can('edit_posts')) {
		die('you do not have access to view this page');
	}
	
	require_once('vendor/autoload.php');
	
	$page_layout = get_option('tsmp_layout');
	if($page_layout == "table1"){
		tsmp_create_pdf_table1();
	}elseif($page_layout == "columns1"){
		//we'll assume columns1 
		tsmp_create_pdf_columns(1);
	}elseif($page_layout == "columns2"){
		//we'll assume columns1 
		tsmp_create_pdf_columns(2);
	}else{
		echo 'something is wrong, page_layout not specified correctly';
	}

});

// ==========================================================================
//                        Make Table1
// ==========================================================================


function tsmp_create_pdf_table1(){
	global $wpdb, $margins, $font_table_rows, $page_width, $page_height, $table_padding, $font_header, 
	$header_top, $font_footer, $footer_bottom, $first_column_width, $table_border_width, $font_table_rows, 
	$table_padding, $font_table_header, $first_column_width, $day_column_width, $table_border_width, $font_table_rows, 
	$table_padding, $first_column_width, $day_column_width, $table_border_width, $inner_page_height, $font_table_rows, 
	$index, $exclude_from_indexes, $zip_codes, $table_padding, $line_height_ratio;
	//must be a logged-in user to run this page (otherwise last_contact will be null)
	if (!is_user_logged_in()) {
		auth_redirect();
	} elseif (!current_user_can('edit_posts')) {
		die('you do not have access to view this page');
	}
	
	ini_set('max_execution_time', 60);
	//output PDF of NYC meeting list using the TCPDF library
	//don't show these in indexes
	$exclude_from_indexes	= array('Beginner', 'Candlelight', 'Closed', 'Grapevine', 'Literature', 'Open', 'Topic Discussion');
	
	$margin_size = get_option('tsmp_margin');
	$font_size = get_option('tsmp_font_size');
	$number_of_columns = get_option('tsmp_column_count');
	$column_padding = get_option('tsmp_column_padding');
	$outtro_text = get_option('tsmp_outtro_html');
	$intro_text = get_option('tsmp_intro_html');
	$page_width = get_option('tsmp_width');
	$page_height = get_option('tsmp_height');
	
	
	$first_page_no = 1;
	
	//config dimensions, in inches
	$table_border_width		= .1;
	//convert dimensions to mm
	$inch_converter			= 25.4; //25.4mm to an inch

	$table_padding		= 1.8; //in mm
	$header_top			= 9;
	$footer_bottom 		= -15;
	$font_header			= array('helvetica', 'b', 18);
	$font_footer			= array('helvetica', 'r', 10);
	$font_table_header	= array('helvetica', 'b', 8);
	$font_table_rows		= array('dejavusans', 'r', 6.4); //for the unicode character
	$font_index_header	= array('helvetica', 'b', 9);
	$font_index_rows		= array('helvetica', 'r', 6);


	$line_height_ratio	= 2.87;
	$index_width			= 57; // in mm
	$table_gap			= .25 * $inch_converter; //gap between tables
	
	
	$inner_page_width		= $page_width - ($margin_size * 2);
	$inner_page_height		= $page_height - ($margin_size * 2);
	$first_column_width		= $inner_page_width * .37;
	$day_column_width		= ($inner_page_width - $first_column_width) / 7;
	$page_threshold			= .5 * $inch_converter; //amount of space to start a new section
	$index = $zip_codes		= array();
	//main sections are here manually to preserve book order
	$region_ids = tsml_get_all_regions();

	$regions = array();
	foreach ($region_ids as $region_id) {
		$regions[$region_id] = array();
	}


	require_once('tabletcpdf.php');
	//run function to attach meeting data to $regions
	$regions = attachPdfRegionData($regions);
	

	//create new PDF
	$pdf = new TableTCPDF();
	foreach ($regions as $region) {

		if(empty($region)){
			// if nothing in this region, skip 
			continue;
		}
		$pdf->header = $region['name'];
		$pdf->NewPage();
		
		if (!empty($region['sub_regions'])) {

			//array_shift($region['sub_regions']);
			foreach ($region['sub_regions'] as $sub_region => $rows) {
				
				//create a new page if there's not enough space
				if (($inner_page_height - $pdf->GetY()) < $page_threshold) {
					$pdf->NewPage();
				}
				
				//draw rows
				$pdf->drawTable($sub_region, $rows, $region['name']);
				
				//draw a gap between tables if there's space
				if (($inner_page_height - $pdf->GetY()) > $table_gap) {
					$pdf->Ln($table_gap);
				}
				
				//break; //for debugging
			}
		} elseif ($region['rows']) {
			$pdf->drawTable($region['name'], $region['rows'], $region['name']);
		}
		//break; //for debugging
	}
	//index
	ksort($index);
	$pdf->header = 'Index';
	$pdf->NewPage();
	$pdf->SetEqualColumns(3, $index_width);
	$pdf->SetCellPaddings(0, 1, 0, 1);
	$index_output = '';
	foreach ($index as $category => $rows) {
		ksort($rows);
		$pdf->SetFont($font_index_header[0], $font_index_header[1], $font_index_header[2]);
		$pdf->Cell(0, 0, $category, array('B'=>array('width' => .25)), 1);
		$pdf->SetFont($font_index_rows[0], $font_index_rows[1], $font_index_rows[2]);
		foreach ($rows as $group => $page) {
			if ($pos = strpos($group, ' #')) $group = substr($group, 0, $pos);
			if (strlen($group) > 33) $group = substr($group, 0, 32) . '…';
			$pdf->Cell($index_width * .88, 0, $group, array('B'=>array('width' => .1)), 0);
			$pdf->Cell($index_width * .12, 0, $page, array('B'=>array('width' => .1)), 1, 'R');
		}
		$pdf->Ln(4);
	}
	//zips are a little different, because page numbers is an array
	$pdf->SetFont($font_index_header[0], $font_index_header[1], $font_index_header[2]);
	$pdf->Cell(0, 0, 'ZIP Codes', array('B'=>array('width' => .25)), 1);
	$pdf->SetFont($font_index_rows[0], $font_index_rows[1], $font_index_rows[2]);
	ksort($zip_codes);
	foreach ($zip_codes as $zip => $pages) {
		$pages = array_unique($pages);
		$pdf->Cell($index_width * .35, 0, $zip, array('B'=>array('width' => .1)), 0);
		$pdf->Cell($index_width * .65, 0, implode(', ', $pages), array('B'=>array('width' => .1)), 1, 'R');
	}
	$pdf->Output( 'meeting_list.pdf', 'I');
	exit;
	
}




// ===========================================================================
//                      Make Columns 1
// ===========================================================================

function tsmp_create_pdf_columns($layout_type){
	ob_start();
	ini_set('max_execution_time', 60);
	require_once('meeting.php');

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


	//run function get array of meeting objects
	$mymeetings = attachPdfMeetingData();

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
	
	$pageLayout = array($page_width, $page_height);
	$pdf = new MYPDF("", PDF_UNIT, $pageLayout, true, 'UTF-8', false);
	$pdf->SetFont('helvetica', '', $font_size);

	$pdf->SetMargins($margin_size, $margin_size, $margin_size, true);
	$pdf->SetAutoPageBreak(TRUE, $margin_size);
	$pdf->AddPage();

	$current_column = 1;
	//starting x,y for the start of this column 
	$column_x = $margin_size + (($column_padding + $column_width) * ($current_column - 1));
	$column_y = $margin_size;
	
	// ----------------------------------------------------------
	//                       Pre HTML  
	// ----------------------------------------------------------
	
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
		
	// ----------------------------------------------------------
	//                       meetings 
	// ----------------------------------------------------------
	
	$current_day = "";
		//loop through meetings 
	foreach ($mymeetings as $mymeeting){
		$column_x = $margin_size + (($column_padding + $column_width) * ($current_column - 1));
		$column_y = $margin_size;
		// $meeting_header = "";
		// -------------------------------------------------------------------------
		$thisday = $mymeeting->get_formatted_day();
		if($thisday !== $current_day){
				$current_day = $thisday;
				$meeting_header =  "<div style=\"background-color:black\" align=\"center\"><font  color=\"white\"  size=\"+2\">"  . $thisday . "</font></div>" ;
		}else{
			$meeting_header = "<hr>";
		}
		
		$start_page = $pdf->getPage();
		//write_log("column width:" . $column_width);
		$pdf->startTransaction();
		$pdf->MultiCell($column_width, 1,  $meeting_header . $mymeeting->get_text($layout_type) , 0, 'J', 0, 2, $column_x, '', true , 0, true, true, 0, 'T', true);
		$end_page = $pdf->getPage();
		
		
		if ($end_page == $start_page) {
			//if we are still onthe same page, commit 
			$pdf->commitTransaction();
		}else{ //we would have popped to a new page 
			$pdf = $pdf->rollbackTransaction();
		
			if($thisday !== $current_day){
				//<div style="background-color:black">
					$meeting_header =  "<div style=\"background-color:black\" align=\"center\"><font  color=\"white\"  size=\"+2\">" . $thisday . "</font></div>" ;
			}else{
				$meeting_header =  "<div style=\"background-color:black\" align=\"center\"><font color=\"white\" size=\"+2\">" . $thisday . " (cont)</font></div>" ;
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
			$pdf->MultiCell($column_width, 1, $meeting_header . $mymeeting->get_text($layout_type) , 0, 'J', 0, 2, $column_x, $column_y, true , 0, true, true, 0, 'T', true);
			
		}
		
		//set $current_day to the day for the meeting we just printed 
		$current_day = $thisday;

	}

	// ----------------------------------------------------------
	//                       Post HTML  
	// ----------------------------------------------------------
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
	
}
