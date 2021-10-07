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
                                      'default' => 7,
                                      )
                      );
    register_setting( 'tsmp-settings-group', 'tsmp_header_font_size', array(
                                      'type' => 'number',
                                      'default' => 9,
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
      register_setting( 'tsmp-settings-group', 'tsmp_width', array(
                                        'type' => 'integer',
                                        'default' => 356,
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_height', array(
                                        'type' => 'integer',
                                        'default' => 216,
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_layout', array(
                                        'type' => 'string',
                                        'default' => 'columns1',
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_first_page_no', array(
                                        'type' => 'integer',
                                        'default' => 1,
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_include_index', array(
                                        'type' => 'integer',
                                        'default' => 1,
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_auto_font', array(
                                        'type' => 'integer',
                                        'default' => 0,
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_set_custom_meeting_html', array(
                                        'type' => 'integer',
                                        'default' => 0,
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_custom_meeting_html', array(
                                        'type' => 'string',
                                        'default' => '',
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_desired_page_count', array(
                                        'type' => 'integer',
                                        'default' => 2,
                                        )
                        );
      register_setting( 'tsmp-settings-group', 'tsmp_column2_indent', array(
                                        'type' => 'integer',
                                        'default' => 15,
                                        )
                        );
        register_setting( 'tsmp-settings-group', 'tsmp_set_save_file', array(
                                          'type' => 'integer',
                                          'default' => 0,
                                          )
                          );
        register_setting( 'tsmp-settings-group', 'tsmp_save_file_name', array(
                                          'type' => 'string',
                                          'default' => 'wp-content/uploads/meeting_list.pdf',
                                          )
                          );
        register_setting( 'tsmp-settings-group', 'tsmp_table_region_new_page', array(
                                          'type' => 'integer',
                                          'default' => 1,
                                          )
                          );

        register_setting( 'tsmp-settings-group', 'tsmp_filtering_types_how', array(
                                          'type' => 'string',
                                          'default' => 'n',
                                          )
                                          //w=white list, b=black lisst, n = none
                          );

        register_setting( 'tsmp-settings-group', 'tsmp_filtering_types_what', array(
                                          'type' => 'array',
                                          'default' => '',
                                          )
                                          //comma seperated list of types being filterd
                          );

          register_setting( 'tsmp-settings-group', 'tsmp_column_html', array(
                                            'type' => 'array',
                                            'default' => '',
                                            )

                            );
          register_setting( 'tsmp-settings-group', 'attendance_option_filtering', array(
                                            'type' => 'array',
                                            'default' => 'all',
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
