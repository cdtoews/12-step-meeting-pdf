<?php

/**
 * Plugin Name: 12 Step Meeting PDF
 */

//generates form from shortcode
include('form.php');

//generates pdf with ajax hook
include('pdf.php');

//for guide page
function format_time($string) {
	if ($string == '12:00') return '12N';
	if ($string == '23:59') return '12M';
	list($hours, $minutes) = explode(':', $string);
	$hours -= 0;
	if ($hours == 0) return '12:' . $minutes . 'a';
	if ($hours < 12) return $hours . ':' . $minutes . 'a';
	if ($hours > 12) $hours -= 12;
	return $hours . ':' . $minutes;
}

//need this for formatting the meeting types
function decode_types($type) {
	global $tsml_programs, $tsml_program;
	if (!array_key_exists($type, $tsml_programs[$tsml_program]['types'])) return '';
	return $tsml_programs[$tsml_program]['types'][$type];
}

//pdf function to get data and attach it to the regions array
function attachPdfMeetingData() {

	$meetings = tsml_get_meetings();

	$cellcontents = array();
	foreach ($meetings as $meeting) {
			@$parts = explode(', ', $meeting['formatted_address']);

			$day = $meeting['day'];
			$formatted_day = "";
			if($day == 0){
				$formatted_day = "Sunday";
			}elseif ($day == 1){
				$formatted_day = "Monday";
			}elseif ($day == 2){
				$formatted_day = "Tuesday";
			}elseif ($day == 3){
				$formatted_day = "Wednesday";
			}elseif ($day == 4){
				$formatted_day = "Thursday";
			}elseif ($day == 5){
				$formatted_day = "Friday";
			}elseif ($day == 6){
				$formatted_day = "Saturday";
			}else{
				$formatted_day = "Unknown Day";
			}

			$meetingtext = "";
			$meetingtext .= $meeting['region'] . " ";
			$meetingtext .= $meeting['sub_region'] . ", ";
			$meetingtext .= $meeting['time_formatted'] . ", ";
			$meetingtext .= "(" . implode (',' , $meeting['types']) . ") ";
			$meetingtext .= $meeting['name'] . ". ";
			$meetingtext .= $meeting['location'] . ". ";
			$meetingtext .= @$parts[0] . ". ";
			$meetingtext .= $meeting['notes'] . ". ";
			$meetingtext .= $meeting['location_notes'] . ". ";

			//let's strip carriage returns and tables
			$meetingtext = str_replace("\r", "", $meetingtext);
			$meetingtext = str_replace("\n", "", $meetingtext);
			$meetingtext = str_replace("\t", "", $meetingtext);

			$thismeeting = array(
					'text' => $meetingtext,
					'day' => $meeting['day'],
					'formatted_day' => $formatted_day

			);



		//	$cellcontents[] = $thismeeting;
			array_push($cellcontents, $thismeeting);
	}

	return $cellcontents;
}
