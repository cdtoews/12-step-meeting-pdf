<?php

class meeting {

  public function __construct(array $meeting_arry) {
      $this->meeting_array = $meeting_arry;
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
  
  public function get_full_meeting_text(){
    //cobble the meeting text together
    @$parts = explode(', ', $this->meeting_array['formatted_address']);
    $meetingtext = "";
    $meetingtext .= "<font='+1'><b>" . $this->get_state() . " ";
    $meetingtext .= $this->get_city() . "</b></font>, ";
    $meetingtext .= $this->meeting_array['time_formatted'] . ", ";
    $meetingtext .= "(" . implode (',' , $this->meeting_array['types']) . ") ";
    $meetingtext .= @$this->meeting_array['name'] . ". ";
    $meetingtext .= @$this->meeting_array['location'] . ". ";
    $meetingtext .= @$parts[0] . ". ";
    $meetingtext .= @$this->meeting_array['notes'] . ". ";
    $meetingtext .= @$this->meeting_array['location_notes'] . ". ";

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
