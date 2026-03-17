<?php if( 'v1' == get_sub_field( 'status' ) ): ?>
<style>
/* ── Sort/Filter Toolbar ── */
.sgu-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    margin-bottom: 30px;
    padding: 16px 20px;
    background: rgba(0,0,0,0.35);
    backdrop-filter: blur(8px);
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.08);
}
.sgu-toolbar-group {
    display: flex;
    align-items: center;
    gap: 8px;
}
.sgu-toolbar-label {
    font-family: "Oswald", sans-serif;
    font-size: 11px;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.5);
    white-space: nowrap;
}
.sgu-select {
    appearance: none;
    -webkit-appearance: none;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 6px;
    color: #fff;
    font-family: "Oswald", sans-serif;
    font-size: 13px;
    letter-spacing: 1px;
    padding: 8px 32px 8px 12px;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    min-width: 140px;
}
.sgu-select:hover, .sgu-select:focus {
    border-color: #ac1d28;
    background-color: rgba(255,255,255,0.12);
    outline: none;
}
.sgu-select option {
    background: #1a1a1a;
    color: #fff;
}
.sgu-search-wrap {
    position: relative;
    flex: 1;
    min-width: 180px;
}
.sgu-search {
    width: 100%;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 6px;
    color: #fff;
    font-family: "Oswald", sans-serif;
    font-size: 13px;
    letter-spacing: 1px;
    padding: 8px 12px 8px 36px;
    transition: border-color 0.2s, background 0.2s;
}
.sgu-search:hover, .sgu-search:focus {
    border-color: #ac1d28;
    background-color: rgba(255,255,255,0.12);
    outline: none;
}
.sgu-search::placeholder {
    color: rgba(255,255,255,0.4);
}
.sgu-search-icon {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.4);
    pointer-events: none;
    font-size: 14px;
}
.sgu-toolbar-right {
    margin-left: auto;
    display: flex;
    gap: 10px;
    align-items: center;
}
.sgu-count {
    font-family: "Oswald", sans-serif;
    font-size: 12px;
    letter-spacing: 1px;
    color: rgba(255,255,255,0.45);
    white-space: nowrap;
}
.sgu-btn-sold {
    font-family: "Oswald", sans-serif;
    font-size: 12px;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 8px 16px;
    border-radius: 6px;
    border: 1px solid rgba(255,255,255,0.15);
    background: transparent;
    color: #fff;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}
.sgu-btn-sold:hover {
    border-color: #ac1d28;
    background: rgba(172,29,40,0.15);
}
.sgu-btn-sold.active {
    background: #ac1d28;
    border-color: #ac1d28;
}
.sgu-clear-filters {
    font-family: "Oswald", sans-serif;
    font-size: 11px;
    letter-spacing: 1px;
    color: rgba(255,255,255,0.4);
    background: none;
    border: none;
    cursor: pointer;
    text-decoration: underline;
    padding: 4px;
    display: none;
}
.sgu-clear-filters.visible {
    display: inline-block;
}
.sgu-clear-filters:hover {
    color: #fff;
}
.no_cars {
    text-align: center;
    color: #fff;
    font-family: "Oswald", sans-serif;
    font-size: 16px;
    letter-spacing: 1px;
    padding: 60px 20px;
    display: none;
}
.no_cars.visible {
    display: block;
}

/* ── Responsive ── */
@media (max-width: 991px) {
    .sgu-toolbar {
        padding: 14px 16px;
    }
    .sgu-toolbar-right {
        margin-left: 0;
        width: 100%;
        justify-content: space-between;
    }
    .sgu-search-wrap {
        width: 100%;
        flex: unset;
    }
}
@media (max-width: 767px) {
    .sgu-toolbar {
        flex-direction: column;
        gap: 12px;
        padding: 16px;
        border-radius: 8px;
    }
    .sgu-toolbar-group {
        width: 100%;
    }
    .sgu-select {
        flex: 1;
        min-width: unset;
    }
    .sgu-search-wrap {
        min-width: unset;
    }
    .sgu-toolbar-right {
        flex-wrap: wrap;
        gap: 8px;
    }
}
</style>

<section class="content-area pt_200">
    <div class="container">
        <div class="breadcrumb-navigation mt-0 mb-4">
            <?php
            if ( function_exists('yoast_breadcrumb') ) {
                yoast_breadcrumb( '<div id="breadcrumbs">','</div>' );
            }
            ?>
        </div>
    </div>

    <div class="container">
        <!-- Sort/Filter Toolbar -->
        <div class="sgu-toolbar">
            <div class="sgu-toolbar-group">
                <span class="sgu-toolbar-label">Ordenar</span>
                <select class="sgu-select" id="sguSort">
                    <option value="make-asc">Marca A-Z</option>
                    <option value="make-desc">Marca Z-A</option>
                    <option value="price-asc">Precio: Menor a Mayor</option>
                    <option value="price-desc">Precio: Mayor a Menor</option>
                    <option value="year-desc">Año: Más Nuevo</option>
                    <option value="year-asc">Año: Más Antiguo</option>
                </select>
            </div>

            <div class="sgu-toolbar-group">
                <span class="sgu-toolbar-label">Marca</span>
                <select class="sgu-select" id="sguFilterMake">
                    <option value="all">Todas</option>
                    <?php
                    $args = array(
                        'post_type'      => 'post',
                        'posts_per_page' => -1,
                        'meta_key'       => 'sold',
                        'meta_value'     => 'v2',
                    );
                    $the_query = new WP_Query($args);
                    $unique_makes = array();
                    $unique_years = array();
                    if ($the_query->have_posts()) :
                        while ($the_query->have_posts()) : $the_query->the_post();
                            $info = get_field('informations');
                            if ($info && !empty($info['make']) && !in_array($info['make'], $unique_makes)) {
                                $unique_makes[] = $info['make'];
                            }
                            if ($info && !empty($info['year']) && !in_array($info['year'], $unique_years)) {
                                $unique_years[] = $info['year'];
                            }
                        endwhile;
                    endif;
                    sort($unique_makes);
                    rsort($unique_years);
                    foreach ($unique_makes as $make) :
                    ?>
                    <option value="<?php echo esc_attr($make); ?>"><?php echo esc_html($make); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="sgu-toolbar-group">
                <span class="sgu-toolbar-label">Año</span>
                <select class="sgu-select" id="sguFilterYear">
                    <option value="all">Todos</option>
                    <?php foreach ($unique_years as $year) : ?>
                    <option value="<?php echo esc_attr($year); ?>"><?php echo esc_html($year); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="sgu-search-wrap">
                <span class="sgu-search-icon"><i class="fas fa-search"></i></span>
                <input type="text" class="sgu-search" id="sguSearch" placeholder="Buscar vehiculo..." />
            </div>

            <div class="sgu-toolbar-right">
                <button class="sgu-btn-sold" id="sguSoldToggle">VER VENDIDOS</button>
                <button class="sgu-clear-filters" id="sguClearAll">Limpiar filtros</button>
                <span class="sgu-count" id="sguCount"></span>
            </div>
        </div>

        <div class="no_cars" id="sguNoResults">No se encontraron vehiculos con los filtros seleccionados.</div>

        <div class="row gamesContainer" id="sguGrid">
            <?php
            // Helper to parse price string to numeric value
            function sgu_parse_price($price_str) {
                if (empty($price_str)) return 0;
                // Remove currency symbols and spaces, keep only digits
                // UY format uses dots as thousands separator (e.g. US$ 86.990)
                $clean = preg_replace('/[^0-9]/', '', $price_str);
                return intval($clean);
            }

            // Query all vehicles (not sold), sorted by make ASC
            $vehicles = new WP_Query(array(
                'post_type'      => 'post',
                'posts_per_page' => -1,
                'meta_key'       => 'informations_make',
                'orderby'        => 'meta_value',
                'order'          => 'ASC',
            ));

            if ($vehicles->have_posts()) :
                while ($vehicles->have_posts()) : $vehicles->the_post();
                    $informations = get_field('informations');
                    $price_raw = get_field('price');
                    $price_num = sgu_parse_price($price_raw);
                    $sold_status = get_field('sold');
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('col-md-4 col-sm-6 ssCar'); ?>
                        data-year="<?php echo esc_attr($informations['year'] ?? ''); ?>"
                        data-make="<?php echo esc_attr($informations['make'] ?? ''); ?>"
                        data-model="<?php echo esc_attr($informations['model'] ?? ''); ?>"
                        data-price="<?php echo esc_attr($price_num); ?>"
                        data-sold="<?php echo esc_attr($sold_status); ?>"
                        data-title="<?php echo esc_attr(get_the_title()); ?>">
                        <div class="box box-style-1">
                            <span class="title text-center d-block font_gillsans"><?php the_title(); ?></span>

                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>" class="post-thumbnail-link alt" style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( $post->ID, 'full' ) ); ?>');">
                                    <?php if ( get_field('no_residentes') ) : ?>
                                        <span class="sgu-nores-badge">&#x1F1FA;&#x1F1F8; NO RESIDENTES</span>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>

                            <div class="details">
                                <?php if( $informations ) : ?>
                                    <div class="box-meta d-flex align-items-center justify-content-between">
                                        <div><div class="year font_oswald"><span class="d-block">AÑO</span><?php echo esc_html( $informations['year'] ); ?></div></div>
                                        <div><div class="year font_oswald"><span class="d-block">kilómetros</span><?php echo esc_html( $informations['kilometers'] ); ?></div></div>
                                    </div>
                                <?php endif; ?>

                                <div class="box-btns font_oswald d-flex align-items-center justify-content-between">
                                    <?php if( 'v1' == $sold_status ) : ?>
                                        <span class="price">VENDIDO</span>
                                    <?php else : ?>
                                        <span class="price"><?php echo esc_html($price_raw); ?></span>
                                    <?php endif; ?>
                                    <a href="<?php the_permalink(); ?>" class="cta-btn btn_red">Ver detalles</a>
                                </div>

                                <div class="box-bottom-meta d-flex align-items-center justify-content-center">
                                    <a target="_blank" href="https://wa.me/598094300100?text=Quiero%20m%C3%A1s%20informaci%C3%B3n%20sobre%20%C3%A9ste%20veh%C3%ADculo%20de%20Signature%20Garage%3A<?php the_permalink(); ?>" class="save_action d-flex align-items-center"><em class="fab fa-whatsapp"></em>Enviar a WhatsApp</a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>

<script>
(function ($) {
    var $grid = $('#sguGrid');
    var $cards = $grid.children('.ssCar');
    var showSold = false;
    var searchTerm = '';

    function parsePrice(el) {
        return parseFloat($(el).data('price')) || 0;
    }

    function sortCards() {
        var sortVal = $('#sguSort').val();
        var parts = sortVal.split('-');
        var field = parts[0];
        var dir = parts[1];

        $cards.sort(function (a, b) {
            var valA, valB;
            if (field === 'price') {
                valA = parsePrice(a);
                valB = parsePrice(b);
            } else if (field === 'year') {
                valA = parseInt($(a).data('year')) || 0;
                valB = parseInt($(b).data('year')) || 0;
            } else {
                valA = ($(a).data('make') || '').toString().toLowerCase();
                valB = ($(b).data('make') || '').toString().toLowerCase();
                if (dir === 'asc') return valA.localeCompare(valB, 'es');
                return valB.localeCompare(valA, 'es');
            }
            return dir === 'asc' ? valA - valB : valB - valA;
        });

        $grid.append($cards);
    }

    function filterCards() {
        var filterMake = $('#sguFilterMake').val();
        var filterYear = $('#sguFilterYear').val();
        var term = searchTerm.toLowerCase();
        var visible = 0;

        $cards.each(function () {
            var $el = $(this);
            var show = true;

            // Sold filter
            var isSold = $el.data('sold') === 'v1';
            if (!showSold && isSold) show = false;
            if (showSold && !isSold) show = false;

            // Make filter
            if (show && filterMake !== 'all') {
                if ($el.data('make') !== filterMake) show = false;
            }

            // Year filter
            if (show && filterYear !== 'all') {
                if (String($el.data('year')) !== filterYear) show = false;
            }

            // Search
            if (show && term) {
                var text = ($el.data('title') + ' ' + $el.data('make') + ' ' + $el.data('model') + ' ' + $el.data('year')).toLowerCase();
                if (text.indexOf(term) === -1) show = false;
            }

            $el.toggle(show);
            if (show) visible++;
        });

        $('#sguCount').text(visible + ' vehiculo' + (visible !== 1 ? 's' : ''));
        $('#sguNoResults').toggleClass('visible', visible === 0);

        // Show clear button if any filter is active
        var hasFilter = filterMake !== 'all' || filterYear !== 'all' || term || showSold;
        $('#sguClearAll').toggleClass('visible', hasFilter);
    }

    // Sort change
    $('#sguSort').on('change', function () {
        sortCards();
    });

    // Filter changes
    $('#sguFilterMake, #sguFilterYear').on('change', function () {
        filterCards();
    });

    // Search
    var searchTimer;
    $('#sguSearch').on('input', function () {
        clearTimeout(searchTimer);
        var val = $(this).val();
        searchTimer = setTimeout(function () {
            searchTerm = val;
            filterCards();
        }, 200);
    });

    // Sold toggle
    $('#sguSoldToggle').on('click', function () {
        showSold = !showSold;
        $(this).text(showSold ? 'VER DISPONIBLES' : 'VER VENDIDOS');
        $(this).toggleClass('active', showSold);
        filterCards();
    });

    // Clear all
    $('#sguClearAll').on('click', function () {
        $('#sguFilterMake').val('all');
        $('#sguFilterYear').val('all');
        $('#sguSearch').val('');
        $('#sguSort').val('make-asc');
        searchTerm = '';
        showSold = false;
        $('#sguSoldToggle').text('VER VENDIDOS').removeClass('active');
        sortCards();
        filterCards();
    });

    // Initial sort and count
    sortCards();
    filterCards();

})(jQuery);
</script>
<?php endif; ?>
