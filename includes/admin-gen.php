<?php

function tsmp_gen_page() {
  require_once('sample_post.php');
// settings page:
  ?>
  <script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> 
  
  <script>
  
  //setup listeners for validation of all fields
  function setEntryListeners(input) {
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
  }


  var samples = {
    "tsmp_outtro_html": <?php echo json_encode($sample_post); ?>,
    
  };
  
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


  function updateVarView(){
    //let's determine which to hide and which to show
    //read id tsmp_layout
    var x = document.getElementById("tsmp_layout").selectedIndex;
    var dropDownValue = document.getElementsByTagName("option")[x].value;
    
    var divsToHide;
    var divsToShow;
    if (dropDownValue.startsWith("column")){
      divsToHide = document.getElementsByClassName("table_row"); 
      divsToShow = document.getElementsByClassName("column_row"); 
    } else if (dropDownValue.startsWith("table")){
      divsToHide = document.getElementsByClassName("column_row"); 
      divsToShow = document.getElementsByClassName("table_row"); 
    }else{
      alert("what is going on?");
    }
    
    
    
    for(var i = 0; i < divsToHide.length; i++){
        divsToHide[i].style.display = "none"; // depending on what you're doing
    }
    for(var i = 0; i < divsToShow.length; i++){
        divsToShow[i].style.display = ""; // depending on what you're doing
    }
    
    
    
  }


  </script>

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
                            $layouts = array("columns1", "table1");//for now we will leave out 'columns2'
                            foreach ($layouts as $layout) {
                              echo ' <option value="' . $layout   .  '" ' . ($tsmp_layout == $layout ? 'selected' : '') .  '>' . $layout . '</option>';
                            }
                      
                      
                       ?>
                 
                 
                 
                   </select>&nbsp;&nbsp;&nbsp;&nbsp;
                   <?php  
                   echo '<a target="_blank" href="' . plugins_url( '/column1_sample.pdf', __FILE__ ) . '" >Column1 Sample</a>&nbsp;&nbsp;&nbsp;&nbsp; '; 
                   echo '<a target="_blank" href="' . plugins_url( '/table1_sample.pdf', __FILE__ ) . '" >Table1 Sample</a>&nbsp;&nbsp;&nbsp;&nbsp; '; 
                   
                   ?>
                  
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
               <tr valign="top"><th scope="row">Margin</th>
                   <td><input type="text" id="tsmp_margin" name="tsmp_margin" value="<?php echo get_option('tsmp_margin'); ?>" /></td>
               </tr>
               <tr class="column_row" align="center">
                  <td colspan="2"><h2>Column Variables</h2></td>
               </tr>
               <tr class="column_row"  valign="top"><th scope="row">Header Text</th>
                   <td><input type="text" id="tsmp_header" name="tsmp_header" value="<?php echo get_option('tsmp_header'); ?>" /></td>
               </tr>
               <tr class="column_row" ><td colspan="2"><font -2>note on html, each div rendered seperately<br>and column breaks will only fall on close of div</font>
               </td></tr>
               <tr class="column_row"  valign="top"><th scope="row">HTML before meetings<br>
               <button type=button onclick="toggleArea1('tsmp_intro_html');">Toggle  Editor</button></th>
                   <td><textarea rows="10" cols="70" id="tsmp_intro_html" name="tsmp_intro_html" ><?php echo get_option('tsmp_intro_html'); ?></textarea></td>
               </tr>
               <tr class="column_row"  valign="top"><th scope="row">HTML after meetings<br><br>
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
               <tr class="table_row"  class="table_row"  valign="top"><th scope="row">Include Type Index?</th>
                   <td>
                     
                     <input id="include_radio1" type="radio" name="tsmp_include_index" value="1" <?php echo ((get_option('tsmp_include_index') == 1) ? 'checked' : '')?>  > Yes<br>
                     <input id="include_radio2" type="radio" name="tsmp_include_index" value="0" <?php echo ((get_option('tsmp_include_index') == 0) ? 'checked' : '')?> > No</td>
               </tr>


           </table>

<?php

if(1==2){
  echo "tsmp_column_padding :" .  get_option('tsmp_column_padding') . "<br>";
  echo "tsmp_column_count :" .  get_option('tsmp_column_count') . "<br>";
  echo "tsmp_margin :" .  get_option('tsmp_margin') . "<br>";
  echo "tsmp_font_size :" .  get_option('tsmp_font_size') . "<br>";
  echo "tsmp_header :" .  get_option('tsmp_header') . "<br>";
  echo "tsmp_intro_html :" .  get_option('tsmp_intro_html') . "<br>";
  echo "tsmp_outtro_html :" .  get_option('tsmp_outtro_html') . "<br>";
}
?>


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
setEntryListeners(document.getElementById("tsmp_margin"));
setEntryListeners(document.getElementById("tsmp_header"));
setEntryListeners(document.getElementById("tsmp_outtro_html"));
setEntryListeners(document.getElementById("tsmp_column_count"));
setEntryListeners(document.getElementById("tsmp_column_padding"));
setEntryListeners(document.getElementById("tsmp_first_page_no"));
setEntryListeners(document.getElementById("include_radio1"));
setEntryListeners(document.getElementById("include_radio2"));
updateVarView()
</script>



  <?php

}
