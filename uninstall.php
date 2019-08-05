<?php

//check
if (!defined('WP_UNINSTALL_PLUGIN')) exit();


//remove settings
unregister_setting( 'tsmp-settings-group', 'tsmp_header');
unregister_setting( 'tsmp-settings-group', 'tsmp_margin');
unregister_setting( 'tsmp-settings-group', 'tsmp_intro_html');
unregister_setting( 'tsmp-settings-group', 'tsmp_font_size');
unregister_setting( 'tsmp-settings-group', 'tsmp_header_font_size');
unregister_setting( 'tsmp-settings-group', 'tsmp_column_count');
unregister_setting( 'tsmp-settings-group', 'tsmp_column_padding');
unregister_setting( 'tsmp-settings-group', 'tsmp_outtro_html');
unregister_setting( 'tsmp-settings-group', 'tsmp_layout');
unregister_setting( 'tsmp-settings-group', 'tsmp_first_page_no');
unregister_setting( 'tsmp-settings-group', 'tsmp_include_index');
unregister_setting( 'tsmp-settings-group', 'tsmp_auto_font');
unregister_setting( 'tsmp-settings-group', 'tsmp_desired_page_count');
unregister_setting( 'tsmp-settings-group', 'tsmp_set_custom_meeting_html');
unregister_setting( 'tsmp-settings-group', 'tsmp_custom_meeting_html');
