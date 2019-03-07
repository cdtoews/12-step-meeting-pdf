<?php

//check
if (!defined('WP_UNINSTALL_PLUGIN')) exit();

//remote tsmp sheets
$groups = get_posts('post_type=tsmp_sheet&numberposts=-1');
foreach ($groups as $group) wp_delete_post($group->ID, true);

//flush rewrite once more for good measure
flush_rewrite_rules();
