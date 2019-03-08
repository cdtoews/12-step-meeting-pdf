<?php

add_action('admin_menu', 'tsmp_admin_menu');


add_action('admin_init', 'tsmp_options_init' );

function tsmp_options_init(){
  //register settings and set default values
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

    //add menu page under '12 Step Meeting List'
    add_submenu_page('edit.php?post_type=tsml_meeting', 'PDF Generator',  'PDF Generator', 'manage_options', 'generate_pdf', 'tsmp_gen_page');

  }
