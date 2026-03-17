<?php
if (!defined('ABSPATH')) exit;

class SGU_No_Residentes {

    public static function init() {
        // Register ACF field
        add_action('acf/init', [__CLASS__, 'register_field']);

        // Frontend CSS
        add_action('wp_head', [__CLASS__, 'frontend_css']);

        // Admin columns for posts and defenders
        add_filter('manage_posts_columns', [__CLASS__, 'add_column']);
        add_action('manage_posts_custom_column', [__CLASS__, 'render_column'], 10, 2);
        add_filter('manage_defenders_posts_columns', [__CLASS__, 'add_column']);
        add_action('manage_defenders_posts_custom_column', [__CLASS__, 'render_column'], 10, 2);

        // Quick edit support
        add_action('quick_edit_custom_box', [__CLASS__, 'quick_edit_box'], 10, 2);
        add_action('save_post', [__CLASS__, 'save_quick_edit']);

        // AJAX toggle from dashboard
        add_action('wp_ajax_sgu_toggle_no_residentes', [__CLASS__, 'ajax_toggle']);
    }

    public static function register_field() {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_sgu_no_residentes',
            'title' => 'No Residentes',
            'fields' => [
                [
                    'key' => 'field_sgu_no_residentes',
                    'label' => 'No Residentes',
                    'name' => 'no_residentes',
                    'type' => 'true_false',
                    'instructions' => 'Activar si este vehículo es para No Residentes (USA)',
                    'default_value' => 0,
                    'ui' => 1,
                    'ui_on_text' => 'SI',
                    'ui_off_text' => 'NO',
                ],
            ],
            'location' => [
                [
                    ['param' => 'post_type', 'operator' => '==', 'value' => 'post'],
                ],
                [
                    ['param' => 'post_type', 'operator' => '==', 'value' => 'defenders'],
                ],
            ],
            'position' => 'side',
            'style' => 'default',
            'label_placement' => 'top',
            'menu_order' => 5,
        ]);
    }

    // ── Admin Columns ──

    public static function add_column($columns) {
        $new = [];
        foreach ($columns as $key => $val) {
            $new[$key] = $val;
            if ($key === 'title') {
                $new['no_residentes'] = '🇺🇸 No Res.';
            }
        }
        return $new;
    }

    public static function render_column($column, $post_id) {
        if ($column !== 'no_residentes') return;
        $val = get_field('no_residentes', $post_id);
        if ($val) {
            echo '<span style="background:#1e40af;color:#fff;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;white-space:nowrap;">🇺🇸 NO RES.</span>';
        } else {
            echo '<span style="color:#cbd5e1;">—</span>';
        }
    }

    // ── Quick Edit ──

    public static function quick_edit_box($column_name, $post_type) {
        if ($column_name !== 'no_residentes') return;
        if (!in_array($post_type, ['post', 'defenders'])) return;
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <label>
                    <input type="checkbox" name="no_residentes" value="1">
                    <span class="checkbox-title">🇺🇸 No Residentes</span>
                </label>
            </div>
        </fieldset>
        <?php
    }

    public static function save_quick_edit($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        if (!in_array(get_post_type($post_id), ['post', 'defenders'])) return;

        // Only process if this is a quick edit (inline-edit)
        if (!isset($_POST['_inline_edit'])) return;

        $val = isset($_POST['no_residentes']) ? 1 : 0;
        update_field('no_residentes', $val, $post_id);
    }

    // ── AJAX Toggle ──

    public static function ajax_toggle() {
        check_ajax_referer('sgu_dashboard_nonce', 'nonce');
        if (!current_user_can('edit_posts')) wp_send_json_error('Sin permisos');

        $post_id = absint($_POST['post_id'] ?? 0);
        if (!$post_id) wp_send_json_error('ID inválido');

        $current = get_field('no_residentes', $post_id);
        $new_val = $current ? 0 : 1;
        update_field('no_residentes', $new_val, $post_id);

        wp_send_json_success(['no_residentes' => (bool)$new_val]);
    }

    // ── Frontend CSS ──

    public static function frontend_css() {
        if (is_admin()) return;
        ?>
        <style>
        .sgu-nores-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: linear-gradient(135deg, #1e3a5f 0%, #1e40af 100%);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 4px;
            letter-spacing: 0.04em;
            z-index: 10;
            text-decoration: none;
            line-height: 1.2;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .post-thumbnail-link {
            position: relative;
        }
        .sgu-nores-badge-single {
            display: inline-block;
            background: linear-gradient(135deg, #1e3a5f 0%, #1e40af 100%);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 6px;
            letter-spacing: 0.04em;
            margin-bottom: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        </style>
        <?php
    }
}
