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
       <form method="post" action="options.php">
           <?php settings_fields('tsmp-settings-group'); ?>
          <?php do_settings_sections( 'tsmp-settings-group' ); ?>
           <table class="form-table">

               <tr valign="top"><th scope="row">Header Text</th>
                   <td><input type="text" name="tsmp_header" value="<?php echo get_option('tsmp_header'); ?>" /></td>
               </tr>
               <tr valign="top"><th scope="row">Font Size</th>
                   <td><input type="text" name="tsmp_font_size" value="<?php echo get_option('tsmp_font_size'); ?>" /></td>
               </tr>
               <tr valign="top"><th scope="row">Margin</th>
                   <td><input type="text" name="tsmp_margin" value="<?php echo get_option('tsmp_margin'); ?>" /></td>
               </tr>
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

           </table>
           outtro_html : <?php echo get_option('tsmp_outtro_html'); ?><br>
       <p class="submit">
           <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
           </p>
       </form>
   </div>
  <?php

}
