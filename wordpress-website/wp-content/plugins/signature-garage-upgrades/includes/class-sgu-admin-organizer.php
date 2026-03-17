<?php
if (!defined('ABSPATH')) exit;

class SGU_Admin_Organizer {

    private static $sections = [
        'vehiculos' => [
            'label' => 'Vehículos',
            'icon'  => 'dashicons-car',
            'items' => [
                'menu-posts',           // Autos
                'menu-posts-defenders', // Defenders
            ],
        ],
        'contenido' => [
            'label' => 'Contenido',
            'icon'  => 'dashicons-edit-page',
            'items' => [
                'menu-pages',    // Páginas
                'menu-media',    // Multimedia
                'menu-comments', // Comentarios
            ],
        ],
        'marketing' => [
            'label' => 'Marketing',
            'icon'  => 'dashicons-megaphone',
            'items' => [
                'toplevel_page_gf_edit_forms',                           // Forms
                'toplevel_page_wpseo_dashboard',                         // Yoast SEO
                'toplevel_page_subscriber',                              // Subscriber
                'toplevel_page_sb-instagram-feed',                       // Instagram Feed
                'toplevel_page_wp-reviews-plugin-for-google-settings',   // Trustindex
            ],
        ],
        'configuracion' => [
            'label' => 'Configuración',
            'icon'  => 'dashicons-admin-settings',
            'items' => [
                'toplevel_page_signaturecar-general-option',             // Theme Options
                'toplevel_page_edit-post_type-acf-field-group',          // ACF
                'menu-appearance',                                       // Apariencia
                'menu-settings',                                         // Ajustes
            ],
        ],
        'administracion' => [
            'label' => 'Administración',
            'icon'  => 'dashicons-admin-tools',
            'items' => [
                'menu-plugins',                        // Plugins
                'menu-users',                          // Usuarios
                'toplevel_page_really-simple-security', // Seguridad
                'toplevel_page_file_manager_advanced_ui', // File Manager
                'toplevel_page_ai1wm_export',          // WP Migration
                'menu-tools',                          // Herramientas
                'toplevel_page_sgu-dashboard',         // SG Upgrades
            ],
        ],
    ];

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'reorganize_menu'], 9999);
        add_action('admin_menu', [__CLASS__, 'add_slider_submenu']);
        add_action('admin_head', [__CLASS__, 'inject_styles']);
        add_action('admin_footer', [__CLASS__, 'inject_scripts']);
        add_action('admin_head', [__CLASS__, 'replace_admin_logo']);
        add_action('admin_menu', [__CLASS__, 'rename_dashboard'], 9999);
    }

    public static function add_slider_submenu() {
        // Add "Slider Manager" link under Autos (Posts)
        add_submenu_page(
            'edit.php',
            'Slider Manager',
            'Slider del Hero',
            'manage_options',
            'admin.php?page=sgu-dashboard&tab=slider-manager'
        );
    }

    public static function rename_dashboard() {
        global $menu;
        foreach ($menu as &$item) {
            if (isset($item[2]) && $item[2] === 'index.php') {
                $item[0] = 'Inicio';
                break;
            }
        }
    }

    public static function replace_admin_logo() {
        $logo_url = get_template_directory_uri() . '/assets/img/logo.png';
        ?>
        <style id="sgu-admin-logo">
            #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
                content: '' !important;
                background: url('<?php echo esc_url($logo_url); ?>') no-repeat center center;
                background-size: contain;
                width: 20px;
                height: 20px;
                display: inline-block;
            }
        </style>
        <?php
    }

    public static function reorganize_menu() {
        global $menu;

        // Collect all menu items by their hookname/id mapping
        // We'll use CSS/JS to reorganize visually since WP menu manipulation is fragile
        // The PHP side just ensures separators and ordering
    }

    public static function inject_styles() {
        ?>
        <style id="sgu-admin-organizer">
            /* Section headers */
            #adminmenu .sgu-section-header {
                display: flex;
                align-items: center;
                padding: 8px 12px 6px;
                margin: 0;
                cursor: pointer;
                user-select: none;
                transition: background 0.15s;
            }
            #adminmenu .sgu-section-header:hover {
                background: rgba(255,255,255,0.06);
            }
            #adminmenu .sgu-section-header .sgu-section-label {
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.8px;
                color: #ffffff;
                flex: 1;
            }
            #adminmenu .sgu-section-header .sgu-section-icon {
                font-size: 14px;
                width: 20px;
                margin-right: 6px;
            }
            #adminmenu .sgu-section-header[data-section="vehiculos"] .sgu-section-icon { color: #4fc3f7; }
            #adminmenu .sgu-section-header[data-section="contenido"] .sgu-section-icon { color: #81c784; }
            #adminmenu .sgu-section-header[data-section="marketing"] .sgu-section-icon { color: #ffb74d; }
            #adminmenu .sgu-section-header[data-section="configuracion"] .sgu-section-icon { color: #ba68c8; }
            #adminmenu .sgu-section-header[data-section="administracion"] .sgu-section-icon { color: #e57373; }
            #adminmenu .sgu-section-header .sgu-section-toggle {
                font-size: 12px;
                color: rgba(255,255,255,0.6);
                transition: transform 0.2s;
            }
            #adminmenu .sgu-section-header.collapsed .sgu-section-toggle {
                transform: rotate(-90deg);
            }
            #adminmenu .sgu-section-group.collapsed {
                display: none;
            }

            /* Active section: left accent bar + subtle background */
            #adminmenu .sgu-section-header:not(.collapsed) {
                background: rgba(255,255,255,0.04);
                border-left: 3px solid transparent;
                padding-left: 9px;
            }
            #adminmenu .sgu-section-header.collapsed {
                border-left: 3px solid transparent;
                padding-left: 9px;
            }
            #adminmenu .sgu-section-header[data-section="vehiculos"]:not(.collapsed) { border-left-color: #4fc3f7; }
            #adminmenu .sgu-section-header[data-section="contenido"]:not(.collapsed) { border-left-color: #81c784; }
            #adminmenu .sgu-section-header[data-section="marketing"]:not(.collapsed) { border-left-color: #ffb74d; }
            #adminmenu .sgu-section-header[data-section="configuracion"]:not(.collapsed) { border-left-color: #ba68c8; }
            #adminmenu .sgu-section-header[data-section="administracion"]:not(.collapsed) { border-left-color: #e57373; }

            /* Items inside open section get subtle left border too */
            #adminmenu li[data-sgu-section]:not([style*="display: none"]) {
                border-left: 3px solid rgba(255,255,255,0.06);
                padding-left: 0;
            }

            /* Hide default WP separators — we use our own */
            #adminmenu .wp-menu-separator {
                display: none !important;
            }

            /* Brand colors: dark background */
            #adminmenu, #adminmenuback, #adminmenuwrap {
                background: #111111;
            }
            #adminmenu a {
                color: rgba(255,255,255,0.75);
            }
            #adminmenu a:hover,
            #adminmenu li.menu-top:hover,
            #adminmenu li.opensub > a.menu-top,
            #adminmenu li > a.menu-top:focus {
                color: #ffffff;
                background: rgba(255,255,255,0.08);
            }
            #adminmenu li.current a.menu-top,
            #adminmenu li.wp-has-current-submenu a.menu-top,
            #adminmenu li.wp-has-current-submenu .wp-submenu,
            .folded #adminmenu li.current.menu-top,
            .folded #adminmenu li.wp-has-current-submenu {
                background: #1a1a1a;
                color: #ffffff;
            }
            #adminmenu .wp-submenu {
                background: #1a1a1a;
            }
            #adminmenu .wp-submenu a {
                color: rgba(255,255,255,0.6);
            }
            #adminmenu .wp-submenu a:hover,
            #adminmenu .wp-submenu a:focus {
                color: #ffffff;
            }
            #adminmenu .wp-submenu li.current a {
                color: #ffffff;
            }

            /* Dashicons in sidebar */
            #adminmenu div.wp-menu-image:before {
                color: rgba(255,255,255,0.75);
            }
            #adminmenu li.current div.wp-menu-image:before,
            #adminmenu li.wp-has-current-submenu div.wp-menu-image:before,
            #adminmenu li:hover div.wp-menu-image:before {
                color: #ffffff;
            }

            /* Admin bar brand */
            #wpadminbar {
                background: #111111;
            }
            #wpadminbar .quicklinks a,
            #wpadminbar .quicklinks a span {
                color: rgba(255,255,255,0.75);
            }
            #wpadminbar .quicklinks a:hover,
            #wpadminbar .quicklinks a:hover span {
                color: #ffffff;
                background: rgba(255,255,255,0.08);
            }

            /* Collapse button */
            #collapse-menu {
                border-top: 1px solid rgba(255,255,255,0.06);
            }
            #collapse-button {
                color: rgba(255,255,255,0.4);
            }
            #collapse-button:hover {
                color: #ffffff;
            }

            /* Updates badge */
            #adminmenu .update-plugins .plugin-count,
            #adminmenu .awaiting-mod {
                background: #d63638;
                color: #fff;
            }
        </style>
        <?php
    }

    public static function inject_scripts() {
        $sections = self::$sections;
        $sections_json = wp_json_encode($sections);
        ?>
        <script id="sgu-admin-organizer-js">
        (function() {
            'use strict';

            var sections = <?php echo $sections_json; ?>;
            var menu = document.getElementById('adminmenu');
            if (!menu) return;

            // Build a map of menu item ID -> li element
            var itemMap = {};
            var allItems = menu.querySelectorAll(':scope > li');
            allItems.forEach(function(li) {
                if (li.id) itemMap[li.id] = li;
            });

            // Detect which section has the current page
            var currentSection = '';
            Object.keys(sections).forEach(function(key) {
                sections[key].items.forEach(function(itemId) {
                    var li = itemMap[itemId];
                    if (li && (li.classList.contains('wp-has-current-submenu') ||
                               li.classList.contains('current') ||
                               li.classList.contains('wp-menu-open'))) {
                        currentSection = key;
                    }
                });
            });

            // Load saved collapsed state
            var stored = {};
            try { stored = JSON.parse(localStorage.getItem('sgu_menu_state') || '{}'); } catch(e) {}

            // Remove all items from menu first (except dashboard and collapse)
            var dashboard = itemMap['menu-dashboard'];
            var collapseBtn = document.getElementById('collapse-menu');
            var fragment = document.createDocumentFragment();

            // Keep dashboard at top
            if (dashboard) fragment.appendChild(dashboard);

            // Build sections
            Object.keys(sections).forEach(function(key) {
                var sec = sections[key];
                var isCollapsed = stored[key] !== undefined ? stored[key] : (key !== currentSection && currentSection !== '');

                // Section header
                var header = document.createElement('li');
                header.className = 'sgu-section-header' + (isCollapsed ? ' collapsed' : '');
                header.setAttribute('data-section', key);
                header.innerHTML = '<span class="dashicons ' + sec.icon + ' sgu-section-icon"></span>' +
                    '<span class="sgu-section-label">' + sec.label + '</span>' +
                    '<span class="dashicons dashicons-arrow-down-alt2 sgu-section-toggle"></span>';
                fragment.appendChild(header);

                // Group wrapper (virtual - we just add items sequentially)
                var groupItems = [];
                sec.items.forEach(function(itemId) {
                    var li = itemMap[itemId];
                    if (li) {
                        li.setAttribute('data-sgu-section', key);
                        if (isCollapsed) li.style.display = 'none';
                        fragment.appendChild(li);
                        groupItems.push(li);
                        delete itemMap[itemId]; // mark as placed
                    }
                });

                // Click handler for collapse/expand
                header.addEventListener('click', function() {
                    var collapsed = header.classList.toggle('collapsed');
                    groupItems.forEach(function(li) {
                        li.style.display = collapsed ? 'none' : '';
                    });
                    // Save state
                    stored[key] = collapsed;
                    try { localStorage.setItem('sgu_menu_state', JSON.stringify(stored)); } catch(e) {}
                });
            });

            // Add any remaining items that weren't in sections
            Object.keys(itemMap).forEach(function(id) {
                var li = itemMap[id];
                if (li && id !== 'menu-dashboard' && id !== 'collapse-menu' &&
                    !li.classList.contains('wp-menu-separator')) {
                    fragment.appendChild(li);
                }
            });

            // Add collapse button at the end
            if (collapseBtn) fragment.appendChild(collapseBtn);

            // Clear menu and rebuild
            while (menu.firstChild) menu.removeChild(menu.firstChild);
            menu.appendChild(fragment);
        })();
        </script>
        <?php
    }
}
