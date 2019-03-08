<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
//import CSV file and handle settings
function tsmp_gen_page() {

/*
<form method="get" class="form-horizontal" action="' . admin_url('admin-ajax.php') . '">

 <input name="action" value="step_pdf" type="hidden">
 */

// <form method="post" action="options.php">

  ?>
  <div class="wrap">
       <h2>PDF Generation Settings</h2>
       [sample values]
       <form method="post" action="options.php">
           <?php settings_fields('tsmp-settings-group'); ?>
          <?php do_settings_sections( 'tsmp-settings-group' ); ?>
           <table class="form-table">

               <tr valign="top"><th scope="row">Header Text</th>
                   <td><input type="text" name="tsmp_header" value="<?php echo get_option('tsmp_header'); ?>" /></td>
               </tr>
               <tr valign="top"><th scope="row">Font Size[7.6]</th>
                   <td><input type="text" name="tsmp_font_size" value="<?php echo get_option('tsmp_font_size'); ?>" /></td>
               </tr>
               <tr valign="top"><th scope="row">Margin[10]</th>
                   <td><input type="text" name="tsmp_margin" value="<?php echo get_option('tsmp_margin'); ?>" /></td>
               </tr>
               <tr valign="top"><th scope="row">HTML before meetings</th>
                   <td><textarea rows="10" cols="70" name="tsmp_intro_html" ><?php echo get_option('tsmp_intro_html'); ?></textarea></td>
               </tr>
               <tr valign="top"><th scope="row">HTML after meetings</th>
                   <td><textarea rows="10" cols="70" name="tsmp_outtro_html" ><?php echo get_option('tsmp_outtro_html'); ?></textarea></td>
               </tr>
               <tr valign="top"><th scope="row">Column Count[4]</th>
                   <td><input type="text" name="tsmp_column_count" value="<?php echo get_option('tsmp_column_count'); ?>" /></td>
               </tr>
               <tr valign="top"><th scope="row">Column Padding[5]</th>
                   <td><input type="text" name="tsmp_column_padding" value="<?php echo get_option('tsmp_column_padding'); ?>" /></td>
               </tr>

           </table>

<?php
//for debugging
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
           <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
           </p>
       </form>
   </div>
   After saving values above<br>
  <form method="get" class="form-horizontal" action="admin-ajax.php">
     <input name="action" value="step_pdf" type="hidden">
<div class="form-group"> <label class="col-md-4 control-label" for="submit"></label>
<div class="col-md-4"> <button id="submit"
name="submit" class="btn btn-primary">Generate PDF</button>
</form>
  <?php

}
