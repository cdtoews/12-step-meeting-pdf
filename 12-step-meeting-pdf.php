<?php

/**
 * Plugin Name: 12 Step Meeting PDF Generator
 * Plugin URI: https://github.com/cdtoews/12-step-meeting-pdf
 * Description: Create PDF meeting list from the 12 Step Meeting List Plugin
 * code forked from https://github.com/meeting-guide/nyintergroup
 * Version: 0.1.0
 * Author: Chris Toews
 * Author URI: https://yourtechguys.info
 * Text Domain: 12-step-meeting-pdf
 */

 	/*
	next versions:
	pages size/orientation
	custom font
	*/

 if (!defined('TSMP_CONTACT_EMAIL')) define('TSMP_CONTACT_EMAIL', 'chris@yourtechguys.info');
 if (!defined('TSMP_PATH')) define('TSMP_PATH', plugin_dir_path(__FILE__));
 if (!defined('TSMP_VERSION')) define('TSMP_VERSION', '0.1.0');


 if ( ! function_exists('write_log')) {
    function write_log ( $log )  {
       if ( is_array( $log ) || is_object( $log ) ) {
          error_log( print_r( $log, true ) );
       } else {
          error_log( $log );
       }
    }
 }

 //include admin files
 if (is_admin()) {
 	include(TSMP_PATH . 'includes/admin-gen.php');
	include(TSMP_PATH . 'includes/admin-menu.php');
 }

	//generates pdf with ajax hook
	include('pdf.php');


	//pdf function to get data and attach it to the regions array
	function attachPdfMeetingData() {
  
    $meetings = tsml_get_meetings(  
       array( 'post_status' => array('publish', 'private')  ) );
     //   ,false
     // ); 
  write_log("size of meetings:" . sizeof($meetings));
  //$meetings = tsml_get_meetings();

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

			//cobble the meeting text together
			$meetingtext = "";
			$meetingtext .= "<font='+1'><b>" . $meeting['region'] . " ";
			$meetingtext .= $meeting['sub_region'] . "</b></font>, ";
			$meetingtext .= $meeting['time_formatted'] . ", ";
			$meetingtext .= "(" . implode (',' , $meeting['types']) . ") ";
			$meetingtext .= @$meeting['name'] . ". ";
			$meetingtext .= @$meeting['location'] . ". ";
			$meetingtext .= @$parts[0] . ". ";
			$meetingtext .= @$meeting['notes'] . ". ";
			$meetingtext .= @$meeting['location_notes'] . ". ";

			//let's strip carriage returns that might be in location notes and notes
			$meetingtext = str_replace("\r", "", $meetingtext);
			$meetingtext = str_replace("\n", "", $meetingtext);
			$meetingtext = str_replace("\t", "", $meetingtext);

			$thismeeting = array(
					'text' => $meetingtext,
					'day' => $meeting['day'],
					'formatted_day' => $formatted_day

			);



		//put this meeting in array
			array_push($cellcontents, $thismeeting);
	}

	return $cellcontents;
}
