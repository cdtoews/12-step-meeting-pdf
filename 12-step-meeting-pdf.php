<?php

/**
 * Plugin Name: 12 Step Meeting PDF Generator
 * Plugin URI: https://github.com/cdtoews/12-step-meeting-pdf
 * Description: Create PDF meeting list from the 12 Step Meeting List Plugin
 * code forked from https://github.com/meeting-guide/nyintergroup
 * Version: 1.0.4
 * Author: Chris Toews
 * Author URI: https://yourtechguys.info
 * Text Domain: 12-step-meeting-pdf
 */



 if (!defined('TSMP_CONTACT_EMAIL')) define('TSMP_CONTACT_EMAIL', 'chris@yourtechguys.info');
 if (!defined('TSMP_PATH')) define('TSMP_PATH', plugin_dir_path(__FILE__));
 if (!defined('TSMP_VERSION')) define('TSMP_VERSION', '0.2.1');

 if ( ! function_exists('write_log')) {
    function write_log ( $log )  {
       if ( is_array( $log ) || is_object( $log ) ) {
          error_log( print_r( $log, true ) );
       } else {
          error_log( $log );
       }
    }
 }

 if ( ! function_exists('verify_array')) {
    function verify_array ( $thing1 )  {
       if ( is_array( $thing1 )  ) {
          return $thing1;
       } else {
          return [];
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

  //for table  page
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

  //symbols used in the book, in the order in which they're applied
	$symbols = array(
		'*',   '^',   '#',   '!',   '+',   '@',   '%',
		'**',  '^^',  '##',  '!!',  '++',  '@@',  '%%',
		'***', '^^^', '###', '!!!', '+++', '@@@', '%%%',
	);

	//pdf function to get data and attach it to the regions array
	function attachPdfMeetingData() {

    $meetings = tsml_get_meetings(
       array( 'post_status' => array('publish', 'private')  )
       ,false
     );

     $meetings = filter_meetings($meetings);

     //i'm after tsml_types_in_use
  // $all_options = get_option('tsml_types_in_use');
  // write_log($all_options);

	$mymeetings = array();
	foreach ($meetings as $meeting) {

			// $thismeeting = array(
			// 		'text' => $meetingtext,
			// 		'day' => $meeting['day'],
      //     'city' => $city,
      //     'state' => $state,
			// 		'formatted_day' => $formatted_day,
      //     'time' => $meeting['time_formatted'],
      //     'types' => $meeting['types']
	    //  );

      //write all types out
      //write_log($meeting['types']);


      $mymeeting =  new  meeting($meeting);
			array_push($mymeetings, $mymeeting);
	}

	return $mymeetings;
}



// ===================================================
//               for nyintergroup table data
// ===================================================

//pdf function to get data and attach it to the regions array
function attachPdfRegionData($regions) {
	global $symbols;

	//going to be checking this over and over
	$count_symbols = count($symbols);

	//get all the sub-regions and their children
	$sub_region_ids = get_terms(array(
		'taxonomy' => 'tsml_region',
		'exclude' => array_keys($regions),
		'fields' => 'ids',
	));

	$sub_sub_regions = array();

	foreach ($sub_region_ids as $sub_region_id) {
		$sub_sub_region_ids = get_terms(array(
			'taxonomy' => 'tsml_region',
			'parent' => $sub_region_id,
			'fields' => 'ids',
		));
		foreach ($sub_sub_region_ids as $sub_sub_region_id) {
			$sub_sub_regions[$sub_sub_region_id] = $sub_region_id;
		}
	}

	//build an array of table rows for each region all in one shot, to preserve memory
	$rows = array();
	$meetings = tsml_get_meetings(
     array( 'post_status' => array('publish', 'private')  )
     ,false
   );
$meetings = filter_meetings($meetings);

	foreach ($meetings as $meeting) {

		//we group meetings by group-at-location
		$key = @$meeting['id'] . '-' . @$meeting['location_id'];

		//replace with parent category
		if (array_key_exists($meeting['region_id'], $sub_sub_regions)) {
			$meeting['region_id'] = $sub_sub_regions[$meeting['region_id']];
		}

		//make sure array region exists
		if (!array_key_exists($meeting['region_id'], $rows)) {
			$rows[$meeting['region_id']] = array();
		}

    //for viewing meeting data
    // write_log($meeting);
    // write_log("\n--------------------------------------");

		//attach meeting to region
		if (!array_key_exists($key, $rows[$meeting['region_id']])) {
			$parts = explode(', ', $meeting['formatted_address']);
			$rows[$meeting['region_id']][$key] = array(
				'group' => @$meeting['name'],
				'location' => @$meeting['location'],
				'address' => $parts[0],
				'postal_code' => substr($parts[2], 3),
				'notes' => @$meeting['location_notes'],
				'last_contact' => empty($meeting['last_contact']) ? null : date('n/j/y', strtotime($meeting['last_contact'])),
				'wheelchair' => false,
				'spanish' => true,
				'days' => array(
					0 => array(),
					1 => array(),
					2 => array(),
					3 => array(),
					4 => array(),
					5 => array(),
					6 => array(),
				),
				'footnotes' => array(),
				'types' => array(), //for indexes
			);
		}

		if(!is_array( $meeting['types']))		{
			$meeting['types'] = [];
		}

		//for indexes verify_array
		$rows[$meeting['region_id']][$key]['types'] = array_merge($rows[$meeting['region_id']][$key]['types'], $meeting['types']);

		//at least one meeting tagged wheelchair-accessible
		if (($index = array_search('X', verify_array( $meeting['types']))) !== false) {
			$rows[$meeting['region_id']][$key]['wheelchair'] = true;
			unset($meeting['types'][$index]);
		}

		//at least one meeting *not* tagged spanish means row is not "spanish"
		if (!in_array('S', $meeting['types'])) $rows[$meeting['region_id']][$key]['spanish'] = false;

		//insert into day
		$time = '';
		if (($index = array_search('D',  $meeting['types'])) !== false) {
			$time .= 'OD-'; //open discussion meeting (comes before open because all ODs are open)
			unset($meeting['types'][$index]);
		} elseif (($index = array_search('O',  $meeting['types'])) !== false) {
			$time .= 'O-';  //open meeting
			unset($meeting['types'][$index]);
		} elseif (($index = array_search('BE', $meeting['types'])) !== false) {
			$time .= 'B-';  //beginners meeting
			unset($meeting['types'][$index]);
		} elseif (($index = array_search('B',  $meeting['types'])) !== false) {
			$time .= 'BB-'; //big book meeting
			unset($meeting['types'][$index]);
		} elseif (($index = array_search('ST', $meeting['types'])) !== false) {
			$time .= 'S-';  //step meeting
			unset($meeting['types'][$index]);
		} elseif (($index = array_search('TR', $meeting['types'])) !== false) {
			$time .= 'T-';  //tradition meeting
			unset($meeting['types'][$index]);
		} elseif (($index = array_search('C',  $meeting['types'])) !== false) {
			$time .= 'C-';  //closed meeting
			unset($meeting['types'][$index]);
		}

		$time .= format_time($meeting['time']);

		//per Janet, don't need Closed meeting type now because it's implied
		// if (($index = array_search('C', $meeting['types'])) !== false) {
		// 	unset($meeting['types'][$index]);
		// }

		//append footnote to array
		if (!empty($meeting['types']) || !empty($meeting['notes'])) {
			//decide what this meeting's footnote should be
			$footnote = array_map('decode_types', $meeting['types']);
			if (!empty($meeting['notes'])) $footnote[] = $meeting['notes'];
			$footnote = implode(', ', $footnote);

			//add footnote if not full
			$count_footnotes = count($rows[$meeting['region_id']][$key]['footnotes']);
			//if (!is_array($rows[$meeting['region_id']][$key]['footnotes'])) dd($meeting);
			if (array_key_exists($footnote, $rows[$meeting['region_id']][$key]['footnotes'])) {
				$index = array_search($footnote, $rows[$meeting['region_id']][$key]['footnotes']);
				$time = $symbols[$index] . $time;
			} elseif ($count_footnotes < $count_symbols) {
				$rows[$meeting['region_id']][$key]['footnotes'][$footnote] = $symbols[$count_footnotes];
				$time = $symbols[$count_footnotes] . $time;
			}
		}

		//add meeting to row->day array
		$rows[$meeting['region_id']][$key]['days'][$meeting['day']][] = $time;
	}

	//add children from the database to the main regions array
	$categories = get_categories('taxonomy=tsml_region');
	foreach ($categories as $category) {

		$category->name = html_entity_decode($category->name);

		if (array_key_exists($category->term_id, $rows)) {
			usort($rows[$category->term_id], function($a, $b) {
				if ($a['group'] == $b['group']) return strcmp($a['location'], $b['location']);
				return strcmp($a['group'], $b['group']);
			});
		}

		//check if this is a sub_region
		if (array_key_exists($category->parent, $regions)) {

			//this region has a parent, so make sure that parent has an array for sub_regions
			if (!isset($regions[$category->parent]['sub_regions'])) $regions[$category->parent]['sub_regions'] = array();

			//skip if there aren't any rows for this sub_region
			if (!array_key_exists($category->term_id, $rows)) continue;

			//attach the sub_region
			$regions[$category->parent]['sub_regions'][$category->name] = $rows[$category->term_id];

		} elseif (array_key_exists($category->term_id, $regions)) {

			//this is a main region
			$regions[$category->term_id]['name'] = $category->name;
			$regions[$category->term_id]['description'] = $category->description;

			if (array_key_exists($category->term_id, $rows)) {
				$regions[$category->term_id]['rows'] = $rows[$category->term_id];
			}

		} else {

			//this isn't in the array -- no meetings are assigned

		}
	}

	//dd($regions);

	return $regions;
}


function filter_meetings($meetings){

  $filtered_meetings = array();
  $attendance_filtered_meetings = array();
  $tsmp_filtering_types_what = get_option('tsmp_filtering_types_what');
  //in_array($each_type,$tsmp_filtering_types_what)
  $tsmp_filtering_types_how = get_option('tsmp_filtering_types_how');
  if($tsmp_filtering_types_how == 'w'){
    //white list
    foreach ($meetings as $meeting) {

        $this_types = $meeting['types'];
        $this_intersection=array_intersect($this_types,$tsmp_filtering_types_what );
        if(sizeof($this_intersection) > 0){
          array_push($filtered_meetings,$meeting );
        }
    }
  }elseif($tsmp_filtering_types_how == 'b'){
    //black list
    foreach ($meetings as $meeting) {

        $this_types = $meeting['types'];
        $this_intersection=array_intersect($this_types,$tsmp_filtering_types_what );
        if(sizeof($this_intersection) == 0){
          array_push($filtered_meetings,$meeting );
        }
    }
  }else{
    //if not either, assume  no filtering
    $filtered_meetings = $meetings;
  }

  //now filter attendance options
  $attendance_option_filtering = get_option('attendance_option_filtering');
  if($attendance_option_filtering == 'all' || $attendance_option_filtering == ''){
    return $filtered_meetings;
  }else{
    //let's do some filtering
    if($attendance_option_filtering == 'online' || $attendance_option_filtering == 'in_person'){
      //if we are in here, we want to include hybrid meetings
        foreach ($filtered_meetings as $meeting) {
      $this_attendance_type = $meeting['attendance_option'];
      if($this_attendance_type == $attendance_option_filtering || $this_attendance_type == 'hybrid'){
        array_push($attendance_filtered_meetings, $meeting);
      }
    }
    }else{
      //if in_person_only, online_only, or hybrid, just match
      //need to set matching term
      $matching_term = "";
      if($attendance_option_filtering == 'online_only'){
        $matching_term = "online";
      }elseif ($attendance_option_filtering == 'in_person_only') {
        $matching_term = "in_person";
      }elseif ($attendance_option_filtering == 'hybrid') {
          $matching_term = "hybrid";
      }
      foreach ($filtered_meetings as $meeting) {
        $this_attendance_type = $meeting['attendance_option'];
        if($this_attendance_type == $matching_term){
          array_push($attendance_filtered_meetings, $meeting);
        }
      }

    }
  }
//Tell someone they are loved today

  return $attendance_filtered_meetings;
}
