<?php
if (!defined('ABSPATH')) exit;

class SGU_Admin {

    private static $tabs = [];
    private static $hook_suffix = '';

    public static function init() {
        self::register_tab('image-optimizer', 'Image Optimizer', SGU_PLUGIN_DIR . 'admin/views/tab-image-optimizer.php');
        self::register_tab('slider-manager', 'Slider Manager', SGU_PLUGIN_DIR . 'admin/views/tab-slider-manager.php');
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    public static function register_tab($slug, $label, $view_file) {
        self::$tabs[$slug] = [
            'label' => $label,
            'view'  => $view_file,
        ];
    }

    public static function add_menu() {
        self::$hook_suffix = add_menu_page(
            'Signature Garage Upgrades',
            'SG Upgrades',
            'manage_options',
            'sgu-dashboard',
            [__CLASS__, 'render_page'],
            'dashicons-admin-generic',
            80
        );
    }

    public static function enqueue_assets($hook) {
        if ($hook !== self::$hook_suffix) return;

        wp_enqueue_style(
            'sgu-admin',
            SGU_PLUGIN_URL . 'admin/css/sgu-admin.css',
            [],
            SGU_VERSION
        );

        wp_enqueue_script(
            'sgu-bulk-optimizer',
            SGU_PLUGIN_URL . 'admin/js/sgu-bulk-optimizer.js',
            [],
            SGU_VERSION,
            true
        );

        wp_localize_script('sgu-bulk-optimizer', 'sguOptimizer', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('sgu_optimizer_nonce'),
        ]);

        // Slider Manager assets
        wp_enqueue_style(
            'sgu-slider-manager',
            SGU_PLUGIN_URL . 'admin/css/sgu-slider-manager.css',
            [],
            SGU_VERSION
        );

        wp_enqueue_script(
            'sgu-slider-manager',
            SGU_PLUGIN_URL . 'admin/js/sgu-slider-manager.js',
            [],
            SGU_VERSION,
            true
        );

        wp_localize_script('sgu-slider-manager', 'sguSlider', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('sgu_slider_nonce'),
        ]);
    }

    public static function register_settings() {
        register_setting('sgu_image_optimizer', 'sgu_image_optimizer_settings', [
            'sanitize_callback' => [__CLASS__, 'sanitize_settings'],
        ]);
    }

    public static function sanitize_settings($input) {
        return [
            'max_width'       => absint($input['max_width'] ?? 1920),
            'jpeg_quality'    => max(1, min(100, absint($input['jpeg_quality'] ?? 82))),
            'png_compression' => max(0, min(9, absint($input['png_compression'] ?? 6))),
            'webp_quality'    => max(1, min(100, absint($input['webp_quality'] ?? 82))),
            'auto_optimize'   => !empty($input['auto_optimize']),
        ];
    }

    public static function render_page() {
        include SGU_PLUGIN_DIR . 'admin/views/admin-page.php';
    }

    public static function get_tabs() {
        return self::$tabs;
    }

    public static function get_current_tab() {
        $current = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : '';
        if (empty($current) || !isset(self::$tabs[$current])) {
            $keys = array_keys(self::$tabs);
            $current = $keys[0] ?? '';
        }
        return $current;
    }
}
