<?php
/**
 * Plugin Name: Contact Custom Feed
 * Plugin URI: https://example.com
 * Description: A simple OOP WordPress plugin with custom feed functionality
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: contact-custom-feed
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CCF_VERSION', '1.0.0');
define('CCF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CCF_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once CCF_PLUGIN_DIR . 'includes/class-contact-custom-feed.php';
require_once CCF_PLUGIN_DIR . 'includes/class-ccf-admin.php';
require_once CCF_PLUGIN_DIR . 'includes/class-ccf-helper.php';

// Initialize the plugin
function ccf_init() {
    $plugin = new Contact_Custom_Feed();
    $plugin->run();
}
add_action('plugins_loaded', 'ccf_init');

// Activation hook
register_activation_hook(__FILE__, array('Contact_Custom_Feed', 'activate'));

// Deactivation hook
register_deactivation_hook(__FILE__, array('Contact_Custom_Feed', 'deactivate'));
