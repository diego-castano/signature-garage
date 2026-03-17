<?php
if (!defined('ABSPATH')) exit;

class SGU_Dashboard {

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'register_dashboard_page'], 9998);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_action('wp_ajax_sgu_vehicle_set_draft', [__CLASS__, 'ajax_vehicle_set_draft']);
        add_action('wp_ajax_sgu_get_vehicles', [__CLASS__, 'ajax_get_vehicles']);
        add_action('wp_ajax_sgu_dismiss_entry', [__CLASS__, 'ajax_dismiss_entry']);
        add_action('wp_ajax_sgu_mark_spam', [__CLASS__, 'ajax_mark_spam']);
        add_action('wp_ajax_sgu_toggle_replied', [__CLASS__, 'ajax_toggle_replied']);
    }

    public static function register_dashboard_page() {
        remove_action('welcome_panel', 'wp_welcome_panel');

        add_action('load-index.php', function () {
            remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
            remove_meta_box('dashboard_activity', 'dashboard', 'normal');
            remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
            remove_meta_box('dashboard_primary', 'dashboard', 'side');
            remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
            remove_meta_box('rg_forms_dashboard', 'dashboard', 'normal');
            remove_meta_box('rg_forms_dashboard', 'dashboard', 'side');
            remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'normal');
            remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'side');
            remove_meta_box('yoast_db_widget', 'dashboard', 'normal');
        });

        add_action('wp_dashboard_setup', [__CLASS__, 'add_dashboard_widgets'], 9999);
    }

    public static function add_dashboard_widgets() {
        global $wp_meta_boxes;

        // Clear all existing widgets but keep the structure
        if (isset($wp_meta_boxes['dashboard'])) {
            foreach ($wp_meta_boxes['dashboard'] as $context => $priorities) {
                foreach ($priorities as $priority => $boxes) {
                    foreach ($boxes as $id => $box) {
                        remove_meta_box($id, 'dashboard', $context);
                    }
                }
            }
        }

        // Single widget that renders our full custom layout
        wp_add_dashboard_widget(
            'sgu_custom_dashboard',
            'Signature Garage',
            [__CLASS__, 'render_full_dashboard']
        );
    }

    public static function enqueue_assets($hook) {
        if ($hook !== 'index.php') return;

        wp_enqueue_style(
            'sgu-inter-font',
            'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
            [],
            null
        );

        wp_enqueue_style(
            'sgu-dashboard',
            SGU_PLUGIN_URL . 'admin/css/sgu-dashboard.css',
            ['sgu-inter-font'],
            SGU_VERSION
        );

        // Chart.js from CDN
        wp_enqueue_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js',
            [],
            '4.4.7',
            true
        );

        wp_enqueue_script(
            'sgu-dashboard',
            SGU_PLUGIN_URL . 'admin/js/sgu-dashboard.js',
            ['chartjs'],
            SGU_VERSION,
            true
        );

        wp_localize_script('sgu-dashboard', 'sguDashboard', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('sgu_dashboard_nonce'),
        ]);
    }

    public static function render_full_dashboard() {
        echo '<div class="sgu-dash">';

        // Top row: 50/50
        echo '<div class="sgu-dash-top">';
        echo '<div class="sgu-dash-col">';
        self::render_contact_forms();
        echo '</div>';
        echo '<div class="sgu-dash-col">';
        self::render_recent_vehicles();
        echo '</div>';
        echo '</div>';

        // Analytics full width
        self::render_analytics();

        // Vehicles table full width
        self::render_vehicles_table();

        echo '</div>';
    }

    // ───────────────── CONTACTS ─────────────────
    public static function render_contact_forms() {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_entry';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) {
            echo '<div class="sgu-card"><div class="sgu-card-header"><h3>Últimas Consultas</h3></div><div class="sgu-card-body"><p class="sgu-empty">Gravity Forms no disponible.</p></div></div>';
            return;
        }

        // Get state lists
        $dismissed = get_option('sgu_dismissed_entries', []);
        $spam_list = get_option('sgu_spam_entries', []);
        $replied_list = get_option('sgu_replied_entries', []);

        $dismissed_ids = !empty($dismissed) ? implode(',', array_map('absint', $dismissed)) : '0';
        $spam_ids = !empty($spam_list) ? implode(',', array_map('absint', $spam_list)) : '0';

        $entries = $wpdb->get_results("
            SELECT e.id, e.form_id, e.date_created, e.is_read,
                   f.title as form_title
            FROM {$wpdb->prefix}gf_entry e
            JOIN {$wpdb->prefix}gf_form f ON e.form_id = f.id
            WHERE e.status = 'active'
            AND e.id NOT IN ({$dismissed_ids})
            AND e.id NOT IN ({$spam_ids})
            ORDER BY e.date_created DESC
        ");

        // Auto-filter spam: detect and auto-dismiss spam entries
        $clean_entries = [];
        $auto_spam_ids = [];
        foreach ($entries as $entry) {
            $meta = $wpdb->get_results($wpdb->prepare("
                SELECT meta_key, meta_value
                FROM {$wpdb->prefix}gf_entry_meta
                WHERE entry_id = %d AND CAST(meta_key AS UNSIGNED) > 0
                ORDER BY CAST(meta_key AS UNSIGNED) ASC
                LIMIT 8
            ", $entry->id));

            $name = '';
            $email = '';
            $message = '';
            foreach ($meta as $m) {
                $val = trim($m->meta_value);
                if (empty($val)) continue;
                if (empty($name) && !filter_var($val, FILTER_VALIDATE_EMAIL) && strlen($val) < 100 && !preg_match('/^https?:/', $val)) {
                    $name = $val;
                } elseif (empty($email) && filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $email = $val;
                } elseif (empty($message) && strlen($val) > 10) {
                    $message = $val;
                }
            }

            if (self::detect_spam($name, $email, $message)) {
                $auto_spam_ids[] = (int)$entry->id;
                continue;
            }

            // Store parsed data for rendering
            $entry->_name = $name;
            $entry->_email = $email;
            $entry->_message = $message;
            $clean_entries[] = $entry;
        }

        // Persist auto-detected spam so they don't reappear
        if (!empty($auto_spam_ids)) {
            $spam_list = array_merge($spam_list, $auto_spam_ids);
            $spam_list = array_unique(array_slice($spam_list, -500));
            update_option('sgu_spam_entries', $spam_list);
            // Also mark as spam in GF
            $ids_str = implode(',', $auto_spam_ids);
            $wpdb->query("UPDATE {$wpdb->prefix}gf_entry SET status = 'spam' WHERE id IN ({$ids_str})");
        }

        echo '<div class="sgu-card sgu-card-contacts">';
        echo '<div class="sgu-card-header sgu-header-contacts">';
        echo '<h3><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg> Últimas Consultas</h3>';
        $unread_count = 0;
        foreach ($clean_entries as $e) { if (empty($e->is_read)) $unread_count++; }
        $total_label = count($clean_entries) . ' consultas';
        if ($unread_count > 0) $total_label = $unread_count . ' nuevas';
        echo '<span class="sgu-badge">' . $total_label . '</span>';
        echo '</div>';
        echo '<div class="sgu-card-body sgu-contact-scroll">';

        if (empty($clean_entries)) {
            echo '<p class="sgu-empty">No hay consultas recientes.</p>';
        }

        foreach ($clean_entries as $entry) {
            $name = $entry->_name;
            $email = $entry->_email;
            $message = $entry->_message;

            $is_unread = empty($entry->is_read);
            $time_ago = human_time_diff(strtotime($entry->date_created), current_time('timestamp'));
            $gf_url = admin_url('admin.php?page=gf_entries&view=entry&id=' . $entry->form_id . '&lid=' . $entry->id);

            $is_replied = in_array((int)$entry->id, $replied_list);
            $classes = 'sgu-contact-item';
            if ($is_replied) $classes .= ' sgu-replied';
            elseif ($is_unread) $classes .= ' sgu-unread';

            echo '<div class="' . $classes . '" data-entry-id="' . esc_attr($entry->id) . '">';

            // Avatar
            echo '<div class="sgu-contact-avatar">' . esc_html(mb_strtoupper(mb_substr($name ?: '?', 0, 1))) . '</div>';

            echo '<div class="sgu-contact-content">';
            echo '<div class="sgu-contact-top">';
            echo '<span class="sgu-contact-name">' . esc_html($name ?: 'Sin nombre') . '</span>';
            if ($is_replied) echo '<span class="sgu-tag sgu-tag-replied">Respondido</span>';
            echo '<span class="sgu-tag sgu-tag-form">' . esc_html($entry->form_title) . '</span>';
            echo '<span class="sgu-contact-time">' . esc_html($time_ago) . '</span>';
            echo '</div>';
            if ($email) echo '<div class="sgu-contact-email">' . esc_html($email) . '</div>';
            if ($message) echo '<div class="sgu-contact-msg">' . esc_html(wp_trim_words($message, 18)) . '</div>';

            // Actions
            echo '<div class="sgu-contact-actions">';
            if ($email) {
                echo '<a href="mailto:' . esc_attr($email) . '?subject=Re: Consulta Signature Garage" class="sgu-btn sgu-btn-primary sgu-btn-sm" title="Responder por email">';
                echo '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg> Responder</a>';
            }
            echo '<a href="' . esc_url($gf_url) . '" class="sgu-btn sgu-btn-ghost sgu-btn-sm" title="Ver en Gravity Forms">';
            echo '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg> Ver</a>';
            echo '<button class="sgu-btn ' . ($is_replied ? 'sgu-btn-replied-active' : 'sgu-btn-ghost') . ' sgu-btn-sm sgu-btn-replied" data-id="' . esc_attr($entry->id) . '" title="' . ($is_replied ? 'Marcar como no respondido' : 'Marcar como respondido') . '">';
            echo '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> ' . ($is_replied ? 'Respondido' : 'Respondido') . '</button>';
            echo '<button class="sgu-btn sgu-btn-ghost sgu-btn-sm sgu-btn-spam" data-id="' . esc_attr($entry->id) . '" title="Marcar como spam">';
            echo '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg> Spam</button>';
            echo '<button class="sgu-btn sgu-btn-ghost sgu-btn-sm sgu-btn-dismiss" data-id="' . esc_attr($entry->id) . '" title="Descartar">';
            echo '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
            echo '</div>';

            echo '</div>'; // content
            echo '</div>'; // item
        }

        echo '</div>'; // body
        echo '</div>'; // card
    }

    private static function detect_spam($name, $email, $message) {
        $text = strtolower($name . ' ' . $email . ' ' . $message);

        // Random character strings (keyboard mashing)
        if ($name && preg_match('/^[a-z]{2,}[A-Z][a-z]{2,}[A-Z]/', $name)) return true;
        if ($name && preg_match('/[^a-záéíóúñü\s\.\-\']/i', $name) && strlen($name) > 3) return true;

        // Common spam patterns
        $spam_words = ['viagra', 'casino', 'lottery', 'bitcoin', 'crypto', 'forex', 'cbd', 'seo services', 'backlinks', 'buy followers', 'free money', 'click here', 'congratulations', 'you have been selected', 'nigerian', 'inheritance', 'prince'];
        foreach ($spam_words as $word) {
            if (strpos($text, $word) !== false) return true;
        }

        // URLs in name field
        if ($name && preg_match('/https?:|www\.|\.com|\.net|\.org/i', $name)) return true;

        // Gibberish detection: consonant-heavy strings
        if ($name && preg_match('/[bcdfghjklmnpqrstvwxyz]{5,}/i', $name)) return true;

        // Very short or suspicious messages
        if ($message && strlen($message) < 5 && !empty($message)) return true;

        // Email from disposable domains
        $disposable = ['mailinator.com', 'tempmail.com', 'throwaway.email', 'guerrillamail.com', 'yopmail.com'];
        if ($email) {
            $domain = strtolower(substr(strrchr($email, '@'), 1));
            if (in_array($domain, $disposable)) return true;
        }

        return false;
    }

    // ───────────────── RECENT VEHICLES ─────────────────
    public static function render_recent_vehicles() {
        $posts = get_posts([
            'post_type'      => ['post', 'defenders'],
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        echo '<div class="sgu-card sgu-card-vehicles">';
        echo '<div class="sgu-card-header sgu-header-vehicles">';
        echo '<h3><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg> Últimos Publicados</h3>';
        echo '<span class="sgu-card-count">' . count($posts) . ' recientes</span>';
        echo '</div>';
        echo '<div class="sgu-card-body sgu-vehicles-scroll">';

        if (empty($posts)) {
            echo '<p class="sgu-empty">No hay vehículos publicados.</p>';
        }

        foreach ($posts as $p) {
            $thumb = get_the_post_thumbnail_url($p->ID, 'medium');
            $info = get_field('informations', $p->ID);
            $price = get_field('price', $p->ID);
            $sold = get_field('sold', $p->ID);
            $no_res = get_field('no_residentes', $p->ID);
            $edit_url = get_edit_post_link($p->ID);
            $type_label = $p->post_type === 'defenders' ? 'Defender' : 'Auto';
            $date = get_the_date('d M Y', $p->ID);

            $row_class = 'sgu-vehicle-row' . ($no_res ? ' sgu-row-nores' : '');
            echo '<div class="' . $row_class . '">';
            echo '<div class="sgu-vehicle-img">';
            if ($thumb) {
                echo '<img src="' . esc_url($thumb) . '" alt="' . esc_attr($p->post_title) . '" loading="lazy">';
            } else {
                echo '<div class="sgu-vehicle-noimg"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg></div>';
            }
            echo '</div>';
            echo '<div class="sgu-vehicle-detail">';
            echo '<div class="sgu-vehicle-name">' . esc_html($p->post_title) . '</div>';
            echo '<div class="sgu-vehicle-sub">';
            if (!empty($info['year'])) echo '<span>' . esc_html($info['year']) . '</span>';
            echo '<span class="sgu-type-tag sgu-type-' . esc_attr($p->post_type) . '">' . esc_html($type_label) . '</span>';
            echo '<span>' . esc_html($date) . '</span>';
            if ($sold === 'v1') echo '<span class="sgu-sold-tag">VENDIDO</span>';
            if ($no_res) echo '<span class="sgu-nores-tag">&#x1F1FA;&#x1F1F8; NO RESIDENTES</span>';
            echo '</div>';
            echo '</div>';
            if ($price && (float)$price > 0) {
                echo '<div class="sgu-vehicle-price">$' . esc_html(number_format((float)$price, 0, ',', '.')) . '</div>';
            }
            echo '<div class="sgu-vehicle-acts">';
            echo '<a href="' . esc_url($edit_url) . '" class="sgu-btn sgu-btn-ghost sgu-btn-sm" title="Editar"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>';
            echo '<button class="sgu-btn sgu-btn-sm sgu-nores-toggle' . ($no_res ? ' sgu-nores-active' : ' sgu-btn-ghost') . '" data-id="' . esc_attr($p->ID) . '" title="Toggle No Residentes">&#x1F1FA;&#x1F1F8;</button>';
            echo '<button class="sgu-btn sgu-btn-danger-ghost sgu-btn-sm sgu-draft-btn" data-id="' . esc_attr($p->ID) . '" title="Pasar a borrador"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></button>';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>'; // body
        echo '</div>'; // card
    }

    // ───────────────── VEHICLES TABLE ─────────────────
    public static function render_vehicles_table() {
        echo '<div class="sgu-card sgu-card-full">';
        echo '<div class="sgu-card-header sgu-header-inventory">';
        echo '<h3><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h18v18H3z"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/></svg> Inventario Completo</h3>';
        echo '<span class="sgu-card-count" id="sgu-total-count"></span>';
        echo '</div>';

        echo '<div class="sgu-table-toolbar">';
        echo '<div class="sgu-search-wrap"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" id="sgu-filter-search" placeholder="Buscar vehículos..."></div>';
        echo '<div class="sgu-filter-group">';
        echo '<select id="sgu-filter-type"><option value="">Tipo</option><option value="post">Autos</option><option value="defenders">Defenders</option></select>';
        echo '<select id="sgu-filter-status"><option value="">Estado</option><option value="publish">Publicado</option><option value="draft">Borrador</option></select>';
        echo '<select id="sgu-filter-sold"><option value="">Venta</option><option value="no">Disponible</option><option value="v1">Vendido</option></select>';
        echo '</div>';
        echo '</div>';

        echo '<div class="sgu-table-wrap">';
        echo '<table id="sgu-vehicles-table">';
        echo '<thead><tr>';
        echo '<th class="sgu-th-img"></th>';
        echo '<th class="sgu-th-title">Vehículo</th>';
        echo '<th class="sgu-th-year">Año</th>';
        echo '<th class="sgu-th-type">Tipo</th>';
        echo '<th class="sgu-th-price">Precio</th>';
        echo '<th class="sgu-th-status">Estado</th>';
        echo '<th class="sgu-th-actions"></th>';
        echo '</tr></thead>';
        echo '<tbody id="sgu-vehicles-tbody">';
        // Skeleton rows
        for ($i = 0; $i < 5; $i++) {
            echo '<tr class="sgu-skeleton-row"><td colspan="7"><div class="sgu-skeleton"></div></td></tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        echo '<div class="sgu-table-footer">';
        echo '<div class="sgu-page-info" id="sgu-page-info"></div>';
        echo '<div class="sgu-page-nav">';
        echo '<button class="sgu-btn sgu-btn-ghost sgu-btn-sm" id="sgu-prev-page" disabled><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg> Anterior</button>';
        echo '<button class="sgu-btn sgu-btn-ghost sgu-btn-sm" id="sgu-next-page">Siguiente <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>';
        echo '</div>';
        echo '</div>';

        echo '</div>'; // card
    }

    // ───────────────── ANALYTICS ─────────────────
    public static function render_analytics() {
        global $wpdb;
        $table = $wpdb->prefix . 'sgu_pageviews';

        echo '<div class="sgu-card sgu-card-full sgu-card-analytics">';
        echo '<div class="sgu-card-header sgu-header-analytics">';
        echo '<h3><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg> Analíticas del Sitio</h3>';
        echo '</div>';

        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table) {
            echo '<div class="sgu-card-body"><p class="sgu-empty">Las analíticas se están inicializando. Los datos aparecerán pronto.</p></div></div>';
            return;
        }

        $today = current_time('Y-m-d');
        $week_ago = date('Y-m-d', strtotime('-7 days', strtotime($today)));
        $month_ago = date('Y-m-d', strtotime('-30 days', strtotime($today)));

        $views_today = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE DATE(created_at) = %s", $today
        ));
        $views_week = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE created_at >= %s", $week_ago . ' 00:00:00'
        ));
        $views_month = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE created_at >= %s", $month_ago . ' 00:00:00'
        ));

        $top_pages = $wpdb->get_results($wpdb->prepare(
            "SELECT url, COUNT(*) as views FROM {$table}
             WHERE created_at >= %s
             AND url NOT LIKE '%%favicon%%'
             AND url NOT LIKE '%%apple-touch-icon%%'
             AND url NOT LIKE '%%.well-known%%'
             AND url NOT LIKE '%%/wp-content/%%'
             AND url NOT LIKE '%%/wp-includes/%%'
             AND url NOT LIKE '%%.ico'
             AND url NOT LIKE '%%.png'
             AND url NOT LIKE '%%.jpg'
             AND url NOT LIKE '%%.js'
             AND url NOT LIKE '%%.css'
             GROUP BY url ORDER BY views DESC LIMIT 8",
            $week_ago . ' 00:00:00'
        ));

        $daily_views = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(created_at) as day, COUNT(*) as views FROM {$table}
             WHERE created_at >= %s
             GROUP BY DATE(created_at) ORDER BY day ASC",
            $week_ago . ' 00:00:00'
        ));

        // Referrer stats
        $top_referrers = $wpdb->get_results($wpdb->prepare(
            "SELECT CASE WHEN referrer = '' THEN 'Directo' ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(referrer, '/', 3), '//', -1) END as source,
             COUNT(*) as views FROM {$table}
             WHERE created_at >= %s
             GROUP BY source ORDER BY views DESC LIMIT 5",
            $week_ago . ' 00:00:00'
        ));

        echo '<div class="sgu-card-body">';

        // KPI cards
        echo '<div class="sgu-kpi-row">';
        echo '<div class="sgu-kpi"><div class="sgu-kpi-icon sgu-kpi-blue"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></div><div class="sgu-kpi-data"><span class="sgu-kpi-num">' . number_format($views_today) . '</span><span class="sgu-kpi-label">Visitas hoy</span></div></div>';
        echo '<div class="sgu-kpi"><div class="sgu-kpi-icon sgu-kpi-green"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div><div class="sgu-kpi-data"><span class="sgu-kpi-num">' . number_format($views_week) . '</span><span class="sgu-kpi-label">Últimos 7 días</span></div></div>';
        echo '<div class="sgu-kpi"><div class="sgu-kpi-icon sgu-kpi-purple"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><div class="sgu-kpi-data"><span class="sgu-kpi-num">' . number_format($views_month) . '</span><span class="sgu-kpi-label">Últimos 30 días</span></div></div>';
        echo '<div class="sgu-kpi"><div class="sgu-kpi-icon sgu-kpi-amber"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><div class="sgu-kpi-data"><span class="sgu-kpi-num">' . ($views_week > 0 ? number_format(round($views_week / 7)) : '0') . '</span><span class="sgu-kpi-label">Promedio diario</span></div></div>';
        echo '</div>';

        // Chart + top pages side by side
        echo '<div class="sgu-analytics-grid">';

        // Chart
        echo '<div class="sgu-analytics-chart-wrap">';
        echo '<canvas id="sgu-views-chart" height="220"></canvas>';

        // Pass data to JS
        $chart_labels = [];
        $chart_data = [];
        if (!empty($daily_views)) {
            foreach ($daily_views as $d) {
                $chart_labels[] = date('D d', strtotime($d->day));
                $chart_data[] = (int)$d->views;
            }
        }
        echo '<script type="application/json" id="sgu-chart-data">' . wp_json_encode([
            'labels' => $chart_labels,
            'data'   => $chart_data,
        ]) . '</script>';
        echo '</div>';

        // Top pages + referrers
        echo '<div class="sgu-analytics-sidebar">';

        if (!empty($top_pages)) {
            echo '<div class="sgu-analytics-section">';
            echo '<h4>Páginas más vistas</h4>';
            $max_pv = !empty($top_pages) ? $top_pages[0]->views : 1;
            foreach ($top_pages as $page) {
                $short = str_replace(home_url(), '', $page->url);
                if (empty($short) || $short === $page->url) $short = wp_parse_url($page->url, PHP_URL_PATH) ?: '/';
                if (empty($short)) $short = '/';
                $pct = round(($page->views / $max_pv) * 100);
                echo '<div class="sgu-top-item">';
                echo '<div class="sgu-top-bar"><div class="sgu-top-fill" style="width:' . $pct . '%"></div></div>';
                echo '<span class="sgu-top-url" title="' . esc_attr($page->url) . '">' . esc_html($short) . '</span>';
                echo '<span class="sgu-top-num">' . number_format($page->views) . '</span>';
                echo '</div>';
            }
            echo '</div>';
        }

        if (!empty($top_referrers)) {
            echo '<div class="sgu-analytics-section">';
            echo '<h4>Fuentes de tráfico</h4>';
            foreach ($top_referrers as $ref) {
                echo '<div class="sgu-ref-item">';
                echo '<span class="sgu-ref-source">' . esc_html($ref->source) . '</span>';
                echo '<span class="sgu-ref-num">' . number_format($ref->views) . '</span>';
                echo '</div>';
            }
            echo '</div>';
        }

        echo '</div>'; // sidebar
        echo '</div>'; // grid
        echo '</div>'; // body
        echo '</div>'; // card
    }

    // ───────────────── AJAX HANDLERS ─────────────────

    public static function ajax_vehicle_set_draft() {
        check_ajax_referer('sgu_dashboard_nonce', 'nonce');
        if (!current_user_can('edit_posts')) wp_send_json_error('Sin permisos');

        $post_id = absint($_POST['post_id'] ?? 0);
        if (!$post_id) wp_send_json_error('ID inválido');

        wp_update_post(['ID' => $post_id, 'post_status' => 'draft']);
        wp_send_json_success(['message' => 'Vehículo pasado a borrador']);
    }

    public static function ajax_dismiss_entry() {
        check_ajax_referer('sgu_dashboard_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Sin permisos');

        $entry_id = absint($_POST['entry_id'] ?? 0);
        if (!$entry_id) wp_send_json_error('ID inválido');

        $dismissed = get_option('sgu_dismissed_entries', []);
        $dismissed[] = $entry_id;
        $dismissed = array_unique(array_slice($dismissed, -200)); // Keep last 200
        update_option('sgu_dismissed_entries', $dismissed);

        wp_send_json_success();
    }

    public static function ajax_mark_spam() {
        check_ajax_referer('sgu_dashboard_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Sin permisos');

        $entry_id = absint($_POST['entry_id'] ?? 0);
        if (!$entry_id) wp_send_json_error('ID inválido');

        $spam_list = get_option('sgu_spam_entries', []);
        $spam_list[] = $entry_id;
        $spam_list = array_unique(array_slice($spam_list, -500));
        update_option('sgu_spam_entries', $spam_list);

        // Also mark as spam in GF if possible
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'gf_entry', ['status' => 'spam'], ['id' => $entry_id], ['%s'], ['%d']);

        wp_send_json_success();
    }

    public static function ajax_toggle_replied() {
        check_ajax_referer('sgu_dashboard_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Sin permisos');

        $entry_id = absint($_POST['entry_id'] ?? 0);
        if (!$entry_id) wp_send_json_error('ID inválido');

        $replied = get_option('sgu_replied_entries', []);
        $key = array_search($entry_id, $replied);
        if ($key !== false) {
            unset($replied[$key]);
            $replied = array_values($replied);
            $is_replied = false;
        } else {
            $replied[] = $entry_id;
            $replied = array_unique(array_slice($replied, -500));
            $is_replied = true;
        }
        update_option('sgu_replied_entries', $replied);

        wp_send_json_success(['replied' => $is_replied]);
    }

    public static function ajax_get_vehicles() {
        check_ajax_referer('sgu_dashboard_nonce', 'nonce');
        if (!current_user_can('edit_posts')) wp_send_json_error('Sin permisos');

        $page = max(1, absint($_POST['page'] ?? 1));
        $per_page = 15;
        $search = sanitize_text_field($_POST['search'] ?? '');
        $type = sanitize_key($_POST['type'] ?? '');
        $status = sanitize_key($_POST['status'] ?? '');
        $sold_filter = sanitize_key($_POST['sold'] ?? '');

        $args = [
            'post_type'      => $type ?: ['post', 'defenders'],
            'post_status'    => $status ?: ['publish', 'draft'],
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        if ($search) $args['s'] = $search;

        if ($sold_filter) {
            if ($sold_filter === 'v1') {
                $args['meta_query'] = [['key' => 'sold', 'value' => 'v1', 'compare' => '=']];
            } else {
                $args['meta_query'] = [
                    'relation' => 'OR',
                    ['key' => 'sold', 'compare' => 'NOT EXISTS'],
                    ['key' => 'sold', 'value' => 'v1', 'compare' => '!='],
                    ['key' => 'sold', 'value' => '', 'compare' => '='],
                ];
            }
        }

        $query = new WP_Query($args);
        $vehicles = [];

        foreach ($query->posts as $p) {
            $thumb = get_the_post_thumbnail_url($p->ID, 'thumbnail');
            $info = get_field('informations', $p->ID);
            $price = get_field('price', $p->ID);
            $sold = get_field('sold', $p->ID);
            $no_res = get_field('no_residentes', $p->ID);

            $vehicles[] = [
                'id'       => $p->ID,
                'title'    => $p->post_title,
                'thumb'    => $thumb ?: '',
                'year'     => $info['year'] ?? '',
                'type'     => $p->post_type === 'defenders' ? 'Defender' : 'Auto',
                'type_raw' => $p->post_type,
                'price'    => $price ? '$' . number_format((float)$price, 0, ',', '.') : '-',
                'status'   => $p->post_status,
                'sold'     => $sold === 'v1',
                'no_residentes' => (bool)$no_res,
                'edit_url' => get_edit_post_link($p->ID, 'raw'),
                'view_url' => get_permalink($p->ID),
            ];
        }

        wp_send_json_success([
            'vehicles'    => $vehicles,
            'total'       => $query->found_posts,
            'total_pages' => $query->max_num_pages,
            'page'        => $page,
        ]);
    }
}
