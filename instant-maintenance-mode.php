<?php
/**
 * Plugin Name: Instant Maintenance Mode
 * Description: Instantly enable / disable maintenance mode with one click.
 * Version: 1.0
 * Author: Mizbahuddin Qasim
 * Author URI: https://github.com/qasimmizbah/
 * License: GPL2
 * Text Domain: instant-maintenance-mode
 */

defined('ABSPATH') or die('No script kiddies please!');

// Define plugin constants
define('IMMOD_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('IMMOD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('IMMOD_PLUGIN_VERSION', '1.2');

// Include files
require_once IMMOD_PLUGIN_PATH . 'includes/class-instant-maintenance-mode.php';
require_once IMMOD_PLUGIN_PATH . 'includes/maintenance-page.php';

// Initialize the plugin
function immod_instant_maintenance_mode_init() {
    new Immod_Instant_Maintenance_Mode();
}
add_action('plugins_loaded', 'immod_instant_maintenance_mode_init');

// Register hooks
register_activation_hook(__FILE__, array('Immod_Instant_Maintenance_Mode', 'activate'));
register_deactivation_hook(__FILE__, array('Immod_Instant_Maintenance_Mode', 'deactivate'));