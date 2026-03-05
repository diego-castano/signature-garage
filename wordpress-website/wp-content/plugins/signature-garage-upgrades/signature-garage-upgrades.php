<?php
/**
 * Plugin Name: Signature Garage Upgrades
 * Plugin URI: https://signature-garage.com
 * Description: Custom upgrades and enhancements for Signature Garage website.
 * Version: 1.0.0
 * Author: Signature Garage Dev Team
 * Author URI: https://signature-garage.com
 * License: GPL-2.0+
 * Text Domain: signature-garage-upgrades
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SGU_VERSION', '1.0.0');
define('SGU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SGU_PLUGIN_URL', plugin_dir_url(__FILE__));

// Core modules
require_once SGU_PLUGIN_DIR . 'includes/class-sgu-logger.php';
require_once SGU_PLUGIN_DIR . 'includes/class-sgu-image-optimizer.php';
require_once SGU_PLUGIN_DIR . 'includes/class-sgu-admin.php';
require_once SGU_PLUGIN_DIR . 'includes/class-sgu-image-hooks.php';
require_once SGU_PLUGIN_DIR . 'includes/class-sgu-image-bulk.php';
require_once SGU_PLUGIN_DIR . 'includes/class-sgu-admin-organizer.php';

// GD check notice
add_action('admin_notices', function () {
    if (!SGU_Image_Optimizer::has_gd()) {
        echo '<div class="notice notice-error"><p><strong>Signature Garage Upgrades:</strong> PHP GD extension is required for image optimization. Please contact your hosting provider.</p></div>';
    }
});

// Initialize modules
add_action('init', function () {
    SGU_Logger::init();
    SGU_Image_Hooks::init();
    SGU_Logger::debug('core', 'Plugin initialized', ['version' => SGU_VERSION]);
});

// Admin UI + AJAX handlers (must hook before admin_menu fires)
SGU_Admin::init();
SGU_Image_Bulk::init();
SGU_Admin_Organizer::init();

// Activation: add WebP rewrite rules to .htaccess
register_activation_hook(__FILE__, function () {
    $rules = [
        '<IfModule mod_rewrite.c>',
        '  RewriteEngine On',
        '  RewriteCond %{HTTP_ACCEPT} image/webp',
        '  RewriteCond %{REQUEST_URI} \.(jpe?g|png)$',
        '  RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI}.webp -f',
        '  RewriteRule ^(.+)\.(jpe?g|png)$ $1.webp [T=image/webp,L]',
        '</IfModule>',
        '',
        '<IfModule mod_headers.c>',
        '  <FilesMatch "\.(jpe?g|png|webp)$">',
        '    Header append Vary Accept',
        '  </FilesMatch>',
        '</IfModule>',
    ];

    $htaccess = ABSPATH . '.htaccess';
    if (is_writable($htaccess) || is_writable(dirname($htaccess))) {
        insert_with_markers($htaccess, 'SGU WebP Delivery', $rules);
    }
});

// Deactivation: remove WebP rewrite rules
register_deactivation_hook(__FILE__, function () {
    $htaccess = ABSPATH . '.htaccess';
    if (is_writable($htaccess)) {
        insert_with_markers($htaccess, 'SGU WebP Delivery', []);
    }
});
