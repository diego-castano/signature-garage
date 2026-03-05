<?php
if (!defined('ABSPATH')) exit;

class SGU_Logger {

    const LOG_DIR = 'sgu-logs';
    const MAX_LOG_SIZE = 2 * 1024 * 1024; // 2MB per log file
    const MAX_LOG_FILES = 10;

    private static $log_path = null;

    public static function init() {
        // REST API endpoint for reading logs remotely
        add_action('rest_api_init', [__CLASS__, 'register_endpoints']);

        // Admin tab
        add_action('admin_init', function () {
            SGU_Admin::register_tab('logs', 'Logs', SGU_PLUGIN_DIR . 'admin/views/tab-logs.php');
        });
    }

    public static function get_log_dir() {
        $upload_dir = wp_upload_dir();
        return trailingslashit($upload_dir['basedir']) . self::LOG_DIR;
    }

    public static function get_log_url() {
        $upload_dir = wp_upload_dir();
        return trailingslashit($upload_dir['baseurl']) . self::LOG_DIR;
    }

    private static function ensure_log_dir() {
        $dir = self::get_log_dir();
        if (!is_dir($dir)) {
            wp_mkdir_p($dir);
            // Protect logs from direct web access
            file_put_contents($dir . '/.htaccess', "Deny from all\n");
            file_put_contents($dir . '/index.php', "<?php // Silence is golden.\n");
        }
        return $dir;
    }

    private static function get_current_log_file() {
        if (self::$log_path) return self::$log_path;

        $dir = self::ensure_log_dir();
        $file = $dir . '/sgu-' . date('Y-m-d') . '.log';

        // Rotate if too large
        if (file_exists($file) && filesize($file) > self::MAX_LOG_SIZE) {
            $i = 1;
            while (file_exists($dir . '/sgu-' . date('Y-m-d') . '-' . $i . '.log')) $i++;
            $file = $dir . '/sgu-' . date('Y-m-d') . '-' . $i . '.log';
        }

        self::$log_path = $file;
        return $file;
    }

    public static function log($level, $module, $message, $context = []) {
        $file = self::get_current_log_file();
        $timestamp = current_time('Y-m-d H:i:s');
        $level = strtoupper($level);

        $entry = "[{$timestamp}] [{$level}] [{$module}] {$message}";
        if (!empty($context)) {
            $entry .= ' | ' . wp_json_encode($context, JSON_UNESCAPED_SLASHES);
        }
        $entry .= "\n";

        file_put_contents($file, $entry, FILE_APPEND | LOCK_EX);

        // Cleanup old logs periodically
        if (mt_rand(1, 100) === 1) {
            self::cleanup_old_logs();
        }
    }

    public static function info($module, $message, $context = []) {
        self::log('INFO', $module, $message, $context);
    }

    public static function error($module, $message, $context = []) {
        self::log('ERROR', $module, $message, $context);
    }

    public static function warning($module, $message, $context = []) {
        self::log('WARNING', $module, $message, $context);
    }

    public static function debug($module, $message, $context = []) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            self::log('DEBUG', $module, $message, $context);
        }
    }

    public static function get_logs($date = null, $lines = 200, $level = null, $module = null) {
        $dir = self::get_log_dir();
        if (!$date) $date = date('Y-m-d');

        $file = $dir . '/sgu-' . $date . '.log';
        if (!file_exists($file)) return [];

        $content = file_get_contents($file);
        $all_lines = array_filter(explode("\n", $content));

        // Filter by level/module if specified
        if ($level || $module) {
            $all_lines = array_filter($all_lines, function ($line) use ($level, $module) {
                if ($level && stripos($line, "[{$level}]") === false) return false;
                if ($module && stripos($line, "[{$module}]") === false) return false;
                return true;
            });
        }

        // Return last N lines
        return array_slice($all_lines, -$lines);
    }

    public static function get_available_dates() {
        $dir = self::get_log_dir();
        if (!is_dir($dir)) return [];

        $files = glob($dir . '/sgu-*.log');
        $dates = [];
        foreach ($files as $f) {
            if (preg_match('/sgu-(\d{4}-\d{2}-\d{2})/', basename($f), $m)) {
                $dates[] = $m[1];
            }
        }
        return array_unique($dates);
    }

    private static function cleanup_old_logs() {
        $dir = self::get_log_dir();
        if (!is_dir($dir)) return;

        $files = glob($dir . '/sgu-*.log');
        if (count($files) <= self::MAX_LOG_FILES) return;

        usort($files, function ($a, $b) { return filemtime($a) - filemtime($b); });
        $to_delete = array_slice($files, 0, count($files) - self::MAX_LOG_FILES);
        foreach ($to_delete as $f) @unlink($f);
    }

    // --- REST API Endpoints ---

    public static function register_endpoints() {
        register_rest_route('sgu/v1', '/logs', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'api_get_logs'],
            'permission_callback' => [__CLASS__, 'api_check_auth'],
            'args'                => [
                'date'   => ['type' => 'string', 'default' => ''],
                'lines'  => ['type' => 'integer', 'default' => 200],
                'level'  => ['type' => 'string', 'default' => ''],
                'module' => ['type' => 'string', 'default' => ''],
            ],
        ]);

        register_rest_route('sgu/v1', '/logs/dates', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'api_get_dates'],
            'permission_callback' => [__CLASS__, 'api_check_auth'],
        ]);

        register_rest_route('sgu/v1', '/health', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'api_health'],
            'permission_callback' => [__CLASS__, 'api_check_auth'],
        ]);
    }

    public static function api_check_auth($request) {
        $token = $request->get_header('X-SGU-Token');
        $stored = get_option('sgu_api_token', '');

        // Auto-generate token on first use
        if (empty($stored)) {
            $stored = wp_generate_password(40, false);
            update_option('sgu_api_token', $stored);
        }

        return !empty($token) && hash_equals($stored, $token);
    }

    public static function api_get_logs($request) {
        $date   = sanitize_text_field($request->get_param('date'));
        $lines  = absint($request->get_param('lines')) ?: 200;
        $level  = sanitize_text_field($request->get_param('level'));
        $module = sanitize_text_field($request->get_param('module'));

        $logs = self::get_logs($date ?: null, $lines, $level ?: null, $module ?: null);

        return new WP_REST_Response([
            'date'  => $date ?: date('Y-m-d'),
            'count' => count($logs),
            'lines' => $logs,
        ], 200);
    }

    public static function api_get_dates($request) {
        return new WP_REST_Response([
            'dates' => self::get_available_dates(),
        ], 200);
    }

    public static function api_health($request) {
        return new WP_REST_Response([
            'status'        => 'ok',
            'plugin_version'=> SGU_VERSION,
            'php_version'   => PHP_VERSION,
            'gd_available'  => SGU_Image_Optimizer::has_gd(),
            'webp_available'=> SGU_Image_Optimizer::has_webp_support(),
            'wp_version'    => get_bloginfo('version'),
            'memory_limit'  => ini_get('memory_limit'),
            'timestamp'     => current_time('c'),
        ], 200);
    }
}
