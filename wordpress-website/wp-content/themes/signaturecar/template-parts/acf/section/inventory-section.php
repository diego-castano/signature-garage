<!-- Content Start -->
<section class="content-area bg_dark">
    <div class="container">
        <?php
            $query = new WP_Query( array( 'post_type' => 'post', 'meta_key' => 'sold', 'meta_value' => 'v2','order'   => 'DESC', 'showposts' => get_sub_field('number_of_items') ) );
            if ($query -> have_posts() ) :
            ?>
		<div class="d-none d-lg-block">
			<div class="row ">
            <?php while ( $query ->  have_posts() ) :  $query -> the_post(); ?>
            <div class="col-md-3 col-sm-6"  id="post-<?php the_ID(); ?>">
            <div class="box box-style-1">
                    <span class="title text-center d-block font_gillsans"><?php the_title(); ?></span>
<!--                     <a href="<?php the_permalink(); ?>"><?php echo get_the_post_thumbnail( $post->ID, 'full' ); ?></a> -->
				<?php
if ( has_post_thumbnail() ) {
    $thumbnail_url = get_the_post_thumbnail_url( $post->ID, 'full' );
?>
    <a href="<?php the_permalink(); ?>" class="post-thumbnail-link" style="background-image: url('<?php echo esc_url( $thumbnail_url ); ?>');">
        <!-- Optional content inside the link -->
    </a>
<?php
}
?>
                    
                    <div class="details">
                        <?php
                        $informations = get_field('informations');
                        if( $informations ): ?>
                            <div class="box-meta d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="year font_oswald">
                                        <span class="d-block">AÑO</span><?php echo $informations['year'] ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="year font_oswald">
                                        <span class="d-block">kilómetro</span><?php echo $informations['kilometers'] ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="box-btns font_oswald d-flex align-items-center justify-content-between">
                            <span class="price"><?php the_field('price') ?></span>
                            <a href="<?php the_permalink(); ?>" class="cta-btn btn_red">Ver detalles</a>
                        </div>
                       <div class="box-bottom-meta  d-flex align-items-center justify-content-center">
                                        <a target="_blank" href="https://wa.me/598094300100?text=Quiero%20m%C3%A1s%20informaci%C3%B3n%20sobre%20%C3%A9ste%20veh%C3%ADculo%20de%20Signature%20Garage%3A<?php the_permalink(); ?>" class="save_action d-flex align-items-center"><em class="fab fa-whatsapp"></em>Enviar a WhatsApp</a>
                                    </div>
                        
                    </div>
                </div>
            </div>    
            <?php endwhile; ?>
            </div>
		</div>
		
            <div class="box-slider  d-lg-none">
            <?php while ( $query ->  have_posts() ) :  $query -> the_post(); ?>
            <div class="slide"  id="post-<?php the_ID(); ?>">
            <div class="box box-style-1">
                    <span class="title text-center d-block font_gillsans"><?php the_title(); ?></span>
<!--                     <a href="<?php the_permalink(); ?>"><?php echo get_the_post_thumbnail( $post->ID, 'full' ); ?></a> -->
                    <?php
if ( has_post_thumbnail() ) {
    $thumbnail_url = get_the_post_thumbnail_url( $post->ID, 'full' );
?>
    <a href="<?php the_permalink(); ?>" class="post-thumbnail-link" style="background-image: url('<?php echo esc_url( $thumbnail_url ); ?>');">
        <!-- Optional content inside the link -->
    </a>
<?php
}
?>
                    <div class="details">
                        <?php
                        $informations = get_field('informations');
                        if( $informations ): ?>
                            <div class="box-meta d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="year font_oswald">
                                        <span class="d-block">AÑO</span><?php echo $informations['year'] ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="year font_oswald">
                                        <span class="d-block">kilómetro</span><?php echo $informations['kilometers'] ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="box-btns font_oswald d-flex align-items-center justify-content-between">
                            <span class="price"><?php the_field('price') ?></span>
                            <a href="<?php the_permalink(); ?>" class="cta-btn btn_red">Ver detalles</a>
                        </div>
                       <div class="box-bottom-meta  d-flex align-items-center justify-content-center">
                                        <a target="_blank" href="https://wa.me/598094300100?text=Quiero%20m%C3%A1s%20informaci%C3%B3n%20sobre%20%C3%A9ste%20veh%C3%ADculo%20de%20Signature%20Garage%3A<?php the_permalink(); ?>" class="save_action d-flex align-items-center"><em class="fab fa-whatsapp"></em>Enviar a WhatsApp</a>
                                    </div>
                        
                    </div>
                </div>
            </div>    
            <?php endwhile; ?>
            </div>

            <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        
            
        <div class="text-center">
        <?php 
        //Button
        $button = get_sub_field( 'button' ); 
        if( isset( $button ) && !empty( $button ) ):
            $target = ( isset( $button['target'] ) && !empty( $button['target'] ) )? 'target="'. $button['target'] .'"' : '';
            $url = ( isset( $button['url'] ) && !empty( $button['url'] ) )? 'href="'. $button['url'] .'"' : '';
            $title = ( isset( $button['title'] ) && !empty( $button['title'] ) )? $button['title'] : '';
            
            printf( '<a %s %s class="cta-btn btn_red ">%s</a>', $url, $target, $title  );
        
        endif;
        ?>
        </div>
    </div>
</section>
<!-- Content End -->



            