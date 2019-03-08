<?php

add_action('admin_menu', 'tsmp_admin_menu');


add_action('admin_init', 'tsmp_options_init' );

function tsmp_options_init(){
  //default values don's seem to work. wordpress documentation on using settings is splintered
      register_setting( 'tsmp-settings-group', 'tsmp_header', array(
                                        'type' => 'string',
                                        'default' => 'Some Group Meeting List',
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_margin' , array(
                                        'type' => 'integer',
                                        'default' => 10,
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_intro_html', array(
                                        'type' => 'string',
                                        'default' => '<h1>Our Meeting List</h1>',
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_font_size', array(
                                        'type' => 'number',
                                        'default' => 7.6,
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_column_count', array(
                                        'type' => 'integer',
                                        'default' => 4,
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_column_padding', array(
                                        'type' => 'integer',
                                        'default' => 5,
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_outtro_html', array(
                                        'type' => 'string',
                                        'default' => '<h1>Thanks for Looking</h1>',
                                        )
                        );
    }



function tsmp_admin_menu() {

  // check user capabilities
  if (!current_user_can('manage_options')) {
      return;
  }

/*
  //set the options and defaults if they don't exist
  if(!get_option('tsmp_header')){
      update_option('tsmp_header', 'AA Meeting of Somewhere');
  }

  if(!get_option('tsmp_margin')){
      update_option('tsmp_margin', '10');
  }

  if(!get_option('tsmp_intro_html')){
      update_option('tsmp_intro_html', '<h1>Here are the Meetings</h1>');
  }

  if(!get_option('tsmp_font_size')){
      update_option('tsmp_font_size', '7.6');
  }

  if(!get_option('tsmp_column_count')){
      update_option('tsmp_column_count', '4');
  }

  if(!get_option('tsmp_column_padding')){
      update_option('tsmp_column_padding', '4');
  }

  if(!get_option('tsmp_outtro_html')){
      update_option('tsmp_outtro_html', '<h2>Thanks for looking at our meetings</h2><br><img src="https://cdn.psychologytoday.com/sites/default/files/blogs/1023/2010/12/52630-43316.jpg">');
  }

*/
//let's register tsml_get_meetings




  /*
  tsmp_header
  tsmp_margin
  tsmp_intro_html
  tsmp_font_size
  tsmp_column_count
  tsmp_column_padding
  tsmp_outtro_html
  */

add_submenu_page('edit.php?post_type=tsml_meeting', 'PDF Generator',  'PDF Generator', 'manage_options', 'generate_pdf', 'tsmp_gen_page');





}
