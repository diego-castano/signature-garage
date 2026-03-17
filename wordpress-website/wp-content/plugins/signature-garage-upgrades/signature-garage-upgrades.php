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
require_once SGU_PLUGIN_DIR . 'includes/class-sgu-dashboard.php';
require_once SGU_PLUGIN_DIR . 'includes/class-sgu-analytics.php';
require_once SGU_PLUGIN_DIR . 'includes/class-sgu-no-residentes.php';
require_once SGU_PLUGIN_DIR . 'includes/class-sgu-frontend-speed.php';
require_once SGU_PLUGIN_DIR . 'includes/class-sgu-slider.php';
require_once SGU_PLUGIN_DIR . 'includes/class-sgu-smtp.php';

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
    SGU_Analytics::init();

    // Auto-create analytics table on upgrade (without needing reactivation)
    if (get_option('sgu_db_version') !== SGU_VERSION) {
        SGU_Analytics::create_table();
        update_option('sgu_db_version', SGU_VERSION);
    }

    SGU_Logger::debug('core', 'Plugin initialized', ['version' => SGU_VERSION]);
});

// Admin UI + AJAX handlers (must hook before admin_menu fires)
SGU_Admin::init();
SGU_Image_Bulk::init();
SGU_Admin_Organizer::init();
SGU_Dashboard::init();
SGU_No_Residentes::init();
SGU_Frontend_Speed::init();
SGU_Slider::init();
SGU_SMTP::init();

// Activation
register_activation_hook(__FILE__, function () {
    SGU_Analytics::activate();

    $rules = [
        '<IfModule mod_rewrite.c>',
        '  RewriteEngine On',
        '  RewriteCond %{HTTP_ACCEPT} image/webp',
        '  RewriteCond %{REQUEST_FILENAME} \.(jpe?g|png)$',
        '  RewriteCond %{REQUEST_FILENAME}.webp -f',
        '  RewriteRule (.+)\.(jpe?g|png)$ $1.$2.webp [T=image/webp,L]',
        '</IfModule>',
        '',
        '<IfModule mod_headers.c>',
        '  <FilesMatch "\.(jpe?g|png|webp)$">',
        '    Header append Vary Accept',
        '  </FilesMatch>',
        '</IfModule>',
        '',
        '# Browser caching for static assets',
        '<IfModule mod_expires.c>',
        '  ExpiresActive On',
        '  ExpiresByType image/jpeg "access plus 1 year"',
        '  ExpiresByType image/png "access plus 1 year"',
        '  ExpiresByType image/webp "access plus 1 year"',
        '  ExpiresByType image/svg+xml "access plus 1 year"',
        '  ExpiresByType text/css "access plus 1 month"',
        '  ExpiresByType application/javascript "access plus 1 month"',
        '  ExpiresByType font/woff2 "access plus 1 year"',
        '  ExpiresByType font/woff "access plus 1 year"',
        '</IfModule>',
        '',
        '# Gzip compression',
        '<IfModule mod_deflate.c>',
        '  AddOutputFilterByType DEFLATE text/html text/css application/javascript application/json image/svg+xml font/woff font/woff2',
        '</IfModule>',
    ];

    $htaccess = ABSPATH . '.htaccess';
    if (is_writable($htaccess) || is_writable(dirname($htaccess))) {
        insert_with_markers($htaccess, 'SGU WebP Delivery', $rules);
    }
});

// Deactivation
register_deactivation_hook(__FILE__, function () {
    SGU_Analytics::deactivate();

    $htaccess = ABSPATH . '.htaccess';
    if (is_writable($htaccess)) {
        insert_with_markers($htaccess, 'SGU WebP Delivery', []);
    }
});
