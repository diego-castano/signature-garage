<?php if( 'v1' == get_sub_field( 'status' ) ): ?>
<style>
input#searchBox {
    background: transparent;
    border: none;
    border-radius: 0;
    border-bottom: 1px solid #fff;
    padding: 10px;
    color: #fff;
	width:100%
}
	input#searchBox:focus{
		outline:none
	}
	.no_cars{
		text-align:center;
		color:#fff
	}
	@media(max-width:767px){
		input#searchBox{
			background: #fff;
    		margin: 30px 0;
			color:#000
		}
		.filter-options .container{
			padding:0
		}

	}
	
</style>
<section class="content-area pt_200">
    <div class="container">
        <!-- Breadcrumb Nav Start -->
        <div class="breadcrumb-navigation mt-0 mb-4">
            <?php
            if ( function_exists('yoast_breadcrumb') ) {
                yoast_breadcrumb( '<div id="breadcrumbs">','</div>' );
            }
            ?>			
        </div>
        <!-- Breadcrumb Nav End -->			
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="filter-options mb-md-5">
                    <div class="container">
                        <div class="filter-inn footer_acc acc">
                            <span class="show_filter d-none">MOSTRAR FILTRO</span>
                            <ul>
                                <li>
                                    <span id="link_min_year" class="in_filter">Año Inicial</span>
                                    <div class="filter-inner link_min_year">
                                        <ul class="filter-year-min">
                                            <li data-year="all"><a>Todos</a></li>
                                            <?php
                                            $args = array(
                                                'post_type'     => 'post',
                                                'order'         => 'ASC',
                                                'posts_per_page'=> '-1',
                                                'meta_key'      => 'sold',
                                                'meta_value'    => 'v2',
                                                'orderby'       => 'meta_value',
                                                'order'         => 'ASC'
                                            );
                                            $the_query = new WP_Query( $args );
                                            $unique_years = array();
                                            if( $the_query->have_posts() ) : 
                                                while ( $the_query->have_posts() ) : $the_query->the_post();
                                                    $city = get_field('informations');
                                                    if( ! in_array( $city['year'], $unique_years ) ) :
                                                        $unique_years[] = $city['year'];
                                                    endif;
                                                endwhile;
                                                sort($unique_years); // Sort years in ascending order
                                                foreach ( $unique_years as $year ) :
                                            ?>
                                            <li data-year="<?php echo $year; ?>"><a><?php echo $year; ?></a></li>
                                            <?php 
                                                endforeach; 
                                            endif; 
                                            ?>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                            <ul>
                                <li>
                                    <span id="link_max_year" class="in_filter">Año Final</span>
                                    <div class="filter-inner link_max_year">
                                        <ul class="filter-year-max">
                                            <li data-year="all"><a>Todos</a></li>
                                            <?php
                                            // Reuse the sorted $unique_years array for the max year filter
                                            foreach ( $unique_years as $year ) :
                                            ?>
                                            <li data-year="<?php echo $year; ?>"><a><?php echo $year; ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                            <ul>
                                <li>
                                    <span id="link_3" class="in_filter">MARCA</span>
                                    <div class="filter-inner link_3">
                                        <ul class="filter-make">
                                            <li data-make="all"><a>Todos</a></li>
                                            <?php
                                            $unique_makes = array();
                                            if( $the_query->have_posts() ) : 
                                                while ( $the_query->have_posts() ) : $the_query->the_post();
                                                    $city = get_field('informations');
                                                    if( ! in_array( $city['make'], $unique_makes ) ) :
                                                        $unique_makes[] = $city['make'];
                                            ?>
                                            <li data-make="<?php echo $city['make']; ?>"><a><?php echo $city['make']; ?></a></li>
                                            <?php
                                                    endif;
                                                endwhile; 
                                            endif; ?>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                            <ul class="d-none">
                                <li>
                                    <span id="link_2" class="in_filter">MODELO</span>
                                    <div class="filter-inner link_2">
                                        <ul class="filter-model">
                                            <li data-model="all"><a>Todos</a></li>
                                            <?php
                                            $unique_models = array();
                                            if( $the_query->have_posts() ) : 
                                                while ( $the_query->have_posts() ) : $the_query->the_post();
                                                    $city = get_field('informations');
                                                    if( ! in_array( $city['model'], $unique_models ) ) :
                                                        $unique_models[] = $city['model'];
                                            ?>
                                            <li data-model="<?php echo $city['model']; ?>"><a><?php echo $city['model']; ?></a></li>
                                            <?php
                                                    endif;
                                                endwhile; 
                                            endif; ?>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center soldfilter cta-btn btn_red d-block" data-sold="<?php the_field( 'sold' ); ?>"><a>VER VENDIDOS</a></div>
				 <div>
					 <input type="text" id="searchBox" placeholder="Buscar..."  />
				</div>
            </div>
        </div>
        <div class="row gamesContainer">
    <?php
    // Query to fetch the specific post you want to display first
    $specific_post_id = 2822; // Replace with your specific post ID

    $specific_post = new WP_Query( array(
        'post_type' => 'post',
        'p' => $specific_post_id
    ));

    if ( $specific_post->have_posts() ) :
        while ( $specific_post->have_posts() ) : $specific_post->the_post();
            $informations = get_field('informations');
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('col-md-4 col-sm-6 ssCar'); ?>
                data-year="<?php echo esc_attr($informations['year']); ?>"
                data-make="<?php echo esc_attr($informations['make']); ?>"
                data-model="<?php echo esc_attr($informations['model']); ?>"
                data-sold="<?php echo esc_attr(get_field('sold')); ?>">
                <div class="box box-style-1">
                    <span class="title text-center d-block font_gillsans"><?php the_title(); ?></span>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>" class="post-thumbnail-link alt" style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( $post->ID, 'full' ) ); ?>');"></a>
                    <?php endif; ?>

                    <div class="details">
                        <?php if( $informations ) : ?>
                            <div class="box-meta d-flex align-items-center justify-content-between">
                                <div><div class="year font_oswald"><span class="d-block">AÑO</span><?php echo esc_html( $informations['year'] ); ?></div></div>
                                <div><div class="year font_oswald"><span class="d-block">kilómetros</span><?php echo esc_html( $informations['kilometers'] ); ?></div></div>
                            </div>
                        <?php endif; ?>

                        <div class="box-btns font_oswald d-flex align-items-center justify-content-between">
                            <?php if( 'v1' == get_field( 'sold' ) ) : ?>
                                <span class="price">VENDIDO</span>
                            <?php else : ?>
                                <span class="price"><?php the_field( 'price' ); ?></span>
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

    // Query to fetch remaining posts in random order, excluding the specific post
    $random_posts = new WP_Query( array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'orderby' => 'rand',
        'post__not_in' => array($specific_post_id) // Exclude the specific post by its ID
    ));

    if ( $random_posts->have_posts() ) :
        while ( $random_posts->have_posts() ) : $random_posts->the_post();
            $informations = get_field('informations');
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('col-md-4 col-sm-6 ssCar'); ?>
                data-year="<?php echo esc_attr($informations['year']); ?>"
                data-make="<?php echo esc_attr($informations['make']); ?>"
                data-model="<?php echo esc_attr($informations['model']); ?>"
                data-sold="<?php echo esc_attr(get_field('sold')); ?>">
                <div class="box box-style-1">
                    <span class="title text-center d-block font_gillsans"><?php the_title(); ?></span>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>" class="post-thumbnail-link alt" style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( $post->ID, 'full' ) ); ?>');"></a>
                    <?php endif; ?>

                    <div class="details">
                        <?php if( $informations ) : ?>
                            <div class="box-meta d-flex align-items-center justify-content-between">
                                <div><div class="year font_oswald"><span class="d-block">AÑO</span><?php echo esc_html( $informations['year'] ); ?></div></div>
                                <div><div class="year font_oswald"><span class="d-block">kilómetros</span><?php echo esc_html( $informations['kilometers'] ); ?></div></div>
                            </div>
                        <?php endif; ?>

                        <div class="box-btns font_oswald d-flex align-items-center justify-content-between">
                            <?php if( 'v1' == get_field( 'sold' ) ) : ?>
                                <span class="price">VENDIDO</span>
                            <?php else : ?>
                                <span class="price"><?php the_field( 'price' ); ?></span>
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
    var minYear = null;
    var maxYear = null;
    var filterApplied = false;

    // Function to update filter text
    function updateInFilterText(filterType, text) {
        $(`#link_${filterType}`).text(text);
    }

    // Function to filter posts by year range
    function filterPostsByYearRange() {
        $('.gamesContainer .col-md-4').each(function () {
            var postYear = $(this).data('year');
            var postSold = $(this).data('sold');
            var isSold = postSold === 'v1';
            var isYearInRange = (
                (minYear === null || postYear >= minYear) &&
                (maxYear === null || postYear <= maxYear)
            );

            if (isYearInRange) {
                if (filterApplied) {
                    // If sold filter is active, only show sold posts in range
                    if (isSold) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                } else {
                    // If no sold filter, show all posts in range
                    $(this).show();
                }
            } else {
                $(this).hide(); // Hide posts out of year range
            }
        });
    }

    // Year range filter event handlers
    $('.filter-year-min li').on("click", function () {
        minYear = $(this).data("year") === "all" ? null : $(this).data("year");
        updateInFilterText("min_year", minYear ? `Año Mínimo: ${minYear}` : "Año Mínimo");
        filterPostsByYearRange();
    });

    $('.filter-year-max li').on("click", function () {
        maxYear = $(this).data("year") === "all" ? null : $(this).data("year");
        updateInFilterText("max_year", maxYear ? `Año Máximo: ${maxYear}` : "Año Máximo");
        filterPostsByYearRange();
    });

    // Sold filter toggle button
    $('.soldfilter').on('click', function () {
        filterApplied = !filterApplied; // Toggle filter state

        // Change button text
        $(this).find('a').text(filterApplied ? 'Borrar filtro' : 'VER VENDIDOS');
        
        // Apply the filter logic considering the year range and sold status
        filterPostsByYearRange();
    });
		
		
		$('#searchBox').on('input', function() {
        var searchTerm = $(this).val();
        
        $.ajax({
            url: "<?php echo admin_url('admin-ajax.php'); ?>",
            type: "POST",
            data: {
                action: "filter_posts",
                search_term: searchTerm
            },
            success: function(response) {
                $('.gamesContainer').html(response);
            }
        });
    });

})(jQuery);


</script>
<?php endif; ?>
