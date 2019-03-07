<?php

add_action('admin_menu', 'tsmp_admin_menu');

function tsmp_admin_menu() {

  // check user capabilities
  if (!current_user_can('manage_options')) {
      return;
  }

add_submenu_page('edit.php?post_type=tsml_meeting', __('PDF Generator', '12-step-meeting-list'),  __('PDF Generator', '12-step-meeting-list'), 'manage_options', 'import', 'tsmp_gen_page');

//add_menu_page('12 Step PDF Generator', '12 Step PDF', 'manage_options', '12-step-pdf-generator', 'tsmp_gen_page');

}
