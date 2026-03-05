<?php
if (!defined('ABSPATH')) exit;

class SGU_Image_Bulk {

    public static function init() {
        add_action('wp_ajax_sgu_scan_unoptimized', [__CLASS__, 'scan_unoptimized']);
        add_action('wp_ajax_sgu_optimize_single', [__CLASS__, 'optimize_single']);
        add_action('wp_ajax_sgu_get_stats', [__CLASS__, 'get_stats']);
        add_action('wp_ajax_sgu_save_settings', [__CLASS__, 'save_settings']);
    }

    public static function scan_unoptimized() {
        check_ajax_referer('sgu_optimizer_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

        $unoptimized = get_posts([
            'post_type'      => 'attachment',
            'post_mime_type' => ['image/jpeg', 'image/png', 'image/gif'],
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [
                [
                    'key'     => '_sgu_optimized',
                    'compare' => 'NOT EXISTS',
                ],
            ],
        ]);

        wp_send_json_success([
            'total' => count($unoptimized),
            'ids'   => $unoptimized,
        ]);
    }

    public static function optimize_single() {
        check_ajax_referer('sgu_optimizer_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

        $id = absint($_POST['attachment_id'] ?? 0);
        if (!$id) wp_send_json_error('Invalid ID');

        $file = get_attached_file($id);
        if (!$file || !file_exists($file)) {
            wp_send_json_error('File not found');
        }

        $filename = basename($file);
        $original_size = filesize($file);

        // Optimize main image
        SGU_Logger::info('bulk-optimizer', 'Processing image', ['id' => $id, 'file' => $filename, 'size' => size_format($original_size)]);
        $stats = SGU_Image_Optimizer::optimize_image($file, $id);
        if (!$stats) {
            SGU_Logger::error('bulk-optimizer', 'Optimization failed', ['id' => $id, 'file' => $filename]);
            wp_send_json_error('Optimization failed for ' . $filename);
        }

        // Optimize thumbnails
        $thumb_savings = SGU_Image_Optimizer::optimize_thumbnails($id);

        // Create WebP for main file
        SGU_Image_Optimizer::create_webp($file);

        // Mark as optimized
        update_post_meta($id, '_sgu_optimized', 1);

        // Update global stats
        $global = get_option('sgu_optimizer_global_stats', [
            'total_optimized'     => 0,
            'total_savings_bytes' => 0,
            'total_webp_created'  => 0,
        ]);
        $global['total_optimized']     = ($global['total_optimized'] ?? 0) + 1;
        $global['total_savings_bytes'] = ($global['total_savings_bytes'] ?? 0) + $stats['savings_bytes'] + $thumb_savings;
        if ($stats['webp_size'] > 0) {
            $global['total_webp_created'] = ($global['total_webp_created'] ?? 0) + 1;
        }
        $global['last_bulk_run'] = current_time('mysql');
        update_option('sgu_optimizer_global_stats', $global);

        SGU_Logger::info('bulk-optimizer', 'Image optimized', [
            'id' => $id, 'file' => $filename,
            'original' => size_format($original_size),
            'optimized' => size_format($stats['optimized_size']),
            'savings' => $stats['savings_pct'] . '%',
        ]);

        wp_send_json_success([
            'id'             => $id,
            'filename'       => $filename,
            'original_size'  => size_format($original_size),
            'optimized_size' => size_format($stats['optimized_size']),
            'savings_pct'    => $stats['savings_pct'],
            'was_resized'    => $stats['was_resized'],
            'new_dimensions' => $stats['new_width'] . 'x' . $stats['new_height'],
            'webp_created'   => $stats['webp_size'] > 0,
            'thumb_savings'  => size_format($thumb_savings),
        ]);
    }

    public static function get_stats() {
        check_ajax_referer('sgu_optimizer_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

        $total_images = (int) wp_count_posts('attachment')->inherit;

        // Count only supported image types
        global $wpdb;
        $total_images = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_type = 'attachment'
             AND post_mime_type IN ('image/jpeg', 'image/png', 'image/gif')"
        );

        $optimized = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->postmeta}
                 WHERE meta_key = %s AND meta_value = %s",
                '_sgu_optimized', '1'
            )
        );

        $global = get_option('sgu_optimizer_global_stats', []);

        wp_send_json_success([
            'total_images'        => $total_images,
            'total_optimized'     => $optimized,
            'total_savings_bytes' => $global['total_savings_bytes'] ?? 0,
            'total_savings'       => size_format($global['total_savings_bytes'] ?? 0),
            'total_webp'          => $global['total_webp_created'] ?? 0,
            'last_bulk_run'       => $global['last_bulk_run'] ?? 'Never',
            'gd_available'        => SGU_Image_Optimizer::has_gd(),
            'webp_available'      => SGU_Image_Optimizer::has_webp_support(),
        ]);
    }

    public static function save_settings() {
        check_ajax_referer('sgu_optimizer_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

        $settings = [
            'max_width'       => absint($_POST['max_width'] ?? 1920),
            'jpeg_quality'    => max(1, min(100, absint($_POST['jpeg_quality'] ?? 82))),
            'png_compression' => max(0, min(9, absint($_POST['png_compression'] ?? 6))),
            'webp_quality'    => max(1, min(100, absint($_POST['webp_quality'] ?? 82))),
            'auto_optimize'   => !empty($_POST['auto_optimize']),
        ];

        update_option('sgu_image_optimizer_settings', $settings);
        wp_send_json_success(['message' => 'Settings saved']);
    }
}
