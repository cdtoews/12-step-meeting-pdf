<?php

function tsmp_gen_page() {
  require_once('sample_post.php');
  require_once('sample_meeting.php');
  require_once('sample_column_html.php');
// settings page

  // https://cdnjs.cloudflare.com/ajax/libs/NicEdit/0.93/nicEdit.js
  ?>
  <head>
<link rel="stylesheet" type="text/css" href="mystyle.css">
<style>

.small_text{
  font-size: 75% !important;
}

.tr_with_border{
  border: thin solid !important;
}


</style>

  <script type="text/javascript" src="<?php echo plugins_url( 'js/nicedit.js', __FILE__ ) ; ?>"></script>

  <script>

  //setup listeners for validation of all fields
  function setEntryListeners(input) {
    ["input", "select", "contextmenu", "drop"].forEach(function(event) {
      input.addEventListener(event, function() {
        disableGenerate();
      });
    });
  }

  //setup listeners for validation of all fields if array of inputs
  function setEntryListeners_onarray(input, index) {
    alert(input.value);
    ["input", "select", "contextmenu", "drop"].forEach(function(event) {
      input.addEventListener(event, function() {
        disableGenerate();
      });
    });
  }


  //generate_warning
  function disableGenerate(){
    document.getElementById("submit").disabled = true;
    document.getElementById("generate_warning").innerHTML = "You Need to click on \"Save Changes\" (Step 1 above) before generating PDF";
  }

  function setValues(width, height) {
    document.getElementById("tsmp_width").value = width;
    document.getElementById("tsmp_height").value = height;
    disableGenerate();
  }

  //gather sample strings
  var samples = {
    "tsmp_outtro_html": <?php echo json_encode($sample_post); ?>,
    "tsmp_custom_meeting_html": <?php echo json_encode($sample_meeting); ?>,
    "tsmp_column_html" : <?php echo json_encode($sample_column_html); ?>,
  };

//load sample strings when called
  function loaddata(filename,textid){
    var textbox = document.getElementById(textid);
    textbox.value = samples[filename];

  }

  var areas = {  };

//tsmp_outtro_html_load
  function toggleArea1(id) {
        area1 = areas[id];
        if(!area1) {
                area1 = new nicEditor({externalCSS : 'css/main.css', fullPanel : true}).panelInstance(id ,{hasPanel : true});
                var loadbutton = document.getElementById(id + "_load");
                if(loadbutton){
                  loadbutton.disabled = true;
                }
        } else {
                area1.removeInstance(id );
                var loadbutton = document.getElementById(id + "_load");
                if(loadbutton){
                  loadbutton.disabled = false;
                }
                area1 = null;
        }
        areas[id] = area1;
  }

  //hide types list if not Filtering
  function hide_show_types(){
    //get radio value

    if(document.getElementById("no_filtering").checked){
      document.getElementById('type_td').style.display = "none";
    }else{
      document.getElementById('type_td').style.display = "block";
    }

  }

  function updateVarView(){
    //let's determine which to hide and which to show
    hide_show_types();
    //read id tsmp_layout
    var x = document.getElementById("tsmp_layout").selectedIndex;
    var dropDownValue = document.getElementsByTagName("option")[x].value;

    var divsToHide;
    var divsToShow;
    var col2divsToShow;
    var col2divsToHide;
    if (dropDownValue.startsWith("column")){
      divsToHide = document.getElementsByClassName("table_row");
      divsToShow = document.getElementsByClassName("column_row");
    } else if (dropDownValue.startsWith("table")){
      divsToHide = document.getElementsByClassName("column_row");
      divsToShow = document.getElementsByClassName("table_row");
    }else{
      alert("what is going on?");
    }

    //hide/show column2 variable
    if(dropDownValue.endsWith("2")){
      col2divsToShow = document.getElementsByClassName("column2_row");
      col2divsToHide = [];
    }else{
      col2divsToHide = document.getElementsByClassName("column2_row");//
      col2divsToShow = [];
    }

    for(var i = 0; i < col2divsToHide.length; i++){
        col2divsToHide[i].style.display = "none"; // depending on what you're doing
    }
    for(var i = 0; i < col2divsToShow.length; i++){
        col2divsToShow[i].style.display = ""; // depending on what you're doing
    }

    for(var i = 0; i < divsToHide.length; i++){
        divsToHide[i].style.display = "none"; // depending on what you're doing
    }
    for(var i = 0; i < divsToShow.length; i++){
        divsToShow[i].style.display = ""; // depending on what you're doing
    }

    //see if meeting customer HTML should show
    // Get the checkbox
    var custom_checkBox = document.getElementById("tsmp_set_custom_meeting_html");
    // Get the output text
    var custom_tr = document.getElementById("custom_meeting_html_tr");

    // If the checkbox is checked, display the output text
    if (custom_checkBox.checked == true){
      custom_tr.style.display = "";
    } else {
      custom_tr.style.display = "none";
    }

    //let's check the filename checkbox thingie
    var saveCheckbox = document.getElementById("tsmp_set_save_file");
    if (saveCheckbox.checked == true){
      document.getElementById("tr_tsmp_save_file_name").style.display = "";
    } else {
      document.getElementById("tr_tsmp_save_file_name").style.display = "none";
    }
  }
  </script>
</head>
<body>
  <div class="wrap">
       <h2>PDF Generation Settings</h2>
       <form id="save_settings" method="post" action="options.php">
           <?php settings_fields('tsmp-settings-group'); ?>
          <?php do_settings_sections( 'tsmp-settings-group' ); ?>
           <table class="form-table">
             <tr valign="top">
               <th scope="row">
                 <span title="Different layouts for the pdf Columns1 is the default, meetings grouped by day and printed in columns. table1 groups by region, and makes a table, each row being a location">
                    Page Layout
                  </span>
                </th>
                 <td>
                   <select onchange="updateVarView()" id="tsmp_layout" name="tsmp_layout" value="<?php echo get_option('tsmp_layout'); ?>" />
                      <?php
                            $tsmp_layout = get_option('tsmp_layout');
                            $layouts = array("columns1","columns2", "table1");
                            foreach ($layouts as $layout) {
                              echo ' <option value="' . $layout   .  '" ' . ($tsmp_layout == $layout ? 'selected' : '') .  '>' . $layout . '</option>';
                            }
                       ?>
                   </select>&nbsp;&nbsp;&nbsp;&nbsp;
                   <?php
                     echo '<a target="_blank" href="' . plugins_url( '/column1_sample.pdf', __FILE__ ) . '" >Column1 Sample</a>&nbsp;&nbsp;&nbsp;&nbsp; ';
                     echo '<a target="_blank" href="' . plugins_url( '/column2_sample.pdf', __FILE__ ) . '" >Column2 Sample</a>&nbsp;&nbsp;&nbsp;&nbsp; ';
                     echo '<a target="_blank" href="' . plugins_url( '/table1_sample.pdf', __FILE__ ) . '" >Table1 Sample</a>&nbsp;&nbsp;&nbsp;&nbsp; ';
                   ?>
                 </td>
             </tr>

             <?php
               $attendance_option_filtering = get_option('attendance_option_filtering');

               //if not properly defined, set to none
               if($attendance_option_filtering != 'online' &&
               $attendance_option_filtering != 'in_person' &&
               $attendance_option_filtering != 'online_only' &&
               $attendance_option_filtering != 'in_person_only' &&
               $attendance_option_filtering != 'hybrid'){
                 $attendance_option_filtering = 'all';
               }
              //Give someone a hug today
            ?>

             <tr valign="top">
        <th scope="row">Filtering by Attendance Options</th>






                   <td style="vertical-align:top">

                     <input type="radio" id="attendance_all" name="attendance_option_filtering" value="all"  <?php echo ($attendance_option_filtering == 'all' ? 'checked' : '') ?>>
                      <label for="attendance_all">All (no filtering)</label><br>

                      <input type="radio" id="attendance_online" name="attendance_option_filtering" value="online"   <?php echo ($attendance_option_filtering == 'online' ? 'checked' : '') ?>>
                      <label for="attendance_online">Online (including Hybrid)</label><br>

                      <input type="radio" id="attendance_online_only" name="attendance_option_filtering" value="online_only"   <?php echo ($attendance_option_filtering == 'online_only' ? 'checked' : '') ?>>
                      <label for="attendance_online">Online only (not including Hybrid)</label><br>

                      <input type="radio" id="attendance_in_person" name="attendance_option_filtering" value="in_person"   <?php echo ($attendance_option_filtering == 'in_person' ? 'checked' : '') ?>>
                      <label for="attendance_in_person">In Person (including Hybrid) </label><br>

                      <input type="radio" id="attendance_in_person_only" name="attendance_option_filtering" value="in_person_only"   <?php echo ($attendance_option_filtering == 'in_person_only' ? 'checked' : '') ?>>
                      <label for="attendance_in_person">In Person only  (not including Hybrid)</label><br>

                      <input type="radio" id="attendance_hybrid" name="attendance_option_filtering" value="hybrid"   <?php echo ($attendance_option_filtering == 'hybrid' ? 'checked' : '') ?>>
                      <label for="attendance_hybrid">Hybrid</label>
                   </td>




             </tr>

             <tr>
              <td colspan="2">
                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Attendance and type filters are combined with an <a href="https://preview.redd.it/ecnonykb5cv51.png?width=960&crop=smart&auto=webp&s=191b56595a937d5f7e18d461a53a79b6cd21ce3b" target="_blank">AND</a>  (as opposed to an OR)</strong>
               </td>
             </tr>


              <?php
                $tsmp_filtering_types_how = get_option('tsmp_filtering_types_how');
                $tsmp_filtering_types_what = get_option('tsmp_filtering_types_what');

                //if how not properly defined, set to none
                if($tsmp_filtering_types_how != 'w' && $tsmp_filtering_types_how != 'b' && $tsmp_filtering_types_how != 'n'){
                  $tsmp_filtering_types_how = 'n';
                }

                $meeting_types_in_use = get_option('tsml_types_in_use');

                //pull eeting types from other plugin
                global $tsml_programs;
                $tsml_program = get_option('tsml_program', 'aa');

                if(is_array($tsmp_filtering_types_what)){
                  $tsmp_filtering_types_displayed =  array_unique(array_merge ($tsmp_filtering_types_what, $meeting_types_in_use));
                }else{
                  $tsmp_filtering_types_displayed =   $meeting_types_in_use;
                }


                if(is_array($tsmp_filtering_types_displayed)){
                  sort($tsmp_filtering_types_displayed);
                }else{
                  $tsmp_filtering_types_displayed = [];
                }

                if(is_array($tsmp_filtering_types_what)){
                  sort($tsmp_filtering_types_what);
                }else{
                  $tsmp_filtering_types_what = [];
                }

                

               ?>

             <tr valign="top"><th scope="row">Filtering by Types</th>
               <td>
                 <table>
                   <tr>
                     <td style="vertical-align:top">
                       Filter how:<br>
                       <input type="radio" id="white_list" name="tsmp_filtering_types_how" value="w" onclick="hide_show_types(this);" <?php echo ($tsmp_filtering_types_how == 'w' ? 'checked' : '') ?>>
                        <label for="white_list">White List</label><br>
                        <input type="radio" id="black_list" name="tsmp_filtering_types_how" value="b" onclick="hide_show_types(this);"  <?php echo ($tsmp_filtering_types_how == 'b' ? 'checked' : '') ?>>
                        <label for="black_list">Black List</label><br>
                        <input type="radio" id="no_filtering" name="tsmp_filtering_types_how" value="n" onclick="hide_show_types(this);"  <?php echo ($tsmp_filtering_types_how == 'n' ? 'checked' : '') ?>>
                        <label for="no_filtering">No Filtering</label>
                     </td>
                     <td width='1' style="border-right: 3px solid #cdd0d4;">
                     </td>
                     <td id="type_td" style="vertical-align:top">
                       filter what: ( <a href="https://github.com/code4recovery/12-step-meeting-list/blob/master/includes/variables.php#L501" target="_blank"> list of types</a> )
                       <br>

                       <?php
                       foreach ($tsml_programs[$tsml_program]['types'] as $key => $type) {
                         echo "<input class='type_what_boxes' type='checkbox' name='tsmp_filtering_types_what[]' value='" . $key . "'" . (@in_array($key,$tsmp_filtering_types_what) ? 'checked' : '') ."  >" . $type . "<br>";

                        //remove any meeting types from display array, this will leave only custom types
                         if (($skey = array_search($key, $tsmp_filtering_types_displayed)) !== false) {
                                unset($tsmp_filtering_types_displayed[$skey]);
                            }

                       }
                       echo "custom types:<br>";
                       foreach ($tsmp_filtering_types_displayed as $each_type) {
                         echo "<input class='type_what_boxes' type='checkbox' name='tsmp_filtering_types_what[]' value='" . $each_type . "'" . (@in_array($each_type,$tsmp_filtering_types_what) ? 'checked' : '') ."  >" . $each_type . "<br>";

                       }

                 ?>
                     </td>
                   </tr>
                 </table>
               </td>
             </tr>

             <tr valign="top"><th scope="row">Paper Size</th>
                 <td>
                     <table>
                        <tr>
                          <td><button type=button onclick="setValues(216,279)">Letter Portrait</button></td>
                          <td><button type=button onclick="setValues(279,216)">Letter Landscape</button></td>
                        </tr>
                        <tr>
                          <td><button type=button onclick="setValues(216,356)">Legal Portrait</button></td>
                          <td><button type=button onclick="setValues(356,216)">Legal Landscape</button></td>
                        </tr>
                      </table>
                 </td>
             </tr>
             <tr valign="top"><th scope="row">Page Width in mm</th>
                 <td><input type="text" id="tsmp_width" name="tsmp_width" value="<?php echo get_option('tsmp_width'); ?>" /></td>
             </tr>
             <tr valign="top"><th scope="row">Page Height in mm</th>
                 <td><input type="text" id="tsmp_height" name="tsmp_height" value="<?php echo get_option('tsmp_Height'); ?>" /></td>
             </tr>


               <tr valign="top"><th scope="row">Font Size<font size="-2">(decimals allowed)</font></th>
                   <td><input type="text" id="tsmp_font_size" name="tsmp_font_size" value="<?php echo get_option('tsmp_font_size'); ?>" /></td>
               </tr>
               <tr valign="top"><th scope="row">Header Font Size<font size="-2">(decimals allowed)</font></th>
                   <td><input type="text" id="tsmp_header_font_size" name="tsmp_header_font_size" value="<?php echo get_option('tsmp_header_font_size'); ?>" /></td>
               </tr>

               <tr class="column_row"  valign="top"><th scope="row"></th>
                   <td bgcolor="#FFFFFF">
                     EXPERIMENTAL:<br>
                     <input type="checkbox" id="tsmp_auto_font" name="tsmp_auto_font" value="1" <?php echo ( (get_option('tsmp_auto_font') == 1) ? "checked": ""); ?>
                      checked( 1, $options['checkbox_example'], false ) />
                    <label for="tsmp_auto_font">Automatically determine optimal Font Size</label><br>
                    <input size="2" type="text" id="tsmp_desired_page_count" name="tsmp_desired_page_count" value="<?php echo get_option('tsmp_desired_page_count'); ?>" />
                    <label for="tsmp_desired_page_count">Desired page count</label><br>
                    <p class="small_text">This is for columns layout only. It will start with the set font size, and try to establish the optimal font size for the desired page count.<br>
                      optimal font size being defined as (optimal_font_size + 0.1) would result in (desired page count +1)<br>
                      once the optimal size is determined, the size is set for future, and the PDF is generated<br>
                      This process can take 30-60 seconds to complete</p>
                   </td>
               </tr>
               <tr valign="top"><th scope="row">Margin</th>
                   <td><input type="text" id="tsmp_margin" name="tsmp_margin" value="<?php echo get_option('tsmp_margin'); ?>" /></td>
               </tr>

               <tr valign="top"><th scope="row">Use Custom meeting HTML</th>
                   <td><input onchange="updateVarView()" type="checkbox" id="tsmp_set_custom_meeting_html" name="tsmp_set_custom_meeting_html" value="1" <?php echo ( (get_option('tsmp_set_custom_meeting_html') == 1) ? "checked": ""); ?>
                    checked( 1, $options['checkbox_example'], false ) /></td>
               </tr>

               <tr  id="custom_meeting_html_tr" class="column_row tr_with_border"  valign="top">
                 <td scope="row"><b>Meeting HTML</b><br><br>
                   <button type=button onclick="toggleArea1('tsmp_custom_meeting_html');">Toggle  Editor</button><br><br>
                   <button type=button id="tsmp_custom_meeting_html_load" onclick="loaddata('tsmp_custom_meeting_html','tsmp_custom_meeting_html')" >Load Sample Data</button>
                   <p class="small_text">Generator will replace the following variables with actual values<br>
                   __types__<br>
                   __time__<br>
                   __day_of_week__<br>
                   __title__<br>
                   __street_address__<br>
                   __region__<br>
                   __subregion__<br>
                   __city__<br>
                   __state__<br>
                   __location__<br>
                   __notes__<br>
                   __location_notes__<br>
                   __formatted_address__<br>
                   __conference_url__<br>
                   __conference_phone__
                   <p>

                 </td>
                 <td>
                   <textarea rows="10" cols="70" id="tsmp_custom_meeting_html" name="tsmp_custom_meeting_html" ><?php echo get_option('tsmp_custom_meeting_html'); ?></textarea>
                 </td>
               </tr>


               <tr class="column_row" align="center">
                  <td colspan="2"><h2>Column Variables</h2></td>
               </tr>
               <tr class="column_row"  valign="top"><th scope="row">Header Text</th>
                   <td><input type="text" id="tsmp_header" name="tsmp_header" value="<?php echo get_option('tsmp_header'); ?>" /></td>
               </tr>
               <tr class="column2_row"  valign="top"><th scope="row">Column2 time indent</th>
                   <td><input type="text" id="tsmp_column2_indent" name="tsmp_column2_indent" value="<?php echo get_option('tsmp_column2_indent'); ?>" /></td>
               </tr>


               <tr class="column_row" ><td colspan="2"><font -2>note on html for before and after, each div rendered seperately<br>
                 and column breaks will only fall on close of div<br>
               html for specific column works differently.</font>
               </td></tr>
               <tr  class="column_row tr_with_border"  valign="top"><th scope="row">HTML before meetings<br>
               <button type=button onclick="toggleArea1('tsmp_intro_html');">Toggle  Editor</button></th>
                   <td><textarea rows="10" cols="70" id="tsmp_intro_html" name="tsmp_intro_html" ><?php echo get_option('tsmp_intro_html'); ?></textarea></td>
               </tr>
				<!-- ############    HTML ON SPECIFIC COLUMNS    ############   -->
               <tr  >
                 <td colspan="2"><font -2>HTML on sepcific column will only function with column layouts<br>
                 If you put too much in the column, you can break the layout.<br>
               With great power comes great responsibility </font></td>

<?php
            // let's load column html params
            $tsmp_column_html_array = get_option("tsmp_column_html");
            
            if(!is_array($tsmp_column_html_array)){
              
              $tsmp_column_html_array = [];
            }

            $tsmp_default_values = array('enable' => 0,
                                          'html' => '',
                                          'page_num' => 0,
                                          'column_num' => 0
                                        );
            foreach($tsmp_default_values as $key => $value){
                if(!array_key_exists( $key, $tsmp_column_html_array) ) {
                  $tsmp_column_html_array[$key] = $value;
                }
            }
 ?>

                 </tr>

                 <tr class="column_row tr_with_border"  valign="top"><th scope="row">HTML on specific Column<br>
               <button type=button onclick="toggleArea1('tsmp_column_html[html]');">Toggle  Editor</button><br>
                <button type=button id="tsmp_column_html_load" onclick="loaddata('tsmp_column_html','tsmp_column_html[html]')" >Load Sample Data</button>
              <br><br>
              enabled
              <input onchange="updateVarView()" type="checkbox" id="tsmp_column_html[enable]" name="tsmp_column_html[enable]" value="1" <?php echo ( ($tsmp_column_html_array['enable'] == 1) ? "checked": ""); ?>
               checked( 1, $options['checkbox_example'], false ) /><br>
               Page Number:
               <input type="text" size="2" id="tsmp_column_html[page_num]" name="tsmp_column_html[page_num]" value="<?php echo $tsmp_column_html_array['page_num'] ; ?>" />
               <br>
               Column Number:
               <input type="text" size="3" id="tsmp_column_html[column_num]" name="tsmp_column_html[column_num]" value="<?php echo $tsmp_column_html_array['column_num'] ; ?>" />
               <br>


             </th>
                   <td><textarea rows="10" cols="70" id="tsmp_column_html[html]" name="tsmp_column_html[html]" ><?php echo $tsmp_column_html_array['html']; ?></textarea></td>
               </tr>

					<!-- ############    HTML AFTER MEETINGS    ############   -->
               <tr class="column_row tr_with_border"  valign="top"><th scope="row">HTML after meetings<br><br>
               <button type=button onclick="toggleArea1('tsmp_outtro_html');">Toggle  Editor</button><br><br>
               <button type=button id="tsmp_outtro_html_load" onclick="loaddata('tsmp_outtro_html','tsmp_outtro_html')" >Load Sample Data</button>
             </th>
                   <td><textarea rows="10" cols="70" id="tsmp_outtro_html" name="tsmp_outtro_html" ><?php echo get_option('tsmp_outtro_html'); ?></textarea></td>
               </tr>
               <tr class="column_row"  valign="top"><th scope="row">Column Count</th>
                   <td><input type="text" id="tsmp_column_count" name="tsmp_column_count" value="<?php echo get_option('tsmp_column_count'); ?>" /></td>
               </tr>
               <tr class="column_row"  valign="top"><th scope="row">Column Padding</th>
                   <td><input type="text" id="tsmp_column_padding" name="tsmp_column_padding" value="<?php echo get_option('tsmp_column_padding'); ?>" /></td>
               </tr>
               <tr  class="table_row" align="center">
                  <td colspan="2"><h2>Table Variables</h2></td>
               </tr>
               <tr class="table_row"  valign="top"><th scope="row">Starting Page Number</th>
                   <td><input type="text" id="tsmp_first_page_no" name="tsmp_first_page_no" value="<?php echo get_option('tsmp_first_page_no'); ?>" /></td>
               </tr>
               <tr  class="table_row"  valign="top"><th scope="row">Include Type Index?</th>
                   <td>

                     <input id="include_radio1" type="radio" name="tsmp_include_index" value="1" <?php echo ((get_option('tsmp_include_index') == 1) ? 'checked' : '')?>  > Yes<br>
                     <input id="include_radio2" type="radio" name="tsmp_include_index" value="0" <?php echo ((get_option('tsmp_include_index') == 0) ? 'checked' : '')?> > No</td>
               </tr>
               <tr class="table_row" valign="top"><th scope="row">New Page for each region</th>
                   <td><input onchange="updateVarView()" type="checkbox" id="tsmp_table_region_new_page" name="tsmp_table_region_new_page" value="1" <?php echo ( (get_option('tsmp_table_region_new_page') == 1) ? "checked": ""); ?>
                    checked( 1, $options['checkbox_example'], false ) /></td>
               </tr>





               <tr valign="top"><th scope="row">Save a copy of File</th>
                   <td><input onchange="updateVarView()" type="checkbox" id="tsmp_set_save_file" name="tsmp_set_save_file" value="1" <?php echo ( (get_option('tsmp_set_save_file') == 1) ? "checked": ""); ?>
                    checked( 1, $options['checkbox_example'], false ) /></td>
               </tr>
               <tr class="column_row" id="tr_tsmp_save_file_name"  valign="top"><th scope="row">File Name</th>
                   <td><?php echo get_home_path();  ?>    <input size=100 type="text" id="tsmp_save_file_name" name="tsmp_save_file_name" value="<?php echo get_option('tsmp_save_file_name'); ?>" /></td>
               </tr>

           </table>



       <p class="submit">
        step 1, If you changed values above:<br>   <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
           </p>
       </form>

    <form id="generate_da_pdf" method="get" class="form-horizontal" action="admin-ajax.php">
     <input name="action" value="step_pdf" type="hidden">
<div class="form-group"> <label class="col-md-4 control-label" for="submit"></label>
step 2 <div class="col-md-4"> <button id="submit"
name="submit" class="button-primary" >Generate PDF</button><div id="generate_warning"></div>
   </div>
 </div>
    </form>
</div>
<script>


//setup the listners to disable generate button
setEntryListeners(document.getElementById("tsmp_layout"));
setEntryListeners(document.getElementById("tsmp_width"));
setEntryListeners(document.getElementById("tsmp_height"));
setEntryListeners(document.getElementById("tsmp_font_size"));
setEntryListeners(document.getElementById("tsmp_header_font_size"));
setEntryListeners(document.getElementById("tsmp_margin"));
setEntryListeners(document.getElementById("tsmp_header"));
setEntryListeners(document.getElementById("tsmp_outtro_html"));
setEntryListeners(document.getElementById("tsmp_column_count"));
setEntryListeners(document.getElementById("tsmp_column_padding"));
setEntryListeners(document.getElementById("tsmp_first_page_no"));
setEntryListeners(document.getElementById("include_radio1"));
setEntryListeners(document.getElementById("include_radio2"));
setEntryListeners(document.getElementById("tsmp_auto_font"));
setEntryListeners(document.getElementById("tsmp_desired_page_count"));
setEntryListeners(document.getElementById("tsmp_set_custom_meeting_html"));
setEntryListeners(document.getElementById("tsmp_custom_meeting_html"));
setEntryListeners(document.getElementById("tsmp_save_file_name"));
setEntryListeners(document.getElementById("tsmp_set_save_file"));
setEntryListeners(document.getElementById("tsmp_table_region_new_page"));
setEntryListeners(document.getElementById("tsmp_column_html[enable]"));
setEntryListeners(document.getElementById("tsmp_column_html[page_num]"));
setEntryListeners(document.getElementById("tsmp_column_html[column_num]"));
setEntryListeners(document.getElementById("tsmp_column_html[html]"));
setEntryListeners(document.getElementById("no_filtering"));
setEntryListeners(document.getElementById("white_list"));
setEntryListeners(document.getElementById("black_list"));
setEntryListeners(document.getElementById("attendance_all"));
setEntryListeners(document.getElementById("attendance_online"));
setEntryListeners(document.getElementById("attendance_in_person"));
setEntryListeners(document.getElementById("attendance_online_only"));
setEntryListeners(document.getElementById("attendance_in_person_only"));
setEntryListeners(document.getElementById("attendance_hybrid"));




var typeboxes = document.getElementsByClassName("type_what_boxes");
for(var i = 0; i < typeboxes.length; i++)
{
   setEntryListeners(typeboxes[i]);
}



updateVarView()
</script>


<?php

//for debugging:
if(false){
  echo "<h2> Options </h2><br>";
  echo "tsmp_column_padding :" .  get_option('tsmp_column_padding') . "<br>";
  echo "tsmp_column_count :" .  get_option('tsmp_column_count') . "<br>";
  echo "tsmp_margin :" .  get_option('tsmp_margin') . "<br>";
  echo "tsmp_font_size :" .  get_option('tsmp_font_size') . "<br>";
  echo "tsmp_header :" .  get_option('tsmp_header') . "<br>";
  echo "attendance_option_filtering :" .  get_option('attendance_option_filtering') . "<br>";
  echo "tsmp_filtering_types_how :" .  get_option('tsmp_filtering_types_how') . "<br>";
  echo "tsmp_filtering_types_what :";
print_r(get_option('tsmp_filtering_types_what') );
  echo "<br>";
echo "<hr>";

  echo "tsmp_column_html :";
print_r(get_option('tsmp_column_html') );
  echo "<br>";
echo "<hr>";

  echo "tsmp_intro_html :" .  get_option('tsmp_intro_html') . "<br>";
  echo "<hr>";
  echo "tsmp_outtro_html :" .  get_option('tsmp_outtro_html') . "<br>";

//$tsmp_column_html_array = get_option("tsmp_column_html");

	echo "tsmp_column_html (array): " . get_option("tsmp_column_html") . "<br>";


echo "<hr>";
  echo "<br>";
echo "<h2>all options</h2><br>";

$all_options = wp_load_alloptions();

foreach ( $all_options as $name => $value ) {
  echo "-- " . $name . " : ";
  $thisValue = get_option($name);
  print_r($thisValue);
  echo "<br><br>";

}


echo "<h2>post types:</h2> <br>";
  foreach ( get_post_types( '', 'names' ) as $post_type ) {
     echo '<p>' . $post_type . '</p><br>';
  }


echo "<hr>";
echo "public custom post types <br>";
$args = array(
   'public'   => true,
   '_builtin' => false
);

$output = 'names'; // names or objects, note names is the default
$operator = 'and'; // 'and' or 'or'

$post_types = get_post_types( $args, $output, $operator );

foreach ( $post_types  as $post_type ) {

   echo '<p>' . $post_type . '</p><br>';
}



echo "<hr>";

echo "getting taxonomy for _____ <br>";
$regions = get_terms('tsml_region');
  // $results = array();
  foreach ($regions as $region) {
    echo $region->slug . " ----  " . $region->name . "<br><br>";
    // $results[] = array(
    //   'id'				=> $region->slug,
    //   'value'				=> html_entity_decode($region->name),
    //   'type'				=> 'region',
    //   'tokens'			=> tsml_string_tokens($region->name),
    // );
  }

echo "<hr>";
echo "custom taxonomies <br>";

$terms = get_terms([

    'hide_empty' => false,
]);

foreach ($terms as $term){
  var_dump($term);
  echo  "<br><br>";
}


}
?>

</body>

  <?php

}