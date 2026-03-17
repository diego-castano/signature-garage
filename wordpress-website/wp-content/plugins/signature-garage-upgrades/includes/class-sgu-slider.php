<?php
if (!defined('ABSPATH')) exit;

class SGU_Slider {

    private static $option_key = 'sgu_hero_slider';

    public static function init() {
        add_action('wp_ajax_sgu_slider_search', [__CLASS__, 'ajax_search']);
        add_action('wp_ajax_sgu_slider_save', [__CLASS__, 'ajax_save']);
        add_action('wp_ajax_sgu_slider_get', [__CLASS__, 'ajax_get']);
    }

    public static function get_slider_data() {
        return wp_parse_args(get_option(self::$option_key, []), [
            'vehicle_ids'    => [],
            'autoplay'       => true,
            'autoplay_speed' => 4000,
            'transition'     => 'fade',
            'show_dots'      => true,
            'show_arrows'    => true,
            'show_info'      => true,
        ]);
    }

    public static function get_slides() {
        $data = self::get_slider_data();
        if (empty($data['vehicle_ids'])) return [];

        $slides = [];
        foreach ($data['vehicle_ids'] as $id) {
            $id = absint($id);
            if (!$id || get_post_status($id) !== 'publish') continue;

            $info = get_field('informations', $id);
            $thumb = self::get_vehicle_thumb($id, 'full');
            if (!$thumb) continue;

            $slides[] = [
                'id'         => $id,
                'title'      => get_the_title($id),
                'image'      => $thumb,
                'year'       => $info['year'] ?? '',
                'make'       => $info['make'] ?? '',
                'model'      => $info['model'] ?? '',
                'price'      => get_field('price', $id) ?: '',
                'sold'       => get_field('sold', $id) === 'v1',
                'permalink'  => get_permalink($id),
                'no_res'     => (bool) get_field('no_residentes', $id),
            ];
        }
        return $slides;
    }

    // ── Get thumbnail with ACF gallery fallback ──

    private static function get_vehicle_thumb($post_id, $size = 'thumbnail') {
        $thumb = get_the_post_thumbnail_url($post_id, $size);
        if ($thumb) return $thumb;

        // Fallback: first image from ACF 'images' gallery
        $images = get_field('images', $post_id);
        if (!empty($images) && is_array($images)) {
            $first = $images[0];
            if (isset($first['sizes'][$size])) return $first['sizes'][$size];
            if (isset($first['url'])) return $first['url'];
        }

        return '';
    }

    // ── AJAX: Search vehicles ──

    public static function ajax_search() {
        check_ajax_referer('sgu_slider_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

        $term = sanitize_text_field($_POST['term'] ?? '');
        $exclude = array_map('absint', (array)($_POST['exclude'] ?? []));

        $args = [
            'post_type'      => 'post',
            'posts_per_page' => 30,
            'post__not_in'   => $exclude,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        if ($term) {
            // Search in title + ACF make/model fields
            $args['meta_query'] = ['relation' => 'OR'];
            $args['meta_query'][] = [
                'key'     => 'informations_make',
                'value'   => $term,
                'compare' => 'LIKE',
            ];
            $args['meta_query'][] = [
                'key'     => 'informations_model',
                'value'   => $term,
                'compare' => 'LIKE',
            ];
            // Also search by title
            $args['_sgu_title_search'] = $term;
            add_filter('posts_where', [__CLASS__, 'title_or_meta_search'], 10, 2);
        }

        $query = new WP_Query($args);

        if ($term) {
            remove_filter('posts_where', [__CLASS__, 'title_or_meta_search'], 10);
        }

        $results = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $pid = get_the_ID();
                $info = get_field('informations');
                $results[] = [
                    'id'    => $pid,
                    'title' => get_the_title(),
                    'thumb' => self::get_vehicle_thumb($pid),
                    'make'  => $info['make'] ?? '',
                    'year'  => $info['year'] ?? '',
                    'price' => get_field('price') ?: '',
                    'sold'  => get_field('sold') === 'v1',
                ];
            }
            wp_reset_postdata();
        }

        wp_send_json_success($results);
    }

    // Allow title OR meta search (not AND)
    public static function title_or_meta_search($where, $query) {
        global $wpdb;
        $term = $query->get('_sgu_title_search');
        if (empty($term)) return $where;

        $like = '%' . $wpdb->esc_like($term) . '%';
        // Replace the meta_query AND with OR that includes title
        $where = preg_replace(
            '/AND\s*\(\s*\(\s*(' . preg_quote($wpdb->postmeta) . '\.meta_key.*?)\)\s*\)/s',
            'AND ( (' . $wpdb->posts . '.post_title LIKE \'' . esc_sql($like) . '\') OR ($1) )',
            $where,
            1
        );
        return $where;
    }

    // ── AJAX: Save slider config ──

    public static function ajax_save() {
        check_ajax_referer('sgu_slider_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

        $vehicle_ids = array_map('absint', (array) json_decode(stripslashes($_POST['vehicle_ids'] ?? '[]'), true));
        $vehicle_ids = array_filter($vehicle_ids);

        $data = [
            'vehicle_ids'    => $vehicle_ids,
            'autoplay'       => !empty($_POST['autoplay']),
            'autoplay_speed' => max(1000, min(15000, absint($_POST['autoplay_speed'] ?? 4000))),
            'transition'     => in_array($_POST['transition'] ?? '', ['fade', 'slide']) ? $_POST['transition'] : 'fade',
            'show_dots'      => !empty($_POST['show_dots']),
            'show_arrows'    => !empty($_POST['show_arrows']),
            'show_info'      => !empty($_POST['show_info']),
        ];

        update_option(self::$option_key, $data);

        wp_send_json_success([
            'message' => 'Slider saved',
            'count'   => count($vehicle_ids),
        ]);
    }

    // ── AJAX: Get current slider config ──

    public static function ajax_get() {
        check_ajax_referer('sgu_slider_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

        $data = self::get_slider_data();
        $vehicles = [];

        foreach ($data['vehicle_ids'] as $id) {
            $id = absint($id);
            if (!$id || get_post_status($id) !== 'publish') continue;

            $info = get_field('informations', $id);
            $vehicles[] = [
                'id'    => $id,
                'title' => get_the_title($id),
                'thumb' => self::get_vehicle_thumb($id),
                'make'  => $info['make'] ?? '',
                'year'  => $info['year'] ?? '',
                'price' => get_field('price', $id) ?: '',
                'sold'  => get_field('sold', $id) === 'v1',
            ];
        }

        wp_send_json_success([
            'vehicles' => $vehicles,
            'settings' => [
                'autoplay'       => $data['autoplay'],
                'autoplay_speed' => $data['autoplay_speed'],
                'transition'     => $data['transition'],
                'show_dots'      => $data['show_dots'],
                'show_arrows'    => $data['show_arrows'],
                'show_info'      => $data['show_info'],
            ],
        ]);
    }
}
