<?php

//import CSV file and handle settings
function tsmp_gen_page() {





  ?>
  <h3>Generate PDF</h3>
 <form method="get" class="form-horizontal" action="' .
 admin_url('admin-ajax.php') . '"> <input name="action"
 value="step_pdf" type="hidden">
 <fieldset>
 <!-- Form Name --> <legend>Form Name</legend>
 <!-- Text input-->
 <div class="form-group"> <label class="col-md-4 control-label"
 for="header_text">Header title</label>
 <div class="col-md-6"> <input id="header_text"
 name="header_text" value="<?php echo get_option('tsmp_header'); ?>"
 class="form-control input-md" type="text"> </div>
 </div>
 <!-- Text input-->
 <div class="form-group"> <label class="col-md-4 control-label"
 for="font_size">Font Size</label> (decimals allowed)
 <div class="col-md-2"> <input id="font_size" name="font_size"
 value="<?php echo get_option('tsmp_font_size'); ?>" class="form-control input-md" type="text"> </div>
 <!-- Text input-->
 <div class="form-group"> <label class="col-md-4
 control-label" for="margin">margin</label>
 <div class="col-md-2"> <input id="margin" name="margin"
 value="<?php echo get_option('tsmp_margin'); ?>" class="form-control input-md" type="text"> </div>
 <!-- Text input-->
 <div class="form-group"> html before meetings
 <div class="col-md-2"> <textarea rows="10" cols="70" name="intro_html" class="form-control input-md"><?php echo get_option('tsmp_intro_html'); ?></textarea>
 </div>
 <div class="form-group"> html after meetings
 <div class="col-md-2"> <textarea rows="10" cols="70" name="outtro_html" class="form-control input-md"><?php echo get_option('tsmp_outtro_html'); ?></textarea>
 </div>
 </div>
 <label class="col-md-4
 control-label" for="column_count">Column Count</label>
 <div class="col-md-2"> <input id="column_count" name="column_count"
 value="<?php echo get_option('tsmp_column_count'); ?>" class="form-control input-md" type="text"> </div>
 <!-- Text input-->
 <div class="form-group"> <label class="col-md-4
 control-label" for="column_padding">Column Padding</label>
 <div class="col-md-4"> <input id="column_padding"
 name="column_padding" value="<?php echo get_option('tsmp_column_padding'); ?>" class="form-control
 input-md" type="text"> <span class="help-block">padding


 between columns</span> </div>
 </div>
 <!-- Button -->
 <div class="form-group"> <label class="col-md-4
 control-label" for="submit"></label>
 <div class="col-md-4"> <button id="submit"
 name="submit" class="btn btn-primary">Generate PDF</button>
 </div>
 </div>
 </div>
 </div>
 </div>
 </fieldset>
 </form>
  <?php

}
