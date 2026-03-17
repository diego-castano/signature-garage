<?php
if (!defined('ABSPATH')) exit;

class SGU_Frontend_Speed {

    // CSS handles that are NOT needed for first paint
    private static $defer_styles = [
        'magnific',
        'slick',
        'slick-theme',
        'lity-css',
        'fontawesome',
        'sbi-styles',
    ];

    public static function init() {
        if (is_admin()) return;

        // Output buffer to process ALL HTML (lazy loading for all img tags)
        add_action('template_redirect', [__CLASS__, 'start_output_buffer']);

        // Defer non-critical CSS
        add_filter('style_loader_tag', [__CLASS__, 'defer_non_critical_css'], 10, 4);

        // Add preconnect and resource hints
        add_action('wp_head', [__CLASS__, 'resource_hints'], 1);

        // Disable emoji scripts (saves ~45KB)
        add_action('init', [__CLASS__, 'disable_emojis']);
    }

    // ── Output Buffer for Lazy Loading ──

    public static function start_output_buffer() {
        ob_start([__CLASS__, 'process_html']);
    }

    public static function process_html($html) {
        if (empty($html)) return $html;

        // Skip if not HTML
        if (strpos($html, '<html') === false) return $html;

        // Count images to skip first 1-2 (above the fold / LCP)
        $img_count = 0;

        $html = preg_replace_callback('/<img\b([^>]*?)(\s*\/?>)/i', function ($match) use (&$img_count) {
            $attrs = $match[1];
            $close = $match[2];
            $img_count++;

            // Skip first image (likely hero/LCP) — don't lazy load it
            if ($img_count <= 1) {
                // Add fetchpriority="high" to first image if not present
                if (strpos($attrs, 'fetchpriority') === false) {
                    $attrs .= ' fetchpriority="high"';
                }
                return '<img' . $attrs . $close;
            }

            // Add loading="lazy" if not present
            if (strpos($attrs, 'loading=') === false) {
                $attrs .= ' loading="lazy"';
            }

            // Add decoding="async" if not present
            if (strpos($attrs, 'decoding=') === false) {
                $attrs .= ' decoding="async"';
            }

            return '<img' . $attrs . $close;
        }, $html);

        return $html;
    }

    // ── Defer Non-Critical CSS ──

    public static function defer_non_critical_css($html, $handle, $href, $media) {
        if (!in_array($handle, self::$defer_styles)) return $html;

        // Load CSS non-blocking: preload + onload swap
        return sprintf(
            '<link rel="preload" href="%s" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' .
            "\n" . '<noscript>%s</noscript>' . "\n",
            esc_url($href),
            $html
        );
    }

    // ── Resource Hints ──

    public static function resource_hints() {
        $domains = [
            'https://cdnjs.cloudflare.com',
            'https://www.googletagmanager.com',
            'https://connect.facebook.net',
        ];

        foreach ($domains as $domain) {
            echo '<link rel="preconnect" href="' . esc_url($domain) . '" crossorigin>' . "\n";
            echo '<link rel="dns-prefetch" href="' . esc_url($domain) . '">' . "\n";
        }
    }

    // ── Disable Emojis ──

    public static function disable_emojis() {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

        add_filter('tiny_mce_plugins', function ($plugins) {
            return is_array($plugins) ? array_diff($plugins, ['wpemoji']) : [];
        });

        add_filter('wp_resource_hints', function ($urls, $relation_type) {
            if ($relation_type === 'dns-prefetch') {
                $urls = array_filter($urls, function ($url) {
                    return strpos($url, 'https://s.w.org/images/core/emoji') === false;
                });
            }
            return $urls;
        }, 10, 2);
    }
}
