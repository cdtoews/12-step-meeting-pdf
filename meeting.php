<?php

class meeting {

  public function __construct(array $meeting_array) {
      $this->meeting_array = $meeting_array;
      echo 'inside constructor, day is ' . $this->meeting_array['day'] . "\n";
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

  public function get_text($layout_type){
    if($layout_type == 1){
      return $this->get_full_meeting_text();
    }elseif($layout_type == 2){
      return $this->get_meeting_table();
    }
  }

  public function get_meeting_table(){
    $meetingtext = "";
    $meetingtext .= @$this->meeting_array['name'] . ". ";
    $meetingtext .= @$this->meeting_array['location'] . ". ";
    $meetingtext .= @$parts[0] . ". ";
    $meetingtext .= @$this->meeting_array['notes'] . ". ";
    $meetingtext .= @$this->meeting_array['location_notes'] . ". ";

    //let's strip carriage returns that might be in location notes and notes
    $meetingtext = str_replace("\r", "", $meetingtext);
    $meetingtext = str_replace("\n", "", $meetingtext);
    $meetingtext = str_replace("\t", "", $meetingtext);

      $table_text = '
        <table   width="100%" border="0" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
        <td rowspan="1" colspan="2" valign="top"><b>' .  $this->get_state() .  ', ' . $this->get_city() . ' ' . $this->meeting_array['time_formatted'] . '</b>
        </td>
        </tr>
        <tr>
        <td width="30" valign="top"><b>' .   implode (',' , $this->meeting_array['types'])  . '</b><br>
        </td>
        <td valign="top">' . $meetingtext . '<br>
        </td>
        </tr>
        </tbody>
        </table>';
      return $table_text;
  }

  public function get_full_meeting_text(){
    //cobble the meeting text togethers
    @$parts = explode(', ', $this->meeting_array['formatted_address']);
    $meetingtext = "";
    $meetingtext .= "<font='+1'><b>" . $this->get_state() . " ";
    $meetingtext .= $this->get_city() . "</b></font>, ";
    $meetingtext .= $this->meeting_array['time_formatted'] . ", ";
    $meetingtext .= "(" . implode (',' , $this->meeting_array['types']) . ") ";
    $meetingtext .= @$this->meeting_array['name'] . ". ";
    $meetingtext .= @$this->meeting_array['location'] . ". ";
    $meetingtext .= @$parts[0] . ". ";

    //let's only add notes and location notes if they exists. this was adding extra punctuation
    if(strlen(@$this->meeting_array['notes']) > 0){
      $meetingtext .= @$this->meeting_array['notes'] . ". ";
    }
    if(strlen(@$this->meeting_array['location_notes']) > 0){
      $meetingtext .= @$this->meeting_array['location_notes'] . ". ";
    }


    //let's strip carriage returns that might be in location notes and notes
    $meetingtext = str_replace("\r", "", $meetingtext);
    $meetingtext = str_replace("\n", "", $meetingtext);
    $meetingtext = str_replace("\t", "", $meetingtext);
    return $meetingtext;
  }

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




//implode (',' , $meeting['types'])

}
