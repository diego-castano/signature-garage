<?php
if (!defined('ABSPATH')) exit;

class SGU_Analytics {

    public static function init() {
        add_action('template_redirect', [__CLASS__, 'track_pageview']);
        add_action('sgu_purge_pageviews', [__CLASS__, 'purge_old_data']);
    }

    public static function activate() {
        self::create_table();

        if (!wp_next_scheduled('sgu_purge_pageviews')) {
            wp_schedule_event(time(), 'weekly', 'sgu_purge_pageviews');
        }
    }

    public static function deactivate() {
        wp_clear_scheduled_hook('sgu_purge_pageviews');
    }

    public static function create_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'sgu_pageviews';
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            url VARCHAR(500) NOT NULL,
            referrer VARCHAR(500) DEFAULT '',
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY idx_created (created_at),
            KEY idx_url (url(191))
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public static function track_pageview() {
        // Skip admin pages, logged-in users, cron, AJAX, REST
        if (is_admin() || is_user_logged_in() || wp_doing_cron() || wp_doing_ajax()) return;
        if (defined('REST_REQUEST') && REST_REQUEST) return;

        // Skip bots
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (empty($ua) || preg_match('/bot|crawl|spider|slurp|facebook|twitter|whatsapp|telegram|preview/i', $ua)) return;

        // Skip non-HTML requests (images, CSS, JS, feeds)
        if (is_feed() || is_robots() || is_trackback()) return;

        // Skip non-page URLs (assets, icons, well-known, etc.)
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        if (preg_match('/\.(ico|png|jpg|jpeg|gif|svg|webp|css|js|woff2?|ttf|eot|map|xml|txt|json)(\?|$)/i', $uri)) return;
        if (preg_match('#^/\.well-known/#i', $uri)) return;
        if (preg_match('#/(favicon|apple-touch-icon|android-chrome|mstile|browserconfig|site\.webmanifest)#i', $uri)) return;
        if (preg_match('#^/wp-(content|includes|admin)/(themes|plugins|uploads)/.+\..+$#i', $uri)) return;

        global $wpdb;
        $table = $wpdb->prefix . 'sgu_pageviews';

        // Check table exists (avoids errors before activation)
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) return;

        // Store relative path only (normalize)
        $path = wp_parse_url(home_url($uri), PHP_URL_PATH);
        if (empty($path)) $path = '/';
        $url = esc_url_raw(home_url($path));
        $referrer = esc_url_raw($_SERVER['HTTP_REFERER'] ?? '');

        $wpdb->insert($table, [
            'url'        => mb_substr($url, 0, 500),
            'referrer'   => mb_substr($referrer, 0, 500),
            'created_at' => current_time('mysql'),
        ], ['%s', '%s', '%s']);
    }

    public static function purge_old_data() {
        global $wpdb;
        $table = $wpdb->prefix . 'sgu_pageviews';
        $wpdb->query("DELETE FROM {$table} WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)");
    }
}
