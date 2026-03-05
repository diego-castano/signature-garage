<?php
if (!defined('ABSPATH')) exit;

class SGU_Image_Hooks {

    public static function init() {
        $settings = SGU_Image_Optimizer::get_settings();
        if (empty($settings['auto_optimize'])) return;

        add_filter('wp_handle_upload', [__CLASS__, 'on_upload'], 10, 2);
        add_filter('wp_generate_attachment_metadata', [__CLASS__, 'on_metadata_generated'], 10, 2);
        add_action('delete_attachment', [__CLASS__, 'on_delete']);
    }

    public static function on_upload($upload, $context) {
        if (!isset($upload['file']) || !isset($upload['type'])) return $upload;

        $supported = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($upload['type'], $supported)) return $upload;

        SGU_Logger::info('image-upload', 'Auto-optimizing uploaded image', ['file' => basename($upload['file']), 'type' => $upload['type']]);
        $stats = SGU_Image_Optimizer::optimize_image($upload['file']);
        if ($stats) {
            SGU_Logger::info('image-upload', 'Upload optimized', [
                'file' => basename($upload['file']),
                'original' => size_format($stats['original_size']),
                'optimized' => size_format($stats['optimized_size']),
                'savings' => $stats['savings_pct'] . '%',
                'resized' => $stats['was_resized'],
            ]);
        }

        return $upload;
    }

    public static function on_metadata_generated($metadata, $attachment_id) {
        $file = get_attached_file($attachment_id);
        if (!$file || !SGU_Image_Optimizer::is_supported_image($file)) return $metadata;

        // Already optimized in on_upload, now store stats and handle thumbnails
        $original_stats = get_post_meta($attachment_id, '_sgu_optimization_data', true);

        if (!$original_stats) {
            // If upload hook didn't fire (e.g. regenerate thumbnails), optimize now
            $original_stats = SGU_Image_Optimizer::optimize_image($file, $attachment_id);
        } else {
            update_post_meta($attachment_id, '_sgu_optimization_data', $original_stats);
        }

        // Create WebP for the main file
        SGU_Image_Optimizer::create_webp($file);

        // Optimize all thumbnails and create their WebP versions
        SGU_Image_Optimizer::optimize_thumbnails($attachment_id);

        // Mark as optimized
        update_post_meta($attachment_id, '_sgu_optimized', 1);

        // Update global stats
        self::update_global_stats($attachment_id);

        return $metadata;
    }

    public static function on_delete($attachment_id) {
        SGU_Logger::info('image-delete', 'Cleaning up WebP files for attachment', ['id' => $attachment_id]);
        SGU_Image_Optimizer::delete_webp_files($attachment_id);

        // Decrement global stats
        $data = get_post_meta($attachment_id, '_sgu_optimization_data', true);
        if (is_array($data)) {
            $global = get_option('sgu_optimizer_global_stats', []);
            if (!empty($global['total_optimized'])) {
                $global['total_optimized'] = max(0, $global['total_optimized'] - 1);
                $savings = ($data['savings_bytes'] ?? 0) + ($data['thumbnails_savings'] ?? 0);
                $global['total_savings_bytes'] = max(0, ($global['total_savings_bytes'] ?? 0) - $savings);
                update_option('sgu_optimizer_global_stats', $global);
            }
        }
    }

    private static function update_global_stats($attachment_id) {
        $data = get_post_meta($attachment_id, '_sgu_optimization_data', true);
        if (!is_array($data)) return;

        $global = get_option('sgu_optimizer_global_stats', [
            'total_optimized'     => 0,
            'total_savings_bytes' => 0,
            'total_webp_created'  => 0,
        ]);

        $global['total_optimized'] = ($global['total_optimized'] ?? 0) + 1;
        $savings = ($data['savings_bytes'] ?? 0) + ($data['thumbnails_savings'] ?? 0);
        $global['total_savings_bytes'] = ($global['total_savings_bytes'] ?? 0) + $savings;
        if (!empty($data['webp_size'])) {
            $global['total_webp_created'] = ($global['total_webp_created'] ?? 0) + 1;
        }

        update_option('sgu_optimizer_global_stats', $global);
    }
}
