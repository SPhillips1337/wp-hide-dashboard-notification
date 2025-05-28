<?php
/**
 * Plugin Name: WP Hide Dashboard Notifications
 * Plugin URI: 
 * Description: Hides all WordPress admin dashboard notifications and cleans up the admin interface.
 * Version: 1.0.0
 * Author: 
 * Author URI: 
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-hide-dashboard-notification
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Disable admin notices
 * Removes all notifications from plugins etc.
 */
function wphdn_disable_admin_notices() {
    global $wp_filter;
    global $pagenow;
    if (is_admin() && $pagenow != 'index.php') {      
        if (is_user_admin()) {
            if (isset($wp_filter['user_admin_notices'])) {
                unset($wp_filter['user_admin_notices']);
            }
        } elseif (isset($wp_filter['admin_notices'])) {
            unset($wp_filter['admin_notices']);
        }
        if (isset($wp_filter['all_admin_notices'])) {
            unset($wp_filter['all_admin_notices']);
        }
    }
}
add_action('admin_print_scripts', 'wphdn_disable_admin_notices');

/**
 * Remove dashboard widgets
 * Cleans up the admin dashboard by removing unnecessary widgets
 */
function wphdn_remove_dashboard_widgets() {
    remove_meta_box('dashboard_browser_nag', 'dashboard', 'normal'); // Removes the "Browser Nag" widget
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal'); // Removes the "Incoming Links" widget
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal'); // Removes the "Plugins" widget
    remove_meta_box('dashboard_primary', 'dashboard', 'side'); // Removes the "WordPress News" widget
    remove_meta_box('dashboard_secondary', 'dashboard', 'side'); // Removes the "Other WordPress News" widget
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); // Removes the "Quick Press" widget
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side'); // Removes the "Recent Drafts" widget
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // Removes the "Recent Comments" widget
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); // Removes the "Right Now" widget
    remove_meta_box('dashboard_activity', 'dashboard', 'normal'); // Removes the "Activity" widget (WordPress 3.8 and later)
    remove_meta_box('elementor_dashboard_widget', 'dashboard', 'normal'); // Removes the Elementor widget
}
add_action('wp_dashboard_setup', 'wphdn_remove_dashboard_widgets');

/**
 * Enqueue custom admin styles
 * Adds CSS to hide notifications and clean up the admin interface
 */
function wphdn_enqueue_admin_styles() {
    global $pagenow;
    
    // Register and enqueue the custom admin styles
    wp_register_style(
        'wphdn-admin-styles',
        plugin_dir_url(__FILE__) . 'assets/css/admin-styles.css',
        array(),
        '1.0.0'
    );
    wp_enqueue_style('wphdn-admin-styles');
}
add_action('admin_enqueue_scripts', 'wphdn_enqueue_admin_styles');

/**
 * Create plugin directory structure on activation
 */
function wphdn_activate() {
    // Create assets directory if it doesn't exist
    $assets_dir = plugin_dir_path(__FILE__) . 'assets';
    if (!file_exists($assets_dir)) {
        mkdir($assets_dir, 0755, true);
    }
    
    // Create css directory if it doesn't exist
    $css_dir = $assets_dir . '/css';
    if (!file_exists($css_dir)) {
        mkdir($css_dir, 0755, true);
    }
    
    // Create the CSS file
    $css_file = $css_dir . '/admin-styles.css';
    $css_content = "/* styles to remove dashboard plugin spam */
/* Only hide .wrap on the dashboard page */
body.index-php .wrap {
    display: none !important;
}
body.index-php #dashboard-widgets-wrap {
    display: none !important;
}
body.index-php .notice {
    display: none !important;
}";
    
    file_put_contents($css_file, $css_content);
}
register_activation_hook(__FILE__, 'wphdn_activate');
