<?php
if (!defined('ABSPATH')) exit;

class SGU_Image_Optimizer {

    private static $default_settings = [
        'max_width'      => 1920,
        'jpeg_quality'   => 82,
        'png_compression'=> 6,
        'webp_quality'   => 82,
        'auto_optimize'  => true,
    ];

    public static function get_settings() {
        return wp_parse_args(
            get_option('sgu_image_optimizer_settings', []),
            self::$default_settings
        );
    }

    public static function is_supported_image($file_path) {
        $mime = wp_check_filetype(basename($file_path));
        $supported = ['image/jpeg', 'image/png', 'image/gif'];
        return in_array($mime['type'], $supported);
    }

    public static function optimize_image($file_path, $attachment_id = null) {
        if (!file_exists($file_path) || !self::is_supported_image($file_path)) {
            return false;
        }

        $settings = self::get_settings();
        $original_size = filesize($file_path);
        $info = getimagesize($file_path);
        if (!$info) return false;

        $orig_width = $info[0];
        $orig_height = $info[1];
        $mime = $info['mime'];
        $was_resized = false;

        wp_raise_memory_limit('image');

        $image = self::load_image($file_path, $mime);
        if (!$image) return false;

        // Resize if wider than max
        if ($orig_width > $settings['max_width']) {
            $ratio = $settings['max_width'] / $orig_width;
            $new_width = $settings['max_width'];
            $new_height = (int) round($orig_height * $ratio);

            $resized = imagecreatetruecolor($new_width, $new_height);
            self::preserve_transparency($resized, $mime);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height);
            imagedestroy($image);
            $image = $resized;
            $was_resized = true;
        } else {
            $new_width = $orig_width;
            $new_height = $orig_height;
        }

        // Save optimized
        self::save_image($image, $file_path, $mime, $settings);

        // Generate WebP
        $webp_size = 0;
        $webp_path = self::create_webp_from_resource($image, $file_path, $settings['webp_quality']);
        if ($webp_path && file_exists($webp_path)) {
            $webp_size = filesize($webp_path);
        }

        imagedestroy($image);
        clearstatcache(true, $file_path);
        $optimized_size = filesize($file_path);

        $stats = [
            'optimized_at'    => current_time('mysql'),
            'original_size'   => $original_size,
            'optimized_size'  => $optimized_size,
            'savings_bytes'   => $original_size - $optimized_size,
            'savings_pct'     => $original_size > 0 ? round((1 - $optimized_size / $original_size) * 100, 1) : 0,
            'webp_size'       => $webp_size,
            'was_resized'     => $was_resized,
            'original_width'  => $orig_width,
            'original_height' => $orig_height,
            'new_width'       => $new_width,
            'new_height'      => $new_height,
        ];

        if ($attachment_id) {
            update_post_meta($attachment_id, '_sgu_optimization_data', $stats);
        }

        return $stats;
    }

    public static function optimize_thumbnails($attachment_id) {
        $metadata = wp_get_attachment_metadata($attachment_id);
        if (!$metadata || empty($metadata['sizes'])) return 0;

        $upload_dir = wp_upload_dir();
        $base_dir = trailingslashit(dirname($upload_dir['basedir'] . '/' . $metadata['file']));
        $settings = self::get_settings();
        $total_savings = 0;
        $count = 0;

        foreach ($metadata['sizes'] as $size_name => $size_data) {
            $thumb_path = $base_dir . $size_data['file'];
            if (!file_exists($thumb_path) || !self::is_supported_image($thumb_path)) continue;

            $before = filesize($thumb_path);
            $info = getimagesize($thumb_path);
            if (!$info) continue;

            wp_raise_memory_limit('image');
            $image = self::load_image($thumb_path, $info['mime']);
            if (!$image) continue;

            self::save_image($image, $thumb_path, $info['mime'], $settings);
            self::create_webp_from_resource($image, $thumb_path, $settings['webp_quality']);
            imagedestroy($image);

            clearstatcache(true, $thumb_path);
            $total_savings += $before - filesize($thumb_path);
            $count++;
        }

        // Update meta with thumbnail info
        $existing = get_post_meta($attachment_id, '_sgu_optimization_data', true);
        if (is_array($existing)) {
            $existing['thumbnails_optimized'] = $count;
            $existing['thumbnails_savings'] = $total_savings;
            update_post_meta($attachment_id, '_sgu_optimization_data', $existing);
        }

        return $total_savings;
    }

    public static function create_webp($file_path) {
        if (!function_exists('imagewebp')) return false;
        if (!file_exists($file_path) || !self::is_supported_image($file_path)) return false;

        $info = getimagesize($file_path);
        if (!$info) return false;

        $settings = self::get_settings();
        wp_raise_memory_limit('image');
        $image = self::load_image($file_path, $info['mime']);
        if (!$image) return false;

        $result = self::create_webp_from_resource($image, $file_path, $settings['webp_quality']);
        imagedestroy($image);
        return $result;
    }

    public static function delete_webp_files($attachment_id) {
        $metadata = wp_get_attachment_metadata($attachment_id);
        if (!$metadata) return;

        $upload_dir = wp_upload_dir();
        $file = $upload_dir['basedir'] . '/' . $metadata['file'];
        $webp = self::get_webp_path($file);
        if (file_exists($webp)) @unlink($webp);

        if (!empty($metadata['sizes'])) {
            $base_dir = trailingslashit(dirname($file));
            foreach ($metadata['sizes'] as $size_data) {
                $thumb_webp = self::get_webp_path($base_dir . $size_data['file']);
                if (file_exists($thumb_webp)) @unlink($thumb_webp);
            }
        }
    }

    public static function has_gd() {
        return extension_loaded('gd') && function_exists('imagecreatefromjpeg');
    }

    public static function has_webp_support() {
        return function_exists('imagewebp');
    }

    // --- Private helpers ---

    private static function load_image($file_path, $mime) {
        switch ($mime) {
            case 'image/jpeg': return @imagecreatefromjpeg($file_path);
            case 'image/png':  return @imagecreatefrompng($file_path);
            case 'image/gif':  return @imagecreatefromgif($file_path);
            default: return false;
        }
    }

    private static function save_image($resource, $file_path, $mime, $settings) {
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($resource, $file_path, $settings['jpeg_quality']);
                break;
            case 'image/png':
                imagesavealpha($resource, true);
                imagepng($resource, $file_path, $settings['png_compression']);
                break;
            case 'image/gif':
                imagegif($resource, $file_path);
                break;
        }
    }

    private static function preserve_transparency($resource, $mime) {
        if ($mime === 'image/png' || $mime === 'image/gif') {
            imagealphablending($resource, false);
            imagesavealpha($resource, true);
            $transparent = imagecolorallocatealpha($resource, 0, 0, 0, 127);
            imagefill($resource, 0, 0, $transparent);
        }
    }

    private static function create_webp_from_resource($resource, $original_path, $quality) {
        if (!function_exists('imagewebp')) return false;

        $webp_path = self::get_webp_path($original_path);
        imagepalettetotruecolor($resource);
        imagesavealpha($resource, true);
        imagewebp($resource, $webp_path, $quality);

        return file_exists($webp_path) ? $webp_path : false;
    }

    private static function get_webp_path($file_path) {
        return $file_path . '.webp';
    }
}
