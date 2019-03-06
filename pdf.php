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

	$number_of_columns = 4;
	$line_break = "<br>";
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
		$page_width			= 11 * $inch_converter;
		$page_height			= 8.5 * $inch_converter;
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

	// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo http://localhost:8080/wp-content/uploads/2019/03/logo.png
        $image_file = 'http://localhost:80/wp-content/uploads/2019/03/logo.png';
        $this->Image('wp-content/uploads/2019/03/logo.png', 10, 10, 15, '', 'JPG', 'http://localhost:80', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 15);
        // Title
        $this->Cell(0, 15, 'SLAA NEI Meeting List', 0, false, 'C', 0, '', 0, false, 'M', 'B');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 9);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}




	//create new PDF
	//$pdf = new MyTCPDF();
	$pdf = new MYPDF("L", PDF_UNIT, "Letter", true, 'UTF-8', false);
	$pdf->SetFont('helvetica', '', 8);
	//$pdf->SetAuthor('Nicola Asuni');
	$pdf->SetTitle('SLAA NEI Meeting List');

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//logo  https://slaanei.org/wp-content/uploads/2019/02/logo.png
//$header_html = '<img src="https://slaanei.org/wp-content/uploads/2019/02/logo.png" alt="logo" width="72" height="71"> SLAA NEI Meeting List';
//$pdf->SetHeaderData("", 50, $header_html, "");

	$pdf->SetMargins(10, 15, 10, true);
	$pdf->SetAutoPageBreak(TRUE, 10);
	$pdf->AddPage();


	// set intro
	$intro_text = '
	<table width="100%" cellspacing="2" cellpadding="2">
<tbody>
<tr>
<td width="50%" align="right"><img
src="https://slaanei.org/wp-content/uploads/2019/02/logo.png"
alt="logo" width="77" height="76"><br>
</td>
<td align="left"><font size="+2">SLAA NEI<br>
617-555-5555</font><br>
</td>
</tr>
<tr>
<td colspan="2" align="center"><font size="+2">https://www.slaanei.org</font>
</td>
</tr>
</tbody>
</table>
<br>
<font size="+1">Codes<br>
C=Closed<br>
O=Open<br>
SP=Speaker<br>
D=Discussion<br>
ST=Step Study<br>
LIT=Literature Study<br>
FF=Fragrance Free<br>
H=Handicap Accessible<br>
</font>
';

$this_column .= $intro_text;

	foreach ($meetings as $meeting){

		if($meeting['formatted_day'] !== $current_day){
				$current_day = $meeting['formatted_day'];
				//$pdf->Write(0, '------ ' . $current_day . ' -------', '', 0, 'L', true, 0, false, false, 0);
				$this_column .=  "<div align=\"center\"><font size=\"9\">========" . $current_day . "========</font></div>" ;

		}else{
			//add the divider
			$this_column .=  "<div align=\"center\">--------------------------</div>" ;
		}



		$this_column .= $meeting['text'] ;
		// $pdf->Write(0, $meeting['text'], '', 0, 'L', true, 0, false, false, 0);
		// $pdf->Write(0, '-----------------------------', '', 0, 'L', true, 0, false, false, 0);


	}

	$pdf->resetColumns();
	$pdf->setEqualColumns($number_of_columns, $column_width);
	$pdf->selectColumn();
	$pdf->writeHTML($this_column, true, false, true, false, 'J');
	//$pdf->Write(0, $this_column, '', 0, 'J', true, 0, false, true, 0);


	// $pdf->MultiCell($column_width, 0, $this_column, 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
 //ob_end_clean();

	$pdf->Output($_GET['size'] . '.pdf', 'I');

	exit;
});
