<?php

class meeting {

  public function __construct(array $meeting_array) {
      $this->meeting_array = $meeting_array;
      //echo 'inside constructor, day is ' . $this->meeting_array['day'] . "\n";
  }

  public function get_formatted_day(){
    echo 'inside formatted_day, day is ' . $this->meeting_array['day'] . "\n";
    $day = $this->meeting_array['day'];
    if($day == 0){
      return  "Sunday";
    }elseif ($day == 1){
      return "Monday";
    }elseif ($day == 2){
      return  "Tuesday";
    }elseif ($day == 3){
      return  "Wednesday";
    }elseif ($day == 4){
      return  "Thursday";
    }elseif ($day == 5){
      return  "Friday";
    }elseif ($day == 6){
      return "Saturday";
    }else{
      return  "Unknown Day";
    }
  }

  //this function is fairly useless at the moment, since adding the custom meeting html thingie
  public function get_text($layout_type){
    if($layout_type == 1){
      return $this->get_full_meeting_text();
    }elseif($layout_type == 2){
      return $this->get_full_meeting_text();
    }
  }

  // public function get_meeting_table(){
  //   $meetingtext = "";
  //   $meetingtext .= @$this->meeting_array['name'] . ". ";
  //   $meetingtext .= @$this->meeting_array['location'] . ". ";
  //   $meetingtext .= @$parts[0] . ". "; //street address
  //   //let's only add notes and location notes if they exists. this was adding extra punctuation
  //   if(strlen(@$this->meeting_array['notes']) > 0){
  //     $meetingtext .= @$this->meeting_array['notes'] . ". ";
  //   }
  //   if(strlen(@$this->meeting_array['location_notes']) > 0){
  //     $meetingtext .= @$this->meeting_array['location_notes'] . ". ";
  //   }
  //
  //
  //   //let's strip carriage returns that might be in location notes and notes
  //   $meetingtext = str_replace("\r", "", $meetingtext);
  //   $meetingtext = str_replace("\n", "", $meetingtext);
  //   $meetingtext = str_replace("\t", "", $meetingtext);
  //
  //   $table_text = '
  //       <table   width="100%" border="0" cellspacing="0" cellpadding="0">
  //       <tbody>
  //       <tr>
  //       <td rowspan="1" colspan="2" valign="top"><b>' .  $this->get_state() .  ', ' . $this->get_city() . ' ' . $this->meeting_array['time_formatted'] . '</b>
  //       </td>
  //       </tr>
  //       <tr>
  //       <td width="30" valign="top"><b>' .   implode (',' , $this->meeting_array['types'])  . '</b><br>
  //       </td>
  //       <td valign="top">' . $meetingtext . '<br>
  //       </td>
  //       </tr>
  //       </tbody>
  //       </table>';
  //     write_log( $table_text );
  //     return $table_text;
  // }

  public function get_full_meeting_text(){
    //first let's check if we will be using custom text, shall we
    $custom_meeting_set = get_option('tsmp_set_custom_meeting_html');

    //write_log("custom_meeting_set = " . $globals[$custom_meeting_set]);
    if($custom_meeting_set == 1){
      $this_meeting_html = get_option('tsmp_custom_meeting_html');
      @$parts = explode(', ', $this->meeting_array['formatted_address']);
      $meeting_variables = array
        (
        array("__types__", implode (',' ,( $this->meeting_array['types'] ??  []))) ,
        array("__time__", $this->meeting_array['time_formatted'] ),
        array("__day_of_week__",$this->get_formatted_day() ),
        array("__street_address__", @$parts[0] ),
        array("__city__", @$parts[1] ),
        array("__title__",  $this->meeting_array['name'] ),
        array("__state__", $this->get_state() ),
        array("__region__", $this->meeting_array['region'] ),
        array("__subregion__",( $this->meeting_array['sub_region'] ?? null) ),
        array("__location__", @$this->meeting_array['location'] ),
        array("__notes__", @$this->meeting_array['notes'] ),
        array("__location_notes__", @$this->meeting_array['location_notes'] ),
        array("__formatted_address__", @$this->meeting_array['formatted_address']),
        array("__conference_url__", @$this->meeting_array['conference_url']),
        array("__conference_phone__", @$this->meeting_array['conference_phone'])

        );

      foreach($meeting_variables as $item) {
        $this_meeting_html = str_replace($item[0], $item[1], $this_meeting_html);

      }
      return $this_meeting_html;


    }else{
      //used for debugging:
      foreach($this->meeting_array as $key => $value) {
        write_log( "[$key]  =>  $value");
      }
      write_log("\n\n");

      //cobble the meeting text together
      @$parts = explode(', ', $this->meeting_array['formatted_address']);
      $meetingtext = "";
      $meetingtext .= "<font='+1'><b>" . $this->get_state() . " ";
      $meetingtext .= $this->get_city() . "</b></font>, ";
      $meetingtext .= $this->meeting_array['time_formatted'] . ", ";
      $meetingtext .= "(" . implode (',' , (array)$this->meeting_array['types']) . ") ";
      $meetingtext .= @$this->meeting_array['name'] . ". ";
      $meetingtext .= @$this->meeting_array['location'] . ". ";
      $meetingtext .= @$parts[0] . ". "; //street address

      //let's only add notes and location notes if they exists. this was adding extra punctuation
      if(strlen(@$this->meeting_array['notes']) > 0){
        $meetingtext .= @$this->meeting_array['notes'];
        // if notes doesn't end with a period, add one. otherwise add just a space
        $meetingtext .= (strcmp(substr( @$this->meeting_array['notes'], -1) , '.') == 0 ? ' ' : '. ');
      }
      if(strlen(@$this->meeting_array['location_notes']) > 0){
        $meetingtext .= @$this->meeting_array['location_notes'];
        //if location_notes doesn't end with a period, add one, otherwise just add a space
        $meetingtext .= (strcmp(substr( @$this->meeting_array['location_notes'], -1) , '.') == 0 ? ' ' : '. ');
      }


      //let's strip carriage returns that might be in location notes and notes
      $meetingtext = str_replace("\r", "", $meetingtext);
      $meetingtext = str_replace("\n", "", $meetingtext);
      $meetingtext = str_replace("\t", "", $meetingtext);
      return $meetingtext;

    }
  }// end of get_full_meeting_text

  public function get_state(){
    $formatted_adddress = @$this->meeting_array['formatted_address'];
    $address_split = explode(", ", $formatted_adddress);
    if(sizeof($address_split) == 4){
      return explode(" ", trim($address_split[2]))[0];
    }elseif(sizeof($address_split) == 3){
      return $address_split[1];
    }else{
      return  "unknown State";
    }
  }

  public function get_city(){
    $formatted_adddress = @$this->meeting_array['formatted_address'];
    $address_split = explode(", ", $formatted_adddress);
    if(sizeof($address_split) == 4){
      return $address_split[1];
    }elseif(sizeof($address_split) == 3){
      return $address_split[0];
    }else{
      return "unknown City";
    }
  }


}
