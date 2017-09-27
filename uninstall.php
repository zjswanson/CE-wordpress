<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

if (!current_user_can('activate_plugins')) {
    return;
}

delete_option("ce_insert_header");
delete_option("ce_insert_footer");
