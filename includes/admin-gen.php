<?php

function tsmp_gen_page() {
// settings page:
  ?>
  <script>
  function setValues(width, height) {
    document.getElementById("tsmp_width").value = width;
    document.getElementById("tsmp_height").value = height;
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
                   <select id="tsmp_layout" name="tsmp_layout" value="<?php echo get_option('tsmp_layout'); ?>" />
                      <?php 
                            $tsmp_layout = get_option('tsmp_layout');
                            $layouts = array("columns1", "table1");
                            foreach ($layouts as $layout) {
                              echo ' <option value="' . $layout   .  '" ' . ($tsmp_layout == $layout ? 'selected' : '') .  '>' . $layout . '</option>';
                            }
                      
                      
                       ?>
                 
                 
                 
                   </select>
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
                   <td><input type="text" name="tsmp_font_size" value="<?php echo get_option('tsmp_font_size'); ?>" /></td>
               </tr>
               <tr valign="top"><th scope="row">Margin</th>
                   <td><input type="text" name="tsmp_margin" value="<?php echo get_option('tsmp_margin'); ?>" /></td>
               </tr>
               <tr align="center">
                  <td colspan="2"><h2>Column Variables</h2></td>
               </tr>
               <tr valign="top"><th scope="row">Header Text</th>
                   <td><input type="text" name="tsmp_header" value="<?php echo get_option('tsmp_header'); ?>" /></td>
               </tr>
               <tr><td colspan="2"><font -2>note on html, each div rendered seperately<br>and column breaks will only fall on close of div</font>
               </td></tr>
               <tr valign="top"><th scope="row">HTML before meetings</th>
                   <td><textarea rows="10" cols="70" name="tsmp_intro_html" ><?php echo get_option('tsmp_intro_html'); ?></textarea></td>
               </tr>
               <tr valign="top"><th scope="row">HTML after meetings</th>
                   <td><textarea rows="10" cols="70" name="tsmp_outtro_html" ><?php echo get_option('tsmp_outtro_html'); ?></textarea></td>
               </tr>
               <tr valign="top"><th scope="row">Column Count</th>
                   <td><input type="text" name="tsmp_column_count" value="<?php echo get_option('tsmp_column_count'); ?>" /></td>
               </tr>
               <tr valign="top"><th scope="row">Column Padding</th>
                   <td><input type="text" name="tsmp_column_padding" value="<?php echo get_option('tsmp_column_padding'); ?>" /></td>
               </tr>
               <tr align="center">
                  <td colspan="2"><h2>Table Variables</h2></td>
               </tr>
               <tr valign="top"><th scope="row">Starting Page Number</th>
                   <td><input type="text" name="tsmp_first_page_no" value="<?php echo get_option('tsmp_first_page_no'); ?>" /></td>
               </tr>
               <tr valign="top"><th scope="row">Include Type Index?</th>
                   <td>
                     
                     <input type="radio" name="tsmp_include_index" value="1" <?php echo ((get_option(tsmp_include_index) == 1) ? 'checked' : '')?>  > Yes<br>
                     <input type="radio" name="tsmp_include_index" value="0" <?php echo ((get_option(tsmp_include_index) == 0) ? 'checked' : '')?> > No</td>
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
name="submit" class="button-primary" >Generate PDF</button>
</form>
   </div>
 </div>
</div>
  <?php

}
