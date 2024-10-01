<?php
/*
Plugin Name: Change Administration Email
Plugin URI: https://plugincritic.com/plugin/change-administration-email/
Description: Change the Site's Administration Email Address on the General Settings page without the confirmation email. <strong>*** Delete this plugin after using it. ***</strong>
Author: Plugin Critic
Version: 1.0.3
Requires at least: 6.4
Requires PHP: 8.0
Author URI: https://plugincritic.com
License: GPLv2 or later
Text Domain: change-administration-email
*/

namespace Change_Administration_Email;

if (!defined('ABSPATH')) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

add_action('admin_notices', __NAMESPACE__ . '\\display_deactivate_notice');
function display_deactivate_notice() {
    include_once __DIR__ . DIRECTORY_SEPARATOR . 'plugin.php';
    $plugin = new Plugin( __FILE__ );

    include_once __DIR__ . DIRECTORY_SEPARATOR . 'action' . DIRECTORY_SEPARATOR . 'deactivate-notice.php';
    $deactivate_notice = new Action\Deactivate_Notice( $plugin );
    $deactivate_notice->output_notice();
}

add_action('admin_footer', __NAMESPACE__ . '\\alter_settings_page_to_provide_confirm_link');
function alter_settings_page_to_provide_confirm_link() {
    include_once __DIR__ . DIRECTORY_SEPARATOR . 'action' . DIRECTORY_SEPARATOR . 'alter-settings-page.php';
    $alter_settings_page = new Action\Alter_Settings_Page();
    $alter_settings_page->output_confirm_link_script();
}
